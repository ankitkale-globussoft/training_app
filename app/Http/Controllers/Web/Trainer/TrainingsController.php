<?php

namespace App\Http\Controllers\Web\Trainer;

use App\Http\Controllers\Controller;
use App\Models\TrainingRequirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class TrainingsController extends Controller
{
    public function open_trainings(Request $request)
    {
        $trainerId = Auth::guard('trainer_web')->user()->trainer_id;

        $requirements = TrainingRequirement::with([
                'program.programType',
                'organisation'
            ])
            ->where('status', 'open')
            ->whereNull('accepted_trainer_id')
            ->latest()
            ->paginate(9);

        return view('trainer.trainings.open', compact('requirements'));
    }

    public function acceptTraining(Request $request)
    {
        $request->validate([
            'requirement_id' => 'required|integer'
        ]);

        $trainerId = Auth::guard('trainer_web')->user()->trainer_id;

        $req = TrainingRequirement::where('requirement_id', $request->requirement_id)
            ->where('status', 'open')
            ->whereNull('accepted_trainer_id')
            ->first();

        if (!$req) {
            return response()->json([
                'status' => false,
                'message' => 'This training request is no longer available.'
            ], 422);
        }

        $req->update([
            'status' => 'accepted',
            'accepted_trainer_id' => $trainerId
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Training request accepted successfully!'
        ]);
    }

}
