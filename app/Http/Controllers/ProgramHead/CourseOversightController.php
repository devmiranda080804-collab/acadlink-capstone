<?php

namespace App\Http\Controllers\ProgramHead;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\CourseOutcome;
use App\Models\ProgramOutcome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseOversightController extends Controller
{
    public function index(Request $request)
    {
        $myProgram = auth()->user()->program;

        // Courses within the PH's own program only
        $courses = Course::where('program', $myProgram)->curriculumOrder()->get();

        $selectedCourse = null;
        $materials = collect();
        $courseOutcomes = collect();
        $courseTopics = collect();

        if ($request->filled('course_id')) {
            $selectedCourse = Course::where('id', $request->course_id)
                ->where('program', $myProgram) // security: own program only
                ->first();

            if ($selectedCourse) {
                $materials = $selectedCourse->materials()->latest()->get()->groupBy('type');
                $courseOutcomes = $selectedCourse->outcomes()->with('programOutcomes')->get();
                $courseTopics = $selectedCourse->topics()->get()->groupBy('grading_period');
            }
        }

        // Program Outcomes apply to the whole program, independent of the selected course
        $programOutcomes = ProgramOutcome::where('program', $myProgram)->orderBy('order')->get();

        return view('program-head.course-oversight', compact(
            'courses', 'selectedCourse', 'materials', 'myProgram',
            'programOutcomes', 'courseOutcomes', 'courseTopics'
        ));
    }

    public function store(Request $request)
    {
        $myProgram = auth()->user()->program;

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title'     => 'required|string|max:255',
            'type'      => 'required|in:syllabus,tos,exam_bank,teaching_material',
            'version'   => 'nullable|string|max:20',
            'file'      => 'required|file|mimes:pdf,doc,docx,xls,xlsx,zip|max:20480', // 20MB max
        ]);

        // Security: ensure the course belongs to the PH's own program
        $course = Course::where('id', $request->course_id)
            ->where('program', $myProgram)
            ->firstOrFail();

        $file = $request->file('file');
        $path = $file->store('course-materials', 'public');

        CourseMaterial::create([
            'course_id'   => $course->id,
            'uploaded_by' => auth()->id(),
            'title'       => $request->title,
            'type'        => $request->type,
            'file_path'   => $path,
            'file_name'   => $file->getClientOriginalName(),
            'file_type'   => $file->getClientOriginalExtension(),
            'version'     => $request->version ?: 'v1.0',
            'file_size'   => $file->getSize(),
        ]);

        return back()->with('success', 'Course material uploaded successfully.')->with('active_tab', 'materials');
    }

    public function destroy(CourseMaterial $material)
    {
        $myProgram = auth()->user()->program;

        // Security: only materials from the PH's own program can be deleted
        abort_unless($material->course->program === $myProgram, 403);

        // Delete the file from storage
        Storage::disk('public')->delete($material->file_path);
        $material->delete();

        return back()->with('success', 'Course material deleted.')->with('active_tab', 'materials');
    }

    // ─── Program Outcomes ───────────────────────────────────────────

    public function storeProgramOutcome(Request $request)
    {
        $request->validate([
            'code'        => 'required|string|max:20',
            'description' => 'required|string|max:1000',
        ]);

        $myProgram = auth()->user()->program;
        $nextOrder = ProgramOutcome::where('program', $myProgram)->max('order') + 1;

        ProgramOutcome::create([
            'program'     => $myProgram,
            'code'        => $request->code,
            'description' => $request->description,
            'order'       => $nextOrder,
            'created_by'  => auth()->id(),
        ]);

        return back()->with('success', 'Program Outcome added.')->with('active_tab', 'outcomes');
    }

    public function destroyProgramOutcome(ProgramOutcome $programOutcome)
    {
        abort_unless($programOutcome->program === auth()->user()->program, 403);

        $programOutcome->delete();

        return back()->with('success', 'Program Outcome removed.')->with('active_tab', 'outcomes');
    }

    // ─── Course Outcomes ────────────────────────────────────────────

    public function storeCourseOutcome(Request $request)
    {
        $myProgram = auth()->user()->program;

        $request->validate([
            'course_id'         => 'required|exists:courses,id',
            'code'              => 'required|string|max:20',
            'description'       => 'required|string|max:1000',
            'sample_activities' => 'nullable|string|max:2000',
        ]);

        $course = Course::where('id', $request->course_id)
            ->where('program', $myProgram)
            ->firstOrFail();

        $nextOrder = CourseOutcome::where('course_id', $course->id)->max('order') + 1;

        CourseOutcome::create([
            'course_id'         => $course->id,
            'code'              => $request->code,
            'description'       => $request->description,
            'sample_activities' => $request->sample_activities,
            'order'             => $nextOrder,
            'created_by'        => auth()->id(),
        ]);

        return back()->with('success', 'Course Outcome added.')->with('active_tab', 'outcomes');
    }

    public function destroyCourseOutcome(CourseOutcome $courseOutcome)
    {
        abort_unless($courseOutcome->course->program === auth()->user()->program, 403);

        $courseOutcome->delete();

        return back()->with('success', 'Course Outcome removed.')->with('active_tab', 'outcomes');
    }

    // Topics & Hours (OBTL) are authored by Faculty for the courses they're
    // actually assigned to teach (see Faculty\CourseCoordinationController) —
    // Program Head gets a read-only view here for oversight, not edit rights.

    // ─── CO–PO Mapping ──────────────────────────────────────────────

    public function toggleMapping(CourseOutcome $courseOutcome, ProgramOutcome $programOutcome)
    {
        $myProgram = auth()->user()->program;
        abort_unless($courseOutcome->course->program === $myProgram, 403);
        abort_unless($programOutcome->program === $myProgram, 403);

        if ($courseOutcome->isMappedTo($programOutcome->id)) {
            $courseOutcome->programOutcomes()->detach($programOutcome->id);
        } else {
            $courseOutcome->programOutcomes()->attach($programOutcome->id);
        }

        return back()->with('success', 'CO–PO mapping updated.')->with('active_tab', 'outcomes');
    }
}
