<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\ProgramAssignment;
use App\Models\User;
use App\Support\AcademicTerm;
use Illuminate\Http\Request;

// Admin/Dean can assign faculty to courses across every program. Program Head
// can also assign faculty, but only within their own program — see
// ProgramHead\ProgramAssignmentController. Both write to the same
// ProgramAssignment table, so each side sees what the other has assigned.
class ProgramAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $program    = $request->get('program');
        $yearLevel  = $request->get('year_level');
        $schoolYear = $request->get('school_year', AcademicTerm::currentSchoolYear());
        $semester   = $request->get('semester', 'First Semester');

        // Courses shown are scoped to the selected year level and curriculum
        // semester too — e.g. picking "Second Year" + "First Semester" only
        // lists the subjects actually taught in Second Year, First Semester.
        $courses = Course::when($program, fn($q) => $q->where('program', $program))
            ->when($yearLevel, fn($q) => $q->where('year_level', $yearLevel))
            ->when($semester, fn($q) => $q->where('semester_offered', $semester))
            ->orderBy('program')
            ->curriculumOrder()
            ->get();

        $assignments = ProgramAssignment::with('faculty')
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->get()
            ->groupBy('course_id');

        // Not filtered by the page's Program filter — the Assign Faculty modal scopes
        // itself to each course's own program client-side (a course keeps its program
        // even when the admin is viewing "All Programs"), so this list needs everyone.
        $facultyList = User::where('role', 'faculty')
            ->whereNull('archived_at')
            ->orderBy('name')
            ->get();

        return view('admin.program-assignment', compact(
            'courses', 'assignments', 'facultyList', 'program', 'yearLevel',
            'schoolYear', 'semester'
        ) + ['schoolYears' => AcademicTerm::schoolYearOptions()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id'   => 'required|exists:courses,id',
            'faculty_id'  => 'required|exists:users,id',
            'school_year' => 'required|string',
            'semester'    => 'required|string',
        ]);

        ProgramAssignment::firstOrCreate([
            'course_id'   => $request->course_id,
            'faculty_id'  => $request->faculty_id,
            'school_year' => $request->school_year,
            'semester'    => $request->semester,
        ], [
            'assigned_by' => auth()->id(),
        ]);

        return back()->with('success', 'Faculty assigned to course.');
    }

    public function destroy(ProgramAssignment $assignment)
    {
        $assignment->delete();

        return back()->with('success', 'Assignment removed.');
    }
}
