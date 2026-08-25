<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Models\Announcement;
use App\Models\CalendarActivity;
use App\Models\CourseAssignment;

class DashboardController extends Controller
{
    protected function currentSchoolYear(): string
    {
        $now = now();
        $year = $now->year;
        return $now->month >= 8 ? $year . '-' . ($year + 1) : ($year - 1) . '-' . $year;
    }

    protected function currentSemester(): string
    {
        $month = now()->month;
        if ($month >= 8 && $month <= 12) return 'First Semester';
        if ($month >= 1 && $month <= 5)  return 'Second Semester';
        return 'Summer';
    }

    public function index()
    {
        $facultyId = auth()->id();
        $myProgram = auth()->user()->program;

        // Bilang ng sariling templates
        $totalTemplates = Template::where('faculty_id', $facultyId)->count();

        // Templates na needs revision (kailangan niyang aksyunan)
        $needsRevision = Template::where('faculty_id', $facultyId)
            ->where('status', 'needs_revision')
            ->count();

        // Approved templates
        $approvedCount = Template::where('faculty_id', $facultyId)
            ->where('status', 'approved')
            ->count();

        // Pending (nasa review o approval pa)
        $pendingCount = Template::where('faculty_id', $facultyId)
            ->whereIn('status', ['pending_review', 'pending_approval'])
            ->count();

        // Listahan ng templates na needs revision (para sa quick action)
        $revisionTemplates = Template::where('faculty_id', $facultyId)
            ->where('status', 'needs_revision')
            ->latest()
            ->take(5)
            ->get();

        // Recent announcements para sa program niya
        $recentAnnouncements = Announcement::with('user')
            ->whereHas('programs', fn($p) => $p->where('program', $myProgram))
            ->latest()
            ->take(4)
            ->get();

        // Upcoming activities
        $upcomingActivities = CalendarActivity::where('activity_date', '>=', now()->toDateString())
            ->orderBy('activity_date')
            ->take(5)
            ->get();

        // Assigned courses para sa kasalukuyang school year/semester
        $myCourses = CourseAssignment::with('course')
            ->where('faculty_id', $facultyId)
            ->where('school_year', $this->currentSchoolYear())
            ->where('semester', $this->currentSemester())
            ->get();

        return view('faculty.dashboard', compact(
            'totalTemplates', 'needsRevision', 'approvedCount', 'pendingCount',
            'revisionTemplates', 'recentAnnouncements', 'upcomingActivities', 'myCourses'
        ));
    }
}