<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\TemplateDocument;
use App\Models\Announcement;
use App\Models\CalendarActivity;
use App\Models\ProgramAssignment;
use App\Support\AcademicTerm;

class DashboardController extends Controller
{
    protected function distributedForMyProgram(string $myProgram)
    {
        return TemplateDocument::whereHas('programs', fn($p) => $p->where('program', $myProgram)->whereNotNull('distributed_at'));
    }

    public function index()
    {
        $facultyId = auth()->id();
        $myProgram = auth()->user()->program;

        // Templates distributed to my program, broken down by type
        $syllabusCount    = $this->distributedForMyProgram($myProgram)->where('type', 'syllabus')->count();
        $lessonPlanCount  = $this->distributedForMyProgram($myProgram)->where('type', 'lesson_plan')->count();
        $courseGuideCount = $this->distributedForMyProgram($myProgram)->where('type', 'course_guide')->count();
        $moduleCount      = $this->distributedForMyProgram($myProgram)->where('type', 'module')->count();

        // Most recently distributed templates (for quick access)
        $revisionTemplates = $this->distributedForMyProgram($myProgram)
            ->with('creator')
            ->latest()
            ->take(5)
            ->get();

        // Recent announcements for their program
        $recentAnnouncements = Announcement::with('user')
            ->active()
            ->whereHas('programs', fn($p) => $p->where('program', $myProgram))
            ->latest()
            ->take(4)
            ->get();

        // Unread announcements, for the dashboard notification banner
        $unreadAnnouncements = Announcement::active()->visibleTo(auth()->user())
            ->unreadBy(auth()->user())
            ->latest()
            ->take(3)
            ->get();

        // Upcoming activities
        $upcomingActivities = CalendarActivity::where('activity_date', '>=', now()->toDateString())
            ->orderBy('activity_date')
            ->take(5)
            ->get();

        // Assigned courses for the current school year/semester
        $myCourses = ProgramAssignment::with('course')
            ->where('faculty_id', $facultyId)
            ->where('school_year', AcademicTerm::currentSchoolYear())
            ->where('semester', AcademicTerm::currentSemester())
            ->get();

        return view('faculty.dashboard', compact(
            'syllabusCount', 'lessonPlanCount', 'courseGuideCount', 'moduleCount',
            'revisionTemplates', 'recentAnnouncements', 'upcomingActivities', 'myCourses',
            'unreadAnnouncements'
        ));
    }
}
