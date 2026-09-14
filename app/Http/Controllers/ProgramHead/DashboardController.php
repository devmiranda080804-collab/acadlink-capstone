<?php

namespace App\Http\Controllers\ProgramHead;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TemplateDocument;
use App\Models\Course;
use App\Models\CalendarActivity;
use App\Models\Announcement;

class DashboardController extends Controller
{
    public function index()
    {
        $myProgram = auth()->user()->program;

        // Faculty within the PH's own program
        $facultyCount = User::where('role', 'faculty')
            ->where('program', $myProgram)
            ->whereNull('archived_at')
            ->count();

        // Templates the Secretary already forwarded, still waiting for this PH to distribute
        $awaitingDistribution = TemplateDocument::whereNotNull('forwarded_at')
            ->whereHas('programs', fn($p) => $p->where('program', $myProgram)->whereNull('distributed_at'))
            ->count();

        // Courses within the PH's program
        $courseCount = Course::where('program', $myProgram)->count();

        // Templates this PH has already distributed to their faculty
        $distributedCount = TemplateDocument::whereHas('programs', fn($p) => $p->where('program', $myProgram)->whereNotNull('distributed_at'))
            ->count();

        // List of templates awaiting this PH's distribution
        $reviewTemplates = TemplateDocument::with('creator')
            ->whereNotNull('forwarded_at')
            ->whereHas('programs', fn($p) => $p->where('program', $myProgram)->whereNull('distributed_at'))
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

        return view('program-head.program-head-dashboard', compact(
            'myProgram', 'facultyCount', 'awaitingDistribution', 'courseCount',
            'distributedCount', 'reviewTemplates', 'upcomingActivities',
            'unreadAnnouncements'
        ));
    }
}
