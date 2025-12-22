<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Trainer;
use App\Models\Candidate;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // 1. General Stats
            $stats = [
                'organizations' => Organization::count(),
                'trainers' => Trainer::count(),
                'candidates' => Candidate::count(),
                'bookings' => Booking::count(),
                'total_revenue' => (float) Booking::whereIn('payment_status', ['paid', 'completed'])->sum('amount'),
            ];

            // 2. Growth Data (Current Year)
            $orgGrowth = array_fill(0, 12, 0);
            $trainerGrowth = array_fill(0, 12, 0);

            $orgGrowthData = Organization::select(DB::raw('COUNT(*) as count'), DB::raw('MONTH(created_at) as month'))
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->get();
            foreach ($orgGrowthData as $data) {
                $orgGrowth[$data->month - 1] = $data->count;
            }

            $trainerGrowthData = Trainer::select(DB::raw('COUNT(*) as count'), DB::raw('MONTH(created_at) as month'))
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->get();
            foreach ($trainerGrowthData as $data) {
                $trainerGrowth[$data->month - 1] = $data->count;
            }

            // 3. Popular Programs
            $popularPrograms = Booking::join('training_requirements', 'bookings.requirement_id', '=', 'training_requirements.requirement_id')
                ->join('programs', 'training_requirements.program_id', '=', 'programs.program_id')
                ->select('programs.title', DB::raw('count(*) as total'))
                ->groupBy('programs.program_id', 'programs.title')
                ->orderByDesc('total')
                ->limit(5)
                ->get();

            // 4. Recent Activity
            $recentBookings = Booking::with(['organization', 'requirement.program'])
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Admin dashboard statistics fetched successfully',
                'data' => [
                    'metrics' => $stats,
                    'growth' => [
                        'organizations' => $orgGrowth,
                        'trainers' => $trainerGrowth
                    ],
                    'popular_programs' => $popularPrograms,
                    'recent_bookings' => $recentBookings
                ],
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard stats: ' . $e->getMessage(),
                'data' => null,
                'status' => 500
            ], 500);
        }
    }
}
