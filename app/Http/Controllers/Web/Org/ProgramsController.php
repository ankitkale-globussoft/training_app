<?php

namespace App\Http\Controllers\Web\Org;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\TrainingRequirement;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;
use Exception;

class ProgramsController extends Controller
{
    public function index(Request $request)
    {
        $orgId = Auth::guard('org_web')->user()->org_id;

        $query = Program::with([
            'programType',
            'trainers',
            'trainingRequirements' => function ($q) use ($orgId) {
                $q->where('org_id', $orgId);
            }
        ]);

        // Search filter
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Program type filter
        if ($request->filled('program_type')) {
            $query->where('program_type_id', $request->program_type);
        }

        // Duration filter
        if ($request->filled('duration')) {
            $query->where('duration', '<=', $request->duration);
        }

        // Cost filters
        if ($request->filled('min_cost')) {
            $query->where('cost', '>=', $request->min_cost);
        }

        if ($request->filled('max_cost')) {
            $query->where('cost', '<=', $request->max_cost);
        }

        $programs = $query->paginate(9)->withQueryString();
        $programTypes = \App\Models\ProgramType::all();

        return view('organisation.programs.index', compact('programs', 'programTypes'));
    }

    public function show($id)
    {
        $program = Program::with(['programType', 'trainers'])
            ->findOrFail($id);

        return response()->json($program);
    }

    public function requestProgram(Request $request)
    {
        $request->validate([
            'program_id' => 'required|integer',
            'mode' => 'required|in:online,offline',
            'number_of_students' => 'required|integer|min:1',
            'schedule_date' => 'required|date',
            'schedule_time' => 'required|string',
        ]);

        $orgId = Auth::guard('org_web')->user()->org_id;

        $alreadyRequested = TrainingRequirement::where('org_id', $orgId)
            ->where('program_id', $request->program_id)
            ->exists();

        if ($alreadyRequested) {
            return response()->json([
                'status' => false,
                'message' => 'You have already requested this program.'
            ], 422);
        }

        TrainingRequirement::create([
            'org_id' => $orgId,
            'program_id' => $request->program_id,
            'mode' => $request->mode,
            'number_of_students' => $request->number_of_students,
            'schedule_date' => $request->schedule_date,
            'schedule_time' => $request->schedule_time
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Program request submitted successfully!'
        ]);
    }

    public function show_requestedPrograms(Request $request)
    {
        $orgId = Auth::guard('org_web')->user()->org_id;

        $requirements = TrainingRequirement::with(['program.programType', 'booking'])
            ->where('org_id', $orgId)
            ->latest()
            ->paginate(9);

        return view('organisation.programs.requested', compact('requirements'));
    }

    public function cancelRequest($id)
    {
        $orgId = Auth::guard('org_web')->user()->org_id;

        $req = TrainingRequirement::where('requirement_id', $id)
            ->where('org_id', $orgId)
            ->firstOrFail();

        // Also delete any failed bookings associated with this requirement
        Booking::where('requirement_id', $id)
            ->where('payment_status', 'failed')
            ->delete();


        $req->delete();

        return response()->json([
            'status' => true,
            'message' => 'Program request cancelled successfully.'
        ]);
    }

    public function initiatePayment(Request $request)
    {
        $request->validate([
            'requirement_id' => 'required|exists:training_requirements,requirement_id'
        ]);

        $orgId = Auth::guard('org_web')->user()->org_id;

        // Get the requirement with program details
        $requirement = TrainingRequirement::with('program')
            ->where('requirement_id', $request->requirement_id)
            ->where('org_id', $orgId)
            ->firstOrFail();

        // Check if already successfully paid
        $paidBooking = Booking::where('requirement_id', $requirement->requirement_id)
            ->where('payment_status', 'completed')
            ->first();

        if ($paidBooking) {
            return response()->json([
                'status' => false,
                'message' => 'Payment already completed for this program.'
            ], 422);
        }

        try {
            // Delete any previous failed/pending bookings for retry
            Booking::where('requirement_id', $requirement->requirement_id)
                ->where('org_id', $orgId)
                ->whereIn('payment_status', ['failed', 'pending'])
                ->delete();

            // Initialize Razorpay API
            $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

            // Amount in paise (smallest currency unit)
            // Price is per student
            $totalAmount = $requirement->program->cost * $requirement->number_of_students;
            $amountInPaise = $totalAmount * 100;

            // Create Razorpay Order
            $orderData = [
                'receipt' => 'PROG_' . $requirement->requirement_id . '_' . time(),
                'amount' => $amountInPaise,
                'currency' => 'INR',
                'notes' => [
                    'requirement_id' => $requirement->requirement_id,
                    'org_id' => $orgId,
                    'program_id' => $requirement->program_id,
                    'program_title' => $requirement->program->title,
                    'students' => $requirement->number_of_students
                ]
            ];

            $razorpayOrder = $api->order->create($orderData);

            // Create new pending booking
            $booking = Booking::create([
                'requirement_id' => $requirement->requirement_id,
                'org_id' => $orgId,
                'trainer_id' => $requirement->accepted_trainer_id,
                'booking_status' => 'assigned',
                'amount' => $totalAmount,
                'payment_status' => 'pending',
                'transaction_id' => $razorpayOrder->id // Store order_id initially
            ]);

            return response()->json([
                'status' => true,
                'order_id' => $razorpayOrder->id,
                'amount' => $amountInPaise,
                'currency' => 'INR',
                'key' => config('services.razorpay.key'), // This is SAFE - it's the public key
                'booking_id' => $booking->booking_id,
                'name' => Auth::guard('org_web')->user()->name,
                'email' => Auth::guard('org_web')->user()->email,
                'contact' => Auth::guard('org_web')->user()->mobile,
                'description' => 'Payment for ' . $requirement->program->title
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to initiate payment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verifyPayment(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature' => 'required',
            'booking_id' => 'required|exists:bookings,booking_id'
        ]);

        try {
            $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

            // Verify payment signature
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ];

            $api->utility->verifyPaymentSignature($attributes);

            // Update booking
            DB::beginTransaction();

            $booking = Booking::findOrFail($request->booking_id);
            $booking->update([
                'payment_status' => 'completed',
                'transaction_id' => $request->razorpay_payment_id // Update with actual payment_id
            ]);

            // Update requirement status
            $requirement = TrainingRequirement::findOrFail($booking->requirement_id);
            $requirement->update([
                'status' => 'assigned',
                'payment' => 'completed'
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Payment completed successfully!',
                'booking_id' => $booking->booking_id
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            // Mark payment as failed for retry
            if ($request->booking_id) {
                Booking::where('booking_id', $request->booking_id)
                    ->update(['payment_status' => 'failed']);
            }

            return response()->json([
                'status' => false,
                'message' => 'Payment verification failed: ' . $e->getMessage()
            ], 400);
        }
    }
}
