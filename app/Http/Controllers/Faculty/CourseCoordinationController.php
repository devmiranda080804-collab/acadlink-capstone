<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\CourseTopic;
use App\Models\ProgramAssignment;
use App\Support\AcademicTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseCoordinationController extends Controller
{
    // Courses this faculty is actually assigned to teach this term — the same
    // scoping used by the Assessment Generator's Subject dropdown.
    protected function assignedCourseIds()
    {
        return ProgramAssignment::where('faculty_id', auth()->id())
            ->where('school_year', AcademicTerm::currentSchoolYear())
            ->where('semester', AcademicTerm::currentSemester())
            ->pluck('course_id');
    }

    public function index(Request $request)
    {
        $courseIds = $this->assignedCourseIds();

        $courses = Course::whereIn('id', $courseIds)->curriculumOrder()->get();

        $selectedCourse = null;
        $materials = collect();
        $courseOutcomes = collect();
        $courseTopics = collect();

        if ($request->filled('course_id')) {
            $selectedCourse = Course::where('id', $request->course_id)
                ->whereIn('id', $courseIds) // security: only assigned courses
                ->first();

            if ($selectedCourse) {
                $materials = $selectedCourse->materials()->latest()->get()->groupBy('type');
                $courseOutcomes = $selectedCourse->outcomes()->with('programOutcomes')->get();
                $courseTopics = $selectedCourse->topics()->get()->groupBy('grading_period');
            }
        }

        return view('faculty.course-coordination', compact('courses', 'selectedCourse', 'materials', 'courseOutcomes', 'courseTopics'));
    }

    // ─── Topics & Hours (OBTL) ──────────────────────────────────────
    // Faculty prepare their own OBTL for the courses they're assigned to teach —
    // shared per course, so co-instructors on the same course see and build on
    // the same topic list. Feeds the Assessment Generator's TOS Generator
    // (Panel comment #8: Total Hours must be auto-fetched from OBE data).

    public function storeCourseTopic(Request $request)
    {
        $request->validate([
            'course_id'      => 'required|exists:courses,id',
            'grading_period' => 'required|in:Prelim,Midterm,Final',
            'topic'          => 'required|string|max:255',
            'weeks'          => 'nullable|string|max:50',
            'hours'          => 'required|integer|min:1',
            'notes'          => 'nullable|string|max:5000',
            'module'         => 'nullable|file|mimes:pdf|max:20480', // 20MB max — PDF only, fed directly to Claude's document input
        ]);

        // Security: only for courses this faculty is actually assigned to teach
        $course = Course::whereIn('id', $this->assignedCourseIds())
            ->where('id', $request->course_id)
            ->firstOrFail();

        $nextOrder = CourseTopic::where('course_id', $course->id)
            ->where('grading_period', $request->grading_period)
            ->max('order') + 1;

        $modulePath = null;
        $moduleFileName = null;
        if ($request->hasFile('module')) {
            $file = $request->file('module');
            $modulePath = $file->store('course-topic-modules', 'public');
            $moduleFileName = $file->getClientOriginalName();
        }

        CourseTopic::create([
            'course_id'        => $course->id,
            'grading_period'   => $request->grading_period,
            'topic'            => $request->topic,
            'weeks'            => $request->weeks,
            'hours'            => $request->hours,
            'notes'            => $request->notes,
            'module_path'      => $modulePath,
            'module_file_name' => $moduleFileName,
            'order'            => $nextOrder,
            'created_by'       => auth()->id(),
        ]);

        return back()->with('success', 'Topic added.');
    }

    public function updateCourseTopic(Request $request, CourseTopic $courseTopic)
    {
        abort_unless($this->assignedCourseIds()->contains($courseTopic->course_id), 403);

        $request->validate([
            'topic'  => 'required|string|max:255',
            'weeks'  => 'nullable|string|max:50',
            'hours'  => 'required|integer|min:1',
            'notes'  => 'nullable|string|max:5000',
            'module' => 'nullable|file|mimes:pdf|max:20480',
        ]);

        $data = [
            'topic' => $request->topic,
            'weeks' => $request->weeks,
            'hours' => $request->hours,
            'notes' => $request->notes,
        ];

        if ($request->hasFile('module')) {
            if ($courseTopic->module_path) {
                Storage::disk('public')->delete($courseTopic->module_path);
            }
            $file = $request->file('module');
            $data['module_path'] = $file->store('course-topic-modules', 'public');
            $data['module_file_name'] = $file->getClientOriginalName();
        }

        $courseTopic->update($data);

        return back()->with('success', 'Topic updated.');
    }

    public function destroyCourseTopic(CourseTopic $courseTopic)
    {
        abort_unless($this->assignedCourseIds()->contains($courseTopic->course_id), 403);

        if ($courseTopic->module_path) {
            Storage::disk('public')->delete($courseTopic->module_path);
        }
        $courseTopic->delete();

        return back()->with('success', 'Topic removed.');
    }

    // ─── Official OBTL document upload ─────────────────────────────
    // The actual prepared-and-signed OBTL file (matching the real, formal
    // document faculty already produce) — the structured Topics & Hours above
    // are the computable extract of it that the TOS Generator relies on.

    public function storeMaterial(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title'     => 'required|string|max:255',
            'version'   => 'nullable|string|max:20',
            'file'      => 'required|file|mimes:pdf,doc,docx,xls,xlsx,zip|max:20480', // 20MB max
        ]);

        // Security: only for courses this faculty is actually assigned to teach
        $course = Course::whereIn('id', $this->assignedCourseIds())
            ->where('id', $request->course_id)
            ->firstOrFail();

        $file = $request->file('file');
        $path = $file->store('course-materials', 'public');

        CourseMaterial::create([
            'course_id'   => $course->id,
            'uploaded_by' => auth()->id(),
            'title'       => $request->title,
            'type'        => 'obtl',
            'file_path'   => $path,
            'file_name'   => $file->getClientOriginalName(),
            'file_type'   => $file->getClientOriginalExtension(),
            'version'     => $request->version ?: 'v1.0',
            'file_size'   => $file->getSize(),
        ]);

        return back()->with('success', 'OBTL document uploaded successfully.');
    }

    public function destroyMaterial(CourseMaterial $material)
    {
        abort_unless(
            $material->type === 'obtl' && $this->assignedCourseIds()->contains($material->course_id),
            403
        );

        Storage::disk('public')->delete($material->file_path);
        $material->delete();

        return back()->with('success', 'OBTL document deleted.');
    }
}
