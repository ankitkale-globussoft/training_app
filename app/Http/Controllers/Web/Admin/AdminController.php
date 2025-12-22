<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        // 1. General Stats
        $orgCount = \App\Models\Organization::count();
        $trainerCount = \App\Models\Trainer::count();
        $candidateCount = \App\Models\Candidate::count();
        $bookingCount = \App\Models\Booking::count();
        $totalRevenue = \App\Models\Booking::whereIn('payment_status', ['paid', 'completed'])->sum('amount');

        // 2. Growth Data (Monthly Registrations for current year)
        $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        $orgGrowth = array_fill(0, 12, 0);
        $trainerGrowth = array_fill(0, 12, 0);

        $orgGrowthData = \App\Models\Organization::select(\DB::raw('COUNT(*) as count'), \DB::raw('MONTH(created_at) as month'))
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->get();
        foreach ($orgGrowthData as $data) {
            $orgGrowth[$data->month - 1] = $data->count;
        }

        $trainerGrowthData = \App\Models\Trainer::select(\DB::raw('COUNT(*) as count'), \DB::raw('MONTH(created_at) as month'))
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->get();
        foreach ($trainerGrowthData as $data) {
            $trainerGrowth[$data->month - 1] = $data->count;
        }

        // 3. Geographic Distribution (By State)
        $orgGeoData = \App\Models\Organization::select('state', \DB::raw('count(*) as total'))
            ->whereNotNull('state')
            ->groupBy('state')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $trainerGeoData = \App\Models\Trainer::select('state', \DB::raw('count(*) as total'))
            ->whereNotNull('state')
            ->groupBy('state')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // 4. Popular Programs
        $popularPrograms = \App\Models\Booking::join('training_requirements', 'bookings.requirement_id', '=', 'training_requirements.requirement_id')
            ->join('programs', 'training_requirements.program_id', '=', 'programs.program_id')
            ->select('programs.title', \DB::raw('count(*) as total'))
            ->groupBy('programs.program_id', 'programs.title')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // 5. Recent Activity
        $recentBookings = \App\Models\Booking::with(['organization', 'requirement.program'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $recentOrgs = \App\Models\Organization::orderByDesc('created_at')->limit(5)->get();
        $recentTrainers = \App\Models\Trainer::orderByDesc('created_at')->limit(5)->get();

        return view('admin.dashboard', compact(
            'orgCount',
            'trainerCount',
            'candidateCount',
            'bookingCount',
            'totalRevenue',
            'months',
            'orgGrowth',
            'trainerGrowth',
            'orgGeoData',
            'trainerGeoData',
            'popularPrograms',
            'recentBookings',
            'recentOrgs',
            'recentTrainers'
        ));
    }

}
