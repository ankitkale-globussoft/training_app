<?php

namespace App\Http\Controllers\Web\Org;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingProgress;
use App\Models\Candidate;
use App\Models\TrainingRequirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Exception;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $orgId = Auth::guard('org_web')->user()->org_id;

        // Fetch bookings that are 'completed' payment status and NOT finished progress
        $activeBookings = Booking::with('requirement.program')
            ->where('org_id', $orgId)
            ->where('payment_status', 'completed')
            ->whereHas('requirement', function ($q) {
                // Ensure requirement exists
                $q->whereNotNull('program_id');
            })
            // Filter out bookings that are fully completed if needed, but user said:
            // "no CRUD ... for which BookingProgress -> status should not be 'completed','test_completed','reviewed'"
            // So we can still list them, but maybe disable actions. For listing Active Programs, we usually just show all.
            ->get();

        $selectedBookingId = $request->booking_id;
        $students = [];
        $selectedBooking = null;

        if ($selectedBookingId) {
            $selectedBooking = Booking::with('requirement')->where('booking_id', $selectedBookingId)->first();
            if ($selectedBooking && $selectedBooking->org_id == $orgId) {
                $students = Candidate::where('booking_id', $selectedBookingId)->latest()->get();
            }
        }

        return view('organisation.students.index', compact('activeBookings', 'students', 'selectedBookingId', 'selectedBooking'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,booking_id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:candidates,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6',
        ]);

        $orgId = Auth::guard('org_web')->user()->org_id;
        $booking = Booking::with(['requirement', 'progress'])->findOrFail($request->booking_id);

        // 1. Ownership Check
        if ($booking->org_id != $orgId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        // 2. Payment Status Check
        if ($booking->payment_status !== 'completed') {
            return response()->json(['success' => false, 'message' => 'Cannot add students. Payment not completed.'], 422);
        }

        // 3. Progress Status Check
        $currentProgress = $booking->progress->last(); // Assuming last is current status
        // Or if there are multiple progress rows, check if ANY is completed? usually latest matters.
        // User said: "BookingProgress -> status shoud not be any of these :- 'completed','test_completed','reviewed'"
        $restrictedStatuses = ['completed', 'test_completed', 'reviewed'];
        if ($currentProgress && in_array($currentProgress->status, $restrictedStatuses)) {
            return response()->json(['success' => false, 'message' => 'Cannot add students. Program is already ' . $currentProgress->status], 422);
        }

        // 4. Student Count Check
        $currentStudentCount = Candidate::where('booking_id', $booking->booking_id)->count();
        $maxStudents = $booking->requirement->number_of_students;

        if ($currentStudentCount >= $maxStudents) {
            return response()->json(['success' => false, 'message' => "Student limit reached. Max allowed: $maxStudents"], 422);
        }

        // Create Student
        Candidate::create([
            'org_id' => $orgId,
            'program_id' => $booking->requirement->program_id,
            'booking_id' => $booking->booking_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'status' => 'active',
        ]);

        return response()->json(['success' => true, 'message' => 'Student added successfully.']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:candidates,email,' . $id . ',candidate_id',
            'phone' => 'required|string|max:20',
        ]);

        $candidate = Candidate::findOrFail($id);

        // Ownership Validation (via booking usually, or explicit org_id check if we trust candidate->org_id)
        if ($candidate->org_id != Auth::guard('org_web')->user()->org_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $candidate->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        if ($request->filled('password')) {
            $candidate->update(['password' => Hash::make($request->password)]);
        }

        return response()->json(['success' => true, 'message' => 'Student updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $candidate = Candidate::findOrFail($id);
        if ($candidate->org_id != Auth::guard('org_web')->user()->org_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        // Check if booking is still valid for modification? User said "Cannot CRUD ... if status is ...".
        // Assuming we should apply same restrictions for delete.
        $booking = Booking::with('progress')->find($candidate->booking_id);
        $restrictedStatuses = ['completed', 'test_completed', 'reviewed'];
        $currentProgress = $booking->progress->last();

        if ($currentProgress && in_array($currentProgress->status, $restrictedStatuses)) {
            return response()->json(['success' => false, 'message' => 'Cannot delete students. Program is already ' . $currentProgress->status], 422);
        }

        $candidate->delete();

        return response()->json(['success' => true, 'message' => 'Student deleted successfully.']);
    }

    public function toggleStatus($id)
    {
        $candidate = Candidate::findOrFail($id);
        if ($candidate->org_id != Auth::guard('org_web')->user()->org_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $candidate->status = ($candidate->status === 'active') ? 'inactive' : 'active';
        $candidate->save();

        return response()->json(['success' => true, 'message' => 'Student status updated.', 'new_status' => $candidate->status]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,booking_id',
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $orgId = Auth::guard('org_web')->user()->org_id;
        $booking = Booking::with(['requirement', 'progress'])->findOrFail($request->booking_id);

        if ($booking->org_id != $orgId)
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        if ($booking->payment_status !== 'completed')
            return response()->json(['success' => false, 'message' => 'Payment not completed.'], 422);

        $restrictedStatuses = ['completed', 'test_completed', 'reviewed'];
        $currentProgress = $booking->progress->last();
        if ($currentProgress && in_array($currentProgress->status, $restrictedStatuses)) {
            return response()->json(['success' => false, 'message' => 'Program is ' . $currentProgress->status], 422);
        }

        $file = $request->file('file');
        $csvData = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_shift($csvData); // Remove header row

        $currentCount = Candidate::where('booking_id', $booking->booking_id)->count();
        $limit = $booking->requirement->number_of_students;
        $remaining = $limit - $currentCount;

        if (count($csvData) > $remaining) {
            return response()->json(['success' => false, 'message' => "Import exceeds limit. You can only add $remaining more students."], 422);
        }

        $successCount = 0;
        $errors = [];

        foreach ($csvData as $index => $row) {
            // Expected format: Name, Email, Phone, Password
            if (count($row) < 4) {
                $errors[] = "Row " . ($index + 2) . ": Invalid format.";
                continue;
            }

            [$name, $email, $phone, $password] = $row;

            // Basic validation
            if (Candidate::where('email', $email)->exists()) {
                $errors[] = "Row " . ($index + 2) . ": Email $email already exists.";
                continue;
            }

            try {
                Candidate::create([
                    'org_id' => $orgId,
                    'program_id' => $booking->requirement->program_id,
                    'booking_id' => $booking->booking_id,
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'password' => Hash::make($password),
                    'status' => 'active',
                ]);
                $successCount++;
            } catch (Exception $e) {
                $errors[] = "Row " . ($index + 2) . ": Error saving data.";
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Imported $successCount students successfully.",
            'errors' => $errors
        ]);
    }
}
