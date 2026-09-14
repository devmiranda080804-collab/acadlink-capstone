<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ProgramAssignment;
use App\Models\User;
use Illuminate\Http\Request;

class ProgramAssignmentController extends Controller
{
    protected function currentSchoolYear(): string
    {
        $now = now();
        $year = $now->year;
        return $now->month >= 8 ? $year . '-' . ($year + 1) : ($year - 1) . '-' . $year;
    }

    public function index(Request $request)
    {
        $role = auth()->user()->role;
        $program = $role === 'program_head' ? auth()->user()->program : $request->get('program');

        $courses = Course::when($program, fn($q) => $q->where('program', $program))
            ->orderBy('program')->orderBy('code')
            ->get();

        $schoolYear = $request->get('school_year', $this->currentSchoolYear());
        $semester   = $request->get('semester', 'First Semester');

        $assignments = ProgramAssignment::with('faculty')
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->get()
            ->groupBy('course_id');

        // Faculty list for the dropdown (filtered by program when Program Head)
        $facultyList = User::where('role', 'faculty')
            ->whereNull('archived_at')
            ->when($program, fn($q) => $q->where('program', $program))
            ->orderBy('name')
            ->get();

        $viewMap = [
            'admin'        => 'admin.program-assignment',
            'program_head' => 'program-head.program-assignment',
            'secretary'    => 'secretary.program-assignment',
        ];

        return view($viewMap[$role], compact(
            'courses', 'assignments', 'facultyList', 'program',
            'schoolYear', 'semester'
        ));
    }

    public function store(Request $request)
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'program_head', 'secretary']), 403);

        $request->validate([
            'course_id'   => 'required|exists:courses,id',
            'faculty_id'  => 'required|exists:users,id',
            'school_year' => 'required|string',
            'semester'    => 'required|string',
        ]);

        // If Program Head, make sure only their own program is allowed
        if (auth()->user()->role === 'program_head') {
            $course = Course::findOrFail($request->course_id);
            abort_unless($course->program === auth()->user()->program, 403);
        }

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
        abort_unless(in_array(auth()->user()->role, ['admin', 'program_head', 'secretary']), 403);

        if (auth()->user()->role === 'program_head') {
            abort_unless($assignment->course->program === auth()->user()->program, 403);
        }

        $assignment->delete();

        return back()->with('success', 'Assignment removed.');
    }
}