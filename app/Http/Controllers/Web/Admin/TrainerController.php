<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trainer;
use Illuminate\Http\Request;

class TrainerController extends Controller
{
    public function view()
    {
        return view('admin.trainer.view');
    }

    public function list(Request $request)
    {
        $query = Trainer::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        // Filter by organization type
        if ($request->filled('org_type')) {
            $query->where('for_org_type', $request->org_type);
        }

        // Filter by training mode
        if ($request->filled('training_mode')) {
            $query->where('training_mode', $request->training_mode);
        }

        // Filter by verified status
        if ($request->filled('verified')) {
            $query->where('verified', $request->verified);
        }

        // Paginate
        $trainers = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json($trainers);
    }

    public function show($id)
    {
        $trainer = Trainer::findOrFail($id);
        return response()->json($trainer);
    }

    public function verify($id)
    {
        $trainer = Trainer::findOrFail($id);
        $trainer->verified = 'verified';
        $trainer->save();

        // Send email
        \Illuminate\Support\Facades\Mail::to($trainer->email)->send(new \App\Mail\TrainerVerifiedMail($trainer));

        return response()->json([
            'success' => true,
            'message' => 'Trainer verified successfully.'
        ]);
    }

    public function suspend($id)
    {
        $trainer = Trainer::findOrFail($id);
        // Toggle between verified and suspended
        $trainer->verified = ($trainer->verified === 'suspended') ? 'verified' : 'suspended';
        $trainer->save();

        $message = ($trainer->verified === 'suspended') ? 'Trainer suspended successfully.' : 'Trainer activated successfully.';

        // Send email based on status
        if ($trainer->verified === 'suspended') {
            \Illuminate\Support\Facades\Mail::to($trainer->email)->send(new \App\Mail\TrainerSuspendedMail($trainer));
        } else {
            \Illuminate\Support\Facades\Mail::to($trainer->email)->send(new \App\Mail\TrainerVerifiedMail($trainer));
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
}
