<?php

namespace App\Http\Controllers\Web\Org;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\TrainingContent;
use App\Models\TrainingRequirement;
use App\Models\ProgramType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActiveProgramController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $orgId = Auth::guard('org_web')->user()->org_id;

            $query = TrainingRequirement::with(['program.programType', 'booking.trainer'])
                ->where('org_id', $orgId)
                ->where('status', 'assigned'); // Only active/assigned programs

            // Search Filter
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->whereHas('program', function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                });
            }

            // Program Type Filter
            if ($request->has('program_type') && !empty($request->program_type)) {
                $typeId = $request->program_type;
                $query->whereHas('program', function ($q) use ($typeId) {
                    $q->where('program_type_id', $typeId);
                });
            }

            $activePrograms = $query->orderBy('updated_at', 'desc')->paginate(9);

            return response()->json([
                'status' => true,
                'html' => view('organisation.active-programs.partials.program-list', compact('activePrograms'))->render(),
                'pagination' => $activePrograms->links('pagination::bootstrap-5')->toHtml()
            ]);
        }

        $programTypes = ProgramType::all();
        return view('organisation.active-programs.index', compact('programTypes'));
    }

    public function viewContent($booking_id)
    {
        $orgId = Auth::guard('org_web')->user()->org_id;

        // Verify booking belongs to org
        $booking = Booking::with(['requirement.program', 'trainer'])
            ->where('booking_id', $booking_id)
            ->where('org_id', $orgId)
            ->firstOrFail();

        // Fetch contents
        $contents = TrainingContent::where('booking_id', $booking_id)
            ->where('is_visible_to_org', 1) // Ensure visibility
            ->orderBy('created_at', 'desc')
            ->get();

        // Group by type for tabs
        $groupedContent = [
            'video' => $contents->where('content_type', 'video'),
            'pdf' => $contents->where('content_type', 'pdf'),
            'text' => $contents->where('content_type', 'text'),
            'link' => $contents->where('content_type', 'link'),
            'meeting' => $contents->where('content_type', 'meeting'),
        ];

        // Count for badges
        $counts = [
            'video' => $groupedContent['video']->count(),
            'pdf' => $groupedContent['pdf']->count(),
            'text' => $groupedContent['text']->count(),
            'link' => $groupedContent['link']->count(),
            'meeting' => $groupedContent['meeting']->count(),
        ];

        return view('organisation.active-programs.view-content', compact('booking', 'groupedContent', 'counts'));
    }
    public function showTrainer($trainer_id)
    {
        $trainer = \App\Models\Trainer::findOrFail($trainer_id);

        return response()->json([
            'status' => true,
            'data' => [
                'name' => $trainer->name,
                'email' => $trainer->email,
                'phone' => $trainer->phone,
                'profile_pic' => $trainer->profile_pic ? asset('storage/' . $trainer->profile_pic) : null,
                'biodata' => $trainer->biodata,
                'achievements' => $trainer->achievements,
                'city' => $trainer->city,
                'state' => $trainer->state
            ]
        ]);
    }
}
