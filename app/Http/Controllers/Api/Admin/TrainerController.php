<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trainer;
use Illuminate\Http\Request;

class TrainerController extends Controller
{
    public function index(Request $request)
    {
        try {
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

            $trainers = $query->orderBy('created_at', 'desc')->paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'Trainers fetched successfully',
                'data' => $trainers,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch trainers: ' . $e->getMessage(),
                'data' => null,
                'status' => 500
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $trainer = Trainer::findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Trainer details fetched successfully',
                'data' => $trainer,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Trainer not found',
                'data' => null,
                'status' => 404
            ], 404);
        }
    }

    public function verify($id)
    {
        try {
            $trainer = Trainer::findOrFail($id);
            $trainer->verified = 'verified';
            $trainer->save();

            return response()->json([
                'success' => true,
                'message' => 'Trainer verified successfully',
                'data' => $trainer,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify trainer',
                'data' => null,
                'status' => 500
            ], 500);
        }
    }

    public function suspend($id)
    {
        try {
            $trainer = Trainer::findOrFail($id);
            $trainer->verified = ($trainer->verified === 'suspended') ? 'verified' : 'suspended';
            $trainer->save();

            $message = ($trainer->verified === 'suspended') ? 'Trainer suspended successfully' : 'Trainer activated successfully';

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $trainer,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to suspend/activate trainer',
                'data' => null,
                'status' => 500
            ], 500);
        }
    }
}
