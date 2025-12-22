<?php

namespace App\Http\Controllers\Api\Trainer;

use App\Http\Controllers\Controller;
use App\Models\TrainingRequirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainingsController extends Controller
{
    public function open_trainings(Request $request)
    {
        try {
            $trainer = Auth::user();

            $query = TrainingRequirement::with([
                'program.programType',
                'organisation'
            ])
                ->select('training_requirements.*')
                ->join('organizations', 'training_requirements.org_id', '=', 'organizations.org_id')
                ->where('training_requirements.status', 'open')
                ->whereNull('training_requirements.accepted_trainer_id');

            // Filter: Training Mode
            if ($request->filled('mode')) {
                $query->where('training_requirements.mode', $request->mode);
            }

            // Filter: City
            if ($request->filled('city')) {
                $query->where('organizations.city', 'like', '%' . $request->city . '%');
            }

            // Filter: Kilometers Range (Pincode Prefix logic)
            if ($request->filled('range') && $trainer->pincode) {
                $pincode = (string) $trainer->pincode;
                $range = $request->range;

                if ($range == '10') {
                    $prefix = substr($pincode, 0, 4);
                    $query->where('organizations.pincode', 'like', $prefix . '%');
                } elseif ($range == '30') {
                    $prefix = substr($pincode, 0, 3);
                    $query->where('organizations.pincode', 'like', $prefix . '%');
                } elseif ($range == '100') {
                    $prefix = substr($pincode, 0, 2);
                    $query->where('organizations.pincode', 'like', $prefix . '%');
                }
            }

            // Search Filter (Program Title)
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('program', function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                });
            }

            // Sorting
            $sort = $request->get('sort', 'latest');
            switch ($sort) {
                case 'price_high':
                    $query->join('programs', 'training_requirements.program_id', '=', 'programs.program_id')
                        ->orderByRaw('(programs.cost * training_requirements.number_of_students) DESC');
                    break;
                case 'price_low':
                    $query->join('programs', 'training_requirements.program_id', '=', 'programs.program_id')
                        ->orderByRaw('(programs.cost * training_requirements.number_of_students) ASC');
                    break;
                case 'students_high':
                    $query->orderBy('number_of_students', 'DESC');
                    break;
                case 'latest':
                default:
                    $query->orderBy('training_requirements.created_at', 'DESC');
                    break;
            }

            $requirements = $query->paginate(9);

            return response()->json([
                'success' => true,
                'message' => 'Open trainings fetched successfully',
                'data' => $requirements,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch open trainings: ' . $e->getMessage(),
                'data' => null,
                'status' => 500
            ], 500);
        }
    }

    public function acceptTraining(Request $request)
    {
        try {
            $request->validate([
                'requirement_id' => 'required|integer'
            ]);

            $trainerId = Auth::user()->trainer_id;

            $req = TrainingRequirement::where('requirement_id', $request->requirement_id)
                ->where('status', 'open')
                ->whereNull('accepted_trainer_id')
                ->first();

            if (!$req) {
                return response()->json([
                    'success' => false,
                    'message' => 'This training request is no longer available',
                    'data' => null,
                    'status' => 422
                ], 422);
            }

            $req->update([
                'status' => 'pending_payment',
                'accepted_trainer_id' => $trainerId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Training request accepted successfully',
                'data' => $req,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to accept training: ' . $e->getMessage(),
                'data' => null,
                'status' => 400
            ], 400);
        }
    }

    public function upcoming(Request $request)
    {
        try {
            $trainerId = Auth::user()->trainer_id;

            $query = TrainingRequirement::with(['organisation', 'program'])
                ->where('accepted_trainer_id', $trainerId);

            if ($request->filled('type')) {
                $type = $request->type;
                if (in_array($type, ['assigned', 'pending_payment', 'cancelled'])) {
                    $query->where('status', $type);
                }
            }

            $trainings = $query->latest()->paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'Upcoming trainings fetched successfully',
                'data' => $trainings,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch upcoming trainings: ' . $e->getMessage(),
                'data' => null,
                'status' => 500
            ], 500);
        }
    }
}
