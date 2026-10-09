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
        $current = fn() => TemplateDocument::whereNull('superseded_at');
        $awaitingApproval = $current()->where('status', TemplateDocument::STATUS_PENDING)->count();
        $approvedCount = $current()->where('status', TemplateDocument::STATUS_APPROVED)->count();
        $returnedCount = $current()->where('status', TemplateDocument::STATUS_RETURNED)->count();

        // Total faculty accounts (all programs — institutional oversight)
        $facultyCount = User::where('role', 'faculty')
            ->whereNull('archived_at')
            ->count();

        // Returned templates first (they need the Secretary's action), then pending ones.
        $pendingDistribution = TemplateDocument::with('creator')
            ->whereNull('superseded_at')
            ->whereIn('status', [TemplateDocument::STATUS_RETURNED, TemplateDocument::STATUS_PENDING])
            ->orderByRaw("status = ? desc", [TemplateDocument::STATUS_RETURNED])
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
            'awaitingApproval', 'approvedCount', 'returnedCount', 'facultyCount',
            'pendingDistribution', 'upcomingActivities',
            'unreadAnnouncements'
        ));
    }
}
