<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Template;
use App\Models\Announcement;
use App\Models\CalendarActivity;

class DashboardController extends Controller
{
    public function index()
    {
        // Account counts (active lang, hindi kasama archived)
        $programHeadCount = User::where('role', 'program_head')->whereNull('archived_at')->count();
        $secretaryCount   = User::where('role', 'secretary')->whereNull('archived_at')->count();
        $facultyCount     = User::where('role', 'faculty')->whereNull('archived_at')->count();

        // Templates pending Admin approval (kailangan niyang aksyunan)
        $pendingApproval = Template::where('status', 'pending_approval')->count();

        // Template status breakdown
        $approvedCount = Template::where('status', 'approved')->count();
        $rejectedCount = Template::where('status', 'rejected')->count();

        // Announcements + activities
        $announcementCount = Announcement::count();
        $upcomingActivities = CalendarActivity::where('activity_date', '>=', now()->toDateString())
            ->orderBy('activity_date')
            ->take(5)
            ->get();

        // Templates na naghihintay ng approval (para sa quick list)
        $pendingTemplates = Template::with('faculty')
            ->where('status', 'pending_approval')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.admin-dashboard', compact(
            'programHeadCount', 'secretaryCount', 'facultyCount',
            'pendingApproval', 'approvedCount', 'rejectedCount',
            'announcementCount', 'upcomingActivities', 'pendingTemplates'
        ));
    }
}