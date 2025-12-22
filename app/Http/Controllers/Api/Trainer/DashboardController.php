<?php

namespace App\Http\Controllers\Api\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $trainerId = Auth::user()->trainer_id;

            // 1. Key Metrics
            $stats = [
                'total_earnings' => (float) Booking::where('trainer_id', $trainerId)
                    ->whereIn('payment_status', ['paid', 'completed'])
                    ->sum('amount'),
                'total_bookings' => Booking::where('trainer_id', $trainerId)->count(),
                'active_programs' => Booking::where('trainer_id', $trainerId)
                    ->whereHas('requirement', function ($q) {
                        $q->whereNotIn('status', ['completed', 'cancelled', 'rejected']);
                    })
                    ->count(),
            ];

            // 2. Monthly Earnings (Current Year)
            $earningsData = array_fill(0, 12, 0);
            $monthlyEarnings = Booking::select(
                DB::raw('SUM(amount) as total'),
                DB::raw('MONTH(created_at) as month')
            )
                ->where('trainer_id', $trainerId)
                ->whereIn('payment_status', ['paid', 'completed'])
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->get();

            foreach ($monthlyEarnings as $earning) {
                $earningsData[$earning->month - 1] = (float) $earning->total;
            }

            // 3. Program Popularity
            $programPopularity = Booking::join('training_requirements', 'bookings.requirement_id', '=', 'training_requirements.requirement_id')
                ->join('programs', 'training_requirements.program_id', '=', 'programs.program_id')
                ->select('programs.title', DB::raw('count(*) as total'))
                ->where('bookings.trainer_id', $trainerId)
                ->groupBy('programs.program_id', 'programs.title')
                ->orderByDesc('total')
                ->limit(5)
                ->get();

            // 4. Recent Activity
            $recentBookings = Booking::with(['organization', 'requirement.program'])
                ->where('trainer_id', $trainerId)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Trainer dashboard statistics fetched successfully',
                'data' => [
                    'metrics' => $stats,
                    'earnings_history' => $earningsData,
                    'program_popularity' => $programPopularity,
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
