<?php

namespace App\Http\Controllers\Api\Org;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\TrainingContent;
use App\Models\TrainingRequirement;
use App\Models\ProgramType;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActiveProgramController extends Controller
{
    public function index(Request $request)
    {
        try {
            $orgId = Auth::user()->org_id;

            $query = TrainingRequirement::with(['program.programType', 'booking.trainer'])
                ->where('org_id', $orgId)
                ->where('status', 'assigned');

            // Search Filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('program', function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                });
            }

            // Program Type Filter
            if ($request->filled('program_type')) {
                $typeId = $request->program_type;
                $query->whereHas('program', function ($q) use ($typeId) {
                    $q->where('program_type_id', $typeId);
                });
            }

            $activePrograms = $query->orderBy('updated_at', 'desc')->paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'Active programs fetched successfully',
                'data' => $activePrograms,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch active programs: ' . $e->getMessage(),
                'data' => null,
                'status' => 500
            ], 500);
        }
    }

    public function viewContent($booking_id)
    {
        try {
            $orgId = Auth::user()->org_id;

            $booking = Booking::with(['requirement.program', 'trainer'])
                ->where('booking_id', $booking_id)
                ->where('org_id', $orgId)
                ->firstOrFail();

            $contents = TrainingContent::where('booking_id', $booking_id)
                ->where('is_visible_to_org', 1)
                ->orderBy('created_at', 'desc')
                ->get();

            $groupedContent = [
                'video' => $contents->where('content_type', 'video')->values(),
                'pdf' => $contents->where('content_type', 'pdf')->values(),
                'text' => $contents->where('content_type', 'text')->values(),
                'link' => $contents->where('content_type', 'link')->values(),
                'meeting' => $contents->where('content_type', 'meeting')->values(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Training content fetched successfully',
                'data' => [
                    'booking' => $booking,
                    'contents' => $groupedContent
                ],
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Content not found or access denied',
                'data' => null,
                'status' => 404
            ], 404);
        }
    }

    public function showTrainer($trainer_id)
    {
        try {
            $trainer = Trainer::findOrFail($trainer_id);

            return response()->json([
                'success' => true,
                'message' => 'Trainer profile fetched successfully',
                'data' => [
                    'name' => $trainer->name,
                    'email' => $trainer->email,
                    'phone' => $trainer->phone,
                    'profile_pic' => $trainer->profile_pic ? asset('storage/' . $trainer->profile_pic) : null,
                    'biodata' => $trainer->biodata,
                    'achievements' => $trainer->achievements,
                    'city' => $trainer->city,
                    'state' => $trainer->state
                ],
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Trainer not found',
                'data' => null,
                'status' => 404
            ], 404);
        }
    }
}
