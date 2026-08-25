<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Template;
use App\Models\CalendarActivity;

class DashboardController extends Controller
{
    public function index()
    {
        // Approved templates na pwedeng i-distribute (hindi pa na-distribute)
        $readyToDistribute = Template::where('status', 'approved')
            ->whereNull('distributed_at')
            ->count();

        // Na-distribute na
        $distributedCount = Template::where('status', 'approved')
            ->whereNotNull('distributed_at')
            ->count();

        // Total faculty accounts (lahat ng program — institutional oversight)
        $facultyCount = User::where('role', 'faculty')
            ->whereNull('archived_at')
            ->count();

        // Total approved templates
        $totalApproved = Template::where('status', 'approved')->count();

        // Listahan ng approved na hindi pa na-distribute
        $pendingDistribution = Template::with('faculty')
            ->where('status', 'approved')
            ->whereNull('distributed_at')
            ->latest()
            ->take(5)
            ->get();

        // Upcoming activities
        $upcomingActivities = CalendarActivity::where('activity_date', '>=', now()->toDateString())
            ->orderBy('activity_date')
            ->take(5)
            ->get();

        return view('secretary.secretary-dashboard', compact(
            'readyToDistribute', 'distributedCount', 'facultyCount',
            'totalApproved', 'pendingDistribution', 'upcomingActivities'
        ));
    }
}