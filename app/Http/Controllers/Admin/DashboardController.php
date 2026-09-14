<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TemplateDocument;
use App\Models\Announcement;
use App\Models\CalendarActivity;

class DashboardController extends Controller
{
    public function index()
    {
        // Account counts (active only, excluding archived)
        $programHeadCount = User::where('role', 'program_head')->whereNull('archived_at')->count();
        $secretaryCount   = User::where('role', 'secretary')->whereNull('archived_at')->count();
        $facultyCount     = User::where('role', 'faculty')->whereNull('archived_at')->count();

        // Templates Admin has uploaded that the Secretary hasn't forwarded yet
        $awaitingSecretary = TemplateDocument::whereNull('forwarded_at')->count();

        // Announcements + activities
        $announcementCount = Announcement::count();
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

        // Templates still awaiting the Secretary (for the quick list)
        $pendingTemplates = TemplateDocument::with('creator')
            ->whereNull('forwarded_at')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.admin-dashboard', compact(
            'programHeadCount', 'secretaryCount', 'facultyCount',
            'awaitingSecretary',
            'announcementCount', 'upcomingActivities', 'pendingTemplates',
            'unreadAnnouncements'
        ));
    }
}
