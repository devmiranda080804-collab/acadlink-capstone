<?php

namespace App\Http\Controllers\ProgramHead;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Template;
use App\Models\Course;
use App\Models\CalendarActivity;

class DashboardController extends Controller
{
    public function index()
    {
        $myProgram = auth()->user()->program;

        // Faculty sa sariling program
        $facultyCount = User::where('role', 'faculty')
            ->where('program', $myProgram)
            ->whereNull('archived_at')
            ->count();

        // Templates pending review (kailangan niyang aksyunan) — sariling program
        $pendingReview = Template::where('program', $myProgram)
            ->where('status', 'pending_review')
            ->count();

        // Courses sa program niya
        $courseCount = Course::where('program', $myProgram)->count();

        // Templates na na-forward na niya kay Admin
        $forwardedCount = Template::where('program', $myProgram)
            ->whereIn('status', ['pending_approval', 'approved'])
            ->count();

        // Listahan ng templates na naghihintay ng review
        $reviewTemplates = Template::with('faculty')
            ->where('program', $myProgram)
            ->where('status', 'pending_review')
            ->latest()
            ->take(5)
            ->get();

        // Upcoming activities
        $upcomingActivities = CalendarActivity::where('activity_date', '>=', now()->toDateString())
            ->orderBy('activity_date')
            ->take(5)
            ->get();

        return view('program-head.program-head-dashboard', compact(
            'myProgram', 'facultyCount', 'pendingReview', 'courseCount',
            'forwardedCount', 'reviewTemplates', 'upcomingActivities'
        ));
    }
}