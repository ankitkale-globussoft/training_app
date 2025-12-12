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
            $query->where(function($q) use ($search) {
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
}
