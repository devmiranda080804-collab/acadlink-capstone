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

        // Templates the Dean sent to this program in the last 30 days
        $awaitingDistribution = TemplateDocument::whereNull('superseded_at')
            ->whereHas('programs', fn($p) => $p->where('program', $myProgram)->where('distributed_at', '>=', now()->subDays(30)))
            ->count();

        // Courses within the PH's program
        $courseCount = Course::where('program', $myProgram)->count();

        // Templates the Dean has sent to this program
        $distributedCount = TemplateDocument::whereNull('superseded_at')->whereHas('programs', fn($p) => $p->where('program', $myProgram)->whereNotNull('distributed_at'))
            ->count();

        // Templates most recently sent to this program
        $reviewTemplates = TemplateDocument::with('creator')
            ->whereNull('superseded_at')
            ->whereHas('programs', fn($p) => $p->where('program', $myProgram)->whereNotNull('distributed_at'))
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
