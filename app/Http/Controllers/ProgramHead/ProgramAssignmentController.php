<?php

namespace App\Http\Controllers\ProgramHead;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\ProgramAssignment;
use App\Models\User;
use App\Support\AcademicTerm;
use Illuminate\Http\Request;

// Program Head can assign faculty too, but only within their own program —
// Admin/Dean keeps full cross-program access unchanged. A Program Head sees
// every assignment in their program regardless of who made it (including
// ones Admin/Dean made), but never another program's assignments.
class ProgramAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $myProgram  = auth()->user()->program;
        $yearLevel  = $request->get('year_level');
        $schoolYear = $request->get('school_year', AcademicTerm::currentSchoolYear());
        $semester   = $request->get('semester', 'First Semester');

        $courses = Course::where('program', $myProgram)
            ->when($yearLevel, fn($q) => $q->where('year_level', $yearLevel))
            ->when($semester, fn($q) => $q->where('semester_offered', $semester))
            ->curriculumOrder()
            ->get();

        $assignments = ProgramAssignment::with(['faculty', 'assigner'])
            ->whereIn('course_id', Course::where('program', $myProgram)->pluck('id'))
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->get()
            ->groupBy('course_id');

        $facultyList = User::where('role', 'faculty')
            ->inProgram($myProgram)
            ->whereNull('archived_at')
            ->orderBy('name')
            ->get();

        return view('program-head.program-assignment', compact(
            'courses', 'assignments', 'facultyList', 'myProgram', 'yearLevel',
            'schoolYear', 'semester'
        ) + ['schoolYears' => AcademicTerm::schoolYearOptions()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id'     => 'required|exists:courses,id',
            'faculty_ids'   => 'required|array|min:1',
            'faculty_ids.*' => 'integer|exists:users,id',
            'school_year'   => 'required|string',
            'semester'      => 'required|string',
        ], ['faculty_ids.required' => 'Choose at least one faculty member.']);

        $myProgram = auth()->user()->program;

        // Security: the course and the faculty must both belong to this PH's own program
        $course = Course::where('id', $request->course_id)->where('program', $myProgram)->firstOrFail();
        $faculty = User::whereIn('id', $request->faculty_ids)->where('role', 'faculty')->inProgram($myProgram)->get();
        abort_if($faculty->count() !== count(array_unique($request->faculty_ids)), 404);

        $added = 0;
        foreach ($faculty as $member) {
            $added += ProgramAssignment::firstOrCreate([
                'course_id'   => $course->id,
                'faculty_id'  => $member->id,
                'school_year' => $request->school_year,
                'semester'    => $request->semester,
            ], [
                'assigned_by' => auth()->id(),
            ])->wasRecentlyCreated ? 1 : 0;
        }

        return back()->with('success', $added . ' faculty ' . ($added === 1 ? 'member' : 'members') . ' assigned to ' . $course->code . '.');
    }

    public function destroy(ProgramAssignment $assignment)
    {
        // Security: can only remove assignments within this PH's own program,
        // regardless of whether Admin/Dean or this PH originally created it.
        abort_unless($assignment->course->program === auth()->user()->program, 403);

        $assignment->delete();

        return back()->with('success', 'Assignment removed.');
    }
}
