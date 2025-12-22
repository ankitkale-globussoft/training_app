<?php

namespace App\Http\Controllers\Api\Org;

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
        try {
            $orgId = Auth::user()->org_id;

            $query = Program::with([
                'programType',
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

            $programs = $query->paginate(9);

            return response()->json([
                'success' => true,
                'message' => 'Programs fetched successfully',
                'data' => $programs,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch programs: ' . $e->getMessage(),
                'data' => null,
                'status' => 500
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $program = Program::with(['programType', 'trainers'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Program details fetched successfully',
                'data' => $program,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Program not found',
                'data' => null,
                'status' => 404
            ], 404);
        }
    }

    public function requestProgram(Request $request)
    {
        try {
            $request->validate([
                'program_id' => 'required|integer',
                'mode' => 'required|in:online,offline',
                'number_of_students' => 'required|integer|min:1',
                'schedule_date' => 'required|date',
                'schedule_time' => 'required|string',
            ]);

            $orgId = Auth::user()->org_id;

            $alreadyRequested = TrainingRequirement::where('org_id', $orgId)
                ->where('program_id', $request->program_id)
                ->exists();

            if ($alreadyRequested) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already requested this program',
                    'data' => null,
                    'status' => 422
                ], 422);
            }

            $requirement = TrainingRequirement::create([
                'org_id' => $orgId,
                'program_id' => $request->program_id,
                'mode' => $request->mode,
                'number_of_students' => $request->number_of_students,
                'schedule_date' => $request->schedule_date,
                'schedule_time' => $request->schedule_time
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Program request submitted successfully',
                'data' => $requirement,
                'status' => 201
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit request: ' . $e->getMessage(),
                'data' => null,
                'status' => 400
            ], 400);
        }
    }

    public function show_requestedPrograms(Request $request)
    {
        try {
            $orgId = Auth::user()->org_id;

            $requirements = TrainingRequirement::with(['program.programType', 'booking'])
                ->where('org_id', $orgId)
                ->latest()
                ->paginate(9);

            return response()->json([
                'success' => true,
                'message' => 'Requested programs fetched successfully',
                'data' => $requirements,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch requested programs: ' . $e->getMessage(),
                'data' => null,
                'status' => 500
            ], 500);
        }
    }

    public function cancelRequest($id)
    {
        try {
            $orgId = Auth::user()->org_id;

            $req = TrainingRequirement::where('requirement_id', $id)
                ->where('org_id', $orgId)
                ->firstOrFail();

            Booking::where('requirement_id', $id)
                ->where('payment_status', 'failed')
                ->delete();

            $req->delete();

            return response()->json([
                'success' => true,
                'message' => 'Program request cancelled successfully',
                'data' => null,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel request: ' . $e->getMessage(),
                'data' => null,
                'status' => 400
            ], 400);
        }
    }

    public function initiatePayment(Request $request)
    {
        try {
            $request->validate([
                'requirement_id' => 'required|exists:training_requirements,requirement_id'
            ]);

            $orgId = Auth::user()->org_id;

            $requirement = TrainingRequirement::with('program')
                ->where('requirement_id', $request->requirement_id)
                ->where('org_id', $orgId)
                ->firstOrFail();

            $paidBooking = Booking::where('requirement_id', $requirement->requirement_id)
                ->where('payment_status', 'completed')
                ->first();

            if ($paidBooking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment already completed for this program',
                    'data' => null,
                    'status' => 422
                ], 422);
            }

            Booking::where('requirement_id', $requirement->requirement_id)
                ->where('org_id', $orgId)
                ->whereIn('payment_status', ['failed', 'pending'])
                ->delete();

            $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

            $totalAmount = $requirement->program->cost * $requirement->number_of_students;
            $amountInPaise = $totalAmount * 100;

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

            $booking = Booking::create([
                'requirement_id' => $requirement->requirement_id,
                'org_id' => $orgId,
                'trainer_id' => $requirement->accepted_trainer_id,
                'booking_status' => 'assigned',
                'amount' => $totalAmount,
                'payment_status' => 'pending',
                'transaction_id' => $razorpayOrder->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment initiated successfully',
                'data' => [
                    'order_id' => $razorpayOrder->id,
                    'amount' => $amountInPaise,
                    'currency' => 'INR',
                    'key' => config('services.razorpay.key'),
                    'booking_id' => $booking->booking_id,
                    'user' => [
                        'name' => Auth::user()->name,
                        'email' => Auth::user()->email,
                        'contact' => Auth::user()->mobile,
                    ],
                    'description' => 'Payment for ' . $requirement->program->title
                ],
                'status' => 200
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate payment: ' . $e->getMessage(),
                'data' => null,
                'status' => 500
            ], 500);
        }
    }

    public function verifyPayment(Request $request)
    {
        try {
            $request->validate([
                'razorpay_order_id' => 'required',
                'razorpay_payment_id' => 'required',
                'razorpay_signature' => 'required',
                'booking_id' => 'required|exists:bookings,booking_id'
            ]);

            $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ];

            $api->utility->verifyPaymentSignature($attributes);

            DB::beginTransaction();

            $booking = Booking::findOrFail($request->booking_id);
            $booking->update([
                'payment_status' => 'completed',
                'transaction_id' => $request->razorpay_payment_id
            ]);

            $requirement = TrainingRequirement::findOrFail($booking->requirement_id);
            $requirement->update([
                'status' => 'assigned',
                'payment' => 'completed'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment completed successfully',
                'data' => ['booking_id' => $booking->booking_id],
                'status' => 200
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            if ($request->booking_id) {
                Booking::where('booking_id', $request->booking_id)
                    ->update(['payment_status' => 'failed']);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed: ' . $e->getMessage(),
                'data' => null,
                'status' => 400
            ], 400);
        }
    }
}
