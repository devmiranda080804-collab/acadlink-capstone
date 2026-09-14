<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TemplateDocument;
use App\Models\CalendarActivity;
use App\Models\Announcement;

class DashboardController extends Controller
{
    public function index()
    {
        // Templates Admin uploaded that still need to be forwarded to Program Heads
        $awaitingForward = TemplateDocument::whereNull('forwarded_at')->count();

        // Already forwarded
        $forwardedCount = TemplateDocument::whereNotNull('forwarded_at')->count();

        // Total faculty accounts (all programs — institutional oversight)
        $facultyCount = User::where('role', 'faculty')
            ->whereNull('archived_at')
            ->count();

        // Total templates Admin has ever uploaded
        $totalTemplates = TemplateDocument::count();

        // List of templates awaiting forward
        $pendingDistribution = TemplateDocument::with('creator')
            ->whereNull('forwarded_at')
            ->latest()
            ->take(5)
            ->get();

        // Upcoming activities
        $upcomingActivities = CalendarActivity::where('activity_date', '>=', now()->toDateString())
            ->orderBy('activity_date')
            ->take(5)
            ->get();

        // Unread announcements, for the dashboard notification banner
        $unreadAnnouncements = Announcement::active()->visibleTo(auth()->user())
            ->unreadBy(auth()->user())
            ->latest()
            ->take(3)
            ->get();

        return view('secretary.secretary-dashboard', compact(
            'awaitingForward', 'forwardedCount', 'facultyCount',
            'totalTemplates', 'pendingDistribution', 'upcomingActivities',
            'unreadAnnouncements'
        ));
    }
}
