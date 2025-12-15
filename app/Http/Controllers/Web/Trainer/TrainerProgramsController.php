<?php

namespace App\Http\Controllers\Web\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\ProgramType;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainerProgramsController extends Controller
{
    private function trainerId()
    {
        return Auth::guard('trainer_web')->user()->trainer_id;
    }

    // Browse all programs
    public function browse()
    {
        $programTypes = ProgramType::orderBy('name')->get();
        return view('trainer.programs.browse', compact('programTypes'));
    }

    // AJAX list of all programs
    public function list(Request $request)
    {
        $trainerId = $this->trainerId();

        $query = Program::leftJoin('program_trainer as pt', function ($join) use ($trainerId) {
            $join->on('pt.program_id', '=', 'programs.program_id')
                ->where('pt.trainer_id', $trainerId);
        })
            ->select(
                'programs.*',
                DB::raw('IF(pt.id IS NULL, 0, 1) as is_selected')
            );

        // 🔍 Filters
        if ($request->program_type_id) {
            $query->where('programs.program_type_id', $request->program_type_id);
        }

        if ($request->duration) {
            $query->where('programs.duration', $request->duration);
        }

        $programs = $query->paginate(10);

        $selectedCount = DB::table('program_trainer')
            ->where('trainer_id', $trainerId)
            ->count();

        return response()->json([
            'programs' => $programs,
            'selected_count' => $selectedCount
        ]);
    }


    // Select program
    public function select(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,program_id'
        ]);

        DB::table('program_trainer')->insertOrIgnore([
            'program_id' => $request->program_id,
            'trainer_id' => $this->trainerId(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    // Remove program
    public function remove(Request $request)
    {
        DB::table('program_trainer')
            ->where('program_id', $request->program_id)
            ->where('trainer_id', $this->trainerId())
            ->delete();

        return response()->json(['success' => true]);
    }

    // Selected programs index
    public function index()
    {
        $trainerId = $this->trainerId();

        $programs = Program::join('program_trainer', 'programs.program_id', '=', 'program_trainer.program_id')
            ->where('program_trainer.trainer_id', $trainerId)
            ->select('programs.*')
            ->paginate(10);

        return view('trainer.programs.index', compact('programs'));
    }
}
