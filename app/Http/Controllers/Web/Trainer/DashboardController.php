<?php

namespace App\Http\Controllers\Web\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\TrainingRequirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $trainerId = Auth::guard('trainer_web')->user()->trainer_id;

        // --- Key Metrics ---

        // Total Earnings: Sum of 'amount' for bookings with 'completed' or 'paid' status
        $totalEarnings = Booking::where('trainer_id', $trainerId)
            ->whereIn('payment_status', ['paid', 'completed'])
            ->sum('amount');

        // Total Bookings: Count of all bookings assigned to this trainer
        $totalBookings = Booking::where('trainer_id', $trainerId)->count();

        // Active Programs: Count of requirements where status is not 'completed' or 'cancelled'
        // Assuming 'Booking' exists implies active assignment, or checking Requirement status
        $activePrograms = Booking::where('trainer_id', $trainerId)
            ->whereHas('requirement', function ($q) {
                $q->whereNotIn('status', ['completed', 'cancelled', 'rejected']);
            })
            ->count();


        // --- Chart Data: Monthly Earnings (Current Year) ---
        $monthlyEarnings = Booking::select(
            DB::raw('SUM(amount) as total'),
            DB::raw('MONTH(created_at) as month')
        )
            ->where('trainer_id', $trainerId)
            ->whereIn('payment_status', ['paid', 'completed'])
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $earningsData = array_fill(0, 12, 0); // Initialize 12 months with 0
        foreach ($monthlyEarnings as $earning) {
            $earningsData[$earning->month - 1] = (float) $earning->total;
        }


        // --- Chart Data: Program Popularity (Top 5) ---
        // Count bookings per program by joining with requirements and programs
        $programPopularity = Booking::join('training_requirements', 'bookings.requirement_id', '=', 'training_requirements.requirement_id')
            ->join('programs', 'training_requirements.program_id', '=', 'programs.program_id')
            ->select('programs.title', DB::raw('count(*) as total'))
            ->where('bookings.trainer_id', $trainerId)
            ->groupBy('programs.program_id', 'programs.title')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $programLabels = [];
        $programData = [];

        foreach ($programPopularity as $item) {
            $programLabels[] = \Illuminate\Support\Str::limit($item->title, 20);
            $programData[] = $item->total;
        }


        // --- Recent Activity ---
        $recentBookings = Booking::with(['organization', 'requirement.program'])
            ->where('trainer_id', $trainerId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('trainer.dashboard', compact(
            'totalEarnings',
            'totalBookings',
            'activePrograms',
            'earningsData',
            'programLabels',
            'programData',
            'recentBookings'
        ));
    }
}
