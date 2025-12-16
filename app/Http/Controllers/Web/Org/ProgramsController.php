<?php

namespace App\Http\Controllers\Web\Org;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\TrainingRequirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'mode' => $request->mode
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Program request submitted successfully!'
        ]);
    }


    public function show_requestedPrograms(Request $request)
    {
        $orgId = Auth::guard('org_web')->user()->org_id;

        $requirements = TrainingRequirement::with(['program.programType'])
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

        $req->delete();

        return response()->json([
            'status' => true,
            'message' => 'Program request cancelled successfully.'
        ]);
    }
}
