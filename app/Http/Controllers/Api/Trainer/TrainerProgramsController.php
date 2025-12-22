<?php

namespace App\Http\Controllers\Api\Trainer;

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
        return Auth::user()->trainer_id;
    }

    public function browse()
    {
        try {
            $programTypes = ProgramType::orderBy('name')->get();
            return response()->json([
                'success' => true,
                'message' => 'Program types for browsing fetched successfully',
                'data' => $programTypes,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch program types',
                'data' => null,
                'status' => 500
            ], 500);
        }
    }

    public function list(Request $request)
    {
        try {
            $trainerId = $this->trainerId();

            $query = Program::leftJoin('program_trainer as pt', function ($join) use ($trainerId) {
                $join->on('pt.program_id', '=', 'programs.program_id')
                    ->where('pt.trainer_id', $trainerId);
            })
                ->select(
                    'programs.*',
                    DB::raw('IF(pt.id IS NULL, 0, 1) as is_selected')
                );

            if ($request->filled('program_type_id')) {
                $query->where('programs.program_type_id', $request->program_type_id);
            }

            if ($request->filled('duration')) {
                $query->where('programs.duration', $request->duration);
            }

            if ($request->filled('search')) {
                $query->where('programs.title', 'like', '%' . $request->search . '%');
            }

            $programs = $query->paginate(10);

            $selectedCount = DB::table('program_trainer')
                ->where('trainer_id', $trainerId)
                ->count();

            return response()->json([
                'success' => true,
                'message' => 'Programs list fetched successfully',
                'data' => [
                    'programs' => $programs,
                    'selected_count' => $selectedCount
                ],
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

    public function select(Request $request)
    {
        try {
            $request->validate([
                'program_id' => 'required|exists:programs,program_id'
            ]);

            DB::table('program_trainer')->insertOrIgnore([
                'program_id' => $request->program_id,
                'trainer_id' => $this->trainerId(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Program selected successfully',
                'data' => null,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to select program: ' . $e->getMessage(),
                'data' => null,
                'status' => 400
            ], 400);
        }
    }

    public function remove(Request $request)
    {
        try {
            $request->validate([
                'program_id' => 'required|exists:programs,program_id'
            ]);

            DB::table('program_trainer')
                ->where('program_id', $request->program_id)
                ->where('trainer_id', $this->trainerId())
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Program removed successfully',
                'data' => null,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove program: ' . $e->getMessage(),
                'data' => null,
                'status' => 400
            ], 400);
        }
    }

    public function index()
    {
        try {
            $trainerId = $this->trainerId();

            $programs = Program::join('program_trainer', 'programs.program_id', '=', 'program_trainer.program_id')
                ->where('program_trainer.trainer_id', $trainerId)
                ->select('programs.*')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'Selected programs fetched successfully',
                'data' => $programs,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch selected programs: ' . $e->getMessage(),
                'data' => null,
                'status' => 500
            ], 500);
        }
    }
}
