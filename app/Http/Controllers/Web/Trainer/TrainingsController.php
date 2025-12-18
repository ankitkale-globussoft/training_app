<?php

namespace App\Http\Controllers\Web\Trainer;

use App\Http\Controllers\Controller;
use App\Models\TrainingRequirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;


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
            'status' => 'pending_payment',
            'accepted_trainer_id' => $trainerId
        ]);

        $link = url('/org/requestedPrograms');
        Mail::send('emails.request-accepted', ['link' => $link], function($message) {
           $message->to();
           $message->subject('Training Request Accepted'); 
        });

        return response()->json([
            'status' => true,
            'message' => 'Training request accepted successfully!'
        ]);
    }

    public function upcomming(Request $request)
    {
        return view('trainer.trainings.upcoming');
    }

    public function list(Request $request)
    {
        $trainerId = Auth::guard('trainer_web')->user()->trainer_id;

        $query = TrainingRequirement::with(['organisation', 'program'])
            ->where('accepted_trainer_id', $trainerId);

        // Tab based filtering
        if ($request->type === 'assigned') {
            $query->where('status', 'assigned');
        }

        if ($request->type === 'payment_pending') {
            $query->where('status', 'pending_payment');
        }

        if ($request->type === 'cancelled') {
            $query->where('status', 'cancelled');
        }

        return $query->latest()->paginate(10);
    }
}
