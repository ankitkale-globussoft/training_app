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
        $trainer = Auth::guard('trainer_web')->user();
        $trainerId = $trainer->trainer_id;

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
        // Range 0: Within same sector (first 4 digits)
        // Range 1: Within same area (first 3 digits)
        // Range 2: Within same district (first 2 digits)
        if ($request->filled('range') && $trainer->pincode) {
            $pincode = (string) $trainer->pincode;
            $range = $request->range;

            if ($range == '10') { // ~10km
                $prefix = substr($pincode, 0, 4);
                $query->where('organizations.pincode', 'like', $prefix . '%');
            } elseif ($range == '30') { // ~30km
                $prefix = substr($pincode, 0, 3);
                $query->where('organizations.pincode', 'like', $prefix . '%');
            } elseif ($range == '100') { // ~100km
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
                // Join or subquery needed if cost is in programs
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

        $requirements = $query->paginate(9)->withQueryString();

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
        Mail::send('emails.request-accepted', ['link' => $link], function ($message) {
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
