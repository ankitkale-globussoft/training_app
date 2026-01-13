<?php

namespace App\Http\Controllers\Api\Org;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Exception;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $orgId = Auth::user()->org_id;
            // Fetch bookings that are 'completed' payment status
            $activeBookings = Booking::with('requirement.program')
                ->where('org_id', $orgId)
                ->where('payment_status', 'completed')
                ->whereHas('requirement', function ($q) {
                    $q->whereNotNull('program_id');
                })
                ->get();

            $selectedBookingId = $request->booking_id;
            $students = [];
            $selectedBooking = null;

            if ($selectedBookingId) {
                $selectedBooking = Booking::with('requirement')->where('booking_id', $selectedBookingId)->first();
                if ($selectedBooking && $selectedBooking->org_id == $orgId) {
                    $students = Candidate::where('booking_id', $selectedBookingId)->latest()->get();
                } else {
                    // Reset if unauthorized or not found
                    $selectedBooking = null;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Data fetched successfully',
                'data' => [
                    'active_bookings' => $activeBookings,
                    'selected_booking' => $selectedBooking,
                    'students' => $students,
                ],
                'status' => 200
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch data: ' . $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,booking_id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:candidates,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
                'status' => 422
            ], 422);
        }

        try {
            $orgId = Auth::user()->org_id;
            $booking = Booking::with(['requirement', 'progress'])->findOrFail($request->booking_id);

            // 1. Ownership Check
            if ($booking->org_id != $orgId) {
                return response()->json(['success' => false, 'message' => 'Unauthorized action.', 'status' => 403], 403);
            }

            // 2. Payment Status Check
            if ($booking->payment_status !== 'completed') {
                return response()->json(['success' => false, 'message' => 'Cannot add students. Payment not completed.', 'status' => 422], 422);
            }

            // 3. Progress Status Check
            $currentProgress = $booking->progress->last();
            $restrictedStatuses = ['completed', 'test_completed', 'reviewed'];
            if ($currentProgress && in_array($currentProgress->status, $restrictedStatuses)) {
                return response()->json(['success' => false, 'message' => 'Cannot add students. Program is already ' . $currentProgress->status, 'status' => 422], 422);
            }

            // 4. Student Count Check
            $currentStudentCount = Candidate::where('booking_id', $booking->booking_id)->count();
            $maxStudents = $booking->requirement->number_of_students;

            if ($currentStudentCount >= $maxStudents) {
                return response()->json(['success' => false, 'message' => "Student limit reached. Max allowed: $maxStudents", 'status' => 422], 422);
            }

            // Create Student
            $student = Candidate::create([
                'org_id' => $orgId,
                'program_id' => $booking->requirement->program_id,
                'booking_id' => $booking->booking_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'status' => 'active',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Student added successfully.',
                'data' => $student,
                'status' => 201
            ], 201);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error adding student: ' . $e->getMessage(), 'status' => 500], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:candidates,email,' . $id . ',candidate_id',
            'phone' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
                'status' => 422
            ], 422);
        }

        try {
            $candidate = Candidate::findOrFail($id);

            // Ownership Validation
            if ($candidate->org_id != Auth::user()->org_id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized.', 'status' => 403], 403);
            }

            $candidate->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            if ($request->filled('password')) {
                $candidate->update(['password' => Hash::make($request->password)]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Student updated successfully.',
                'data' => $candidate,
                'status' => 200
            ], 200);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating student: ' . $e->getMessage(), 'status' => 500], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $candidate = Candidate::findOrFail($id);
            if ($candidate->org_id != Auth::user()->org_id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized.', 'status' => 403], 403);
            }

            $booking = Booking::with('progress')->find($candidate->booking_id);
            $restrictedStatuses = ['completed', 'test_completed', 'reviewed'];
            $currentProgress = $booking->progress->last();

            if ($currentProgress && in_array($currentProgress->status, $restrictedStatuses)) {
                return response()->json(['success' => false, 'message' => 'Cannot delete students. Program is already ' . $currentProgress->status, 'status' => 422], 422);
            }

            $candidate->delete();

            return response()->json(['success' => true, 'message' => 'Student deleted successfully.', 'status' => 200], 200);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting student: ' . $e->getMessage(), 'status' => 500], 500);
        }
    }

    public function toggleStatus($id)
    {
        try {
            $candidate = Candidate::findOrFail($id);
            if ($candidate->org_id != Auth::user()->org_id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized.', 'status' => 403], 403);
            }

            $candidate->status = ($candidate->status === 'active') ? 'inactive' : 'active';
            $candidate->save();

            return response()->json([
                'success' => true,
                'message' => 'Student status updated.',
                'new_status' => $candidate->status,
                'status' => 200
            ], 200);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating status: ' . $e->getMessage(), 'status' => 500], 500);
        }
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,booking_id',
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
                'status' => 422
            ], 422);
        }

        try {
            $orgId = Auth::user()->org_id;
            $booking = Booking::with(['requirement', 'progress'])->findOrFail($request->booking_id);

            if ($booking->org_id != $orgId)
                return response()->json(['success' => false, 'message' => 'Unauthorized.', 'status' => 403], 403);
            if ($booking->payment_status !== 'completed')
                return response()->json(['success' => false, 'message' => 'Payment not completed.', 'status' => 422], 422);

            $restrictedStatuses = ['completed', 'test_completed', 'reviewed'];
            $currentProgress = $booking->progress->last();
            if ($currentProgress && in_array($currentProgress->status, $restrictedStatuses)) {
                return response()->json(['success' => false, 'message' => 'Program is ' . $currentProgress->status, 'status' => 422], 422);
            }

            $file = $request->file('file');
            $csvData = array_map('str_getcsv', file($file->getRealPath()));
            $header = array_shift($csvData); // Remove header row

            $currentCount = Candidate::where('booking_id', $booking->booking_id)->count();
            $limit = $booking->requirement->number_of_students;
            $remaining = $limit - $currentCount;

            if (count($csvData) > $remaining) {
                return response()->json(['success' => false, 'message' => "Import exceeds limit. You can only add $remaining more students.", 'status' => 422], 422);
            }

            $successCount = 0;
            $errors = [];

            foreach ($csvData as $index => $row) {
                if (count($row) < 4) {
                    $errors[] = "Row " . ($index + 2) . ": Invalid format.";
                    continue;
                }

                [$name, $email, $phone, $password] = $row;

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
                'errors' => $errors,
                'status' => 200
            ], 200);

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Import failed: ' . $e->getMessage(), 'status' => 500], 500);
        }
    }
}
