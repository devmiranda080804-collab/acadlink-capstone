<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\CourseTopic;
use App\Models\ProgramAssignment;
use App\Services\AcademicDocumentValidator;
use App\Support\AcademicTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;

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
            // Required when first adding a topic — Teaching Notes alone is not
            // enough grounding for the Assessment Generator's AI question
            // drafting. Editing an existing topic can still leave it as-is
            // (see updateCourseTopic()), since it may already have a module.
            'module'         => 'required|file|mimes:pdf,docx|max:20480', // 20MB max
        ]);

        // Security: only for courses this faculty is actually assigned to teach
        $course = Course::whereIn('id', $this->assignedCourseIds())
            ->where('id', $request->course_id)
            ->firstOrFail();

        $nextOrder = CourseTopic::where('course_id', $course->id)
            ->where('grading_period', $request->grading_period)
            ->max('order') + 1;

        $moduleData = $this->storeModuleFile($request);
        if ($moduleData['error']) {
            return back()->withErrors(['module' => $moduleData['error']])->with('active_tab', 'topics');
        }

        CourseTopic::create([
            'course_id'        => $course->id,
            'grading_period'   => $request->grading_period,
            'topic'            => $request->topic,
            'weeks'            => $request->weeks,
            'hours'            => $request->hours,
            'notes'            => $moduleData['extracted_notes'] ?? $request->notes,
            'module_path'      => $moduleData['module_path'],
            'module_file_name' => $moduleData['module_file_name'],
            'order'            => $nextOrder,
            'created_by'       => auth()->id(),
        ]);

        return back()->with('success', 'Topic added.')->with('active_tab', 'topics');
    }

    public function updateCourseTopic(Request $request, CourseTopic $courseTopic)
    {
        abort_unless($this->assignedCourseIds()->contains($courseTopic->course_id), 403);

        $request->validate([
            'topic'  => 'required|string|max:255',
            'weeks'  => 'nullable|string|max:50',
            'hours'  => 'required|integer|min:1',
            'notes'  => 'nullable|string|max:5000',
            'module' => 'nullable|file|mimes:pdf,docx|max:20480',
        ]);

        $data = [
            'topic' => $request->topic,
            'weeks' => $request->weeks,
            'hours' => $request->hours,
            'notes' => $request->notes,
        ];

        if ($request->hasFile('module')) {
            $moduleData = $this->storeModuleFile($request);
            if ($moduleData['error']) {
                return back()->withErrors(['module' => $moduleData['error']])->with('active_tab', 'topics');
            }

            if ($courseTopic->module_path) {
                Storage::disk('public')->delete($courseTopic->module_path);
            }
            $data['module_path'] = $moduleData['module_path'];
            $data['module_file_name'] = $moduleData['module_file_name'];
            if ($moduleData['extracted_notes']) {
                $data['notes'] = $moduleData['extracted_notes'];
            }
        }

        $courseTopic->update($data);

        return back()->with('success', 'Topic updated.')->with('active_tab', 'topics');
    }

    // Stores the uploaded module file. PDFs are kept as-is — the Assessment
    // Generator feeds the actual PDF to Claude's document input directly, the
    // highest-fidelity option. DOCX files can't be read natively by the API, so
    // their text is extracted here (once, at upload time) and used to fill/replace
    // Teaching Notes instead — everything downstream only ever needs to check
    // "is there a PDF" vs "is there text", never re-parse the DOCX.
    //
    // A content sanity-check runs first — anything can be picked in a file
    // dialog, and if what's uploaded isn't actually teaching content, the
    // Assessment Generator's AI question drafting would have nothing real
    // to read from later. Returns ['error' => ...] instead of storing when
    // the content clearly doesn't look like a module.
    protected function storeModuleFile(Request $request): array
    {
        $result = ['module_path' => null, 'module_file_name' => null, 'extracted_notes' => null, 'error' => null];

        if (! $request->hasFile('module')) {
            return $result;
        }

        $file = $request->file('module');

        $check = (new AcademicDocumentValidator())->validate($file->getRealPath(), $file->getClientOriginalExtension(), 'module');
        if (!$check['valid']) {
            $result['error'] = ($check['reason'] ?? 'This file does not appear to be a teaching module.')
                . ' Please upload the actual learning material for this topic.';
            return $result;
        }

        $result['module_path'] = $file->store('course-topic-modules', 'public');
        $result['module_file_name'] = $file->getClientOriginalName();

        if (strtolower($file->getClientOriginalExtension()) === 'docx') {
            $result['extracted_notes'] = $this->extractDocxText($file->getRealPath());
        }

        return $result;
    }

    // Fails open — a malformed .docx or a PhpWord parsing error shouldn't 500
    // the whole upload; the module file itself is still stored either way,
    // this only skips auto-filling Teaching Notes from it.
    protected function extractDocxText(string $path): ?string
    {
        try {
            $phpWord = IOFactory::load($path);
            $text = '';

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $t = $element->getText();
                        $text .= (is_string($t) ? $t : '') . "\n";
                    } elseif (method_exists($element, 'getElements')) {
                        foreach ($element->getElements() as $child) {
                            if (method_exists($child, 'getText')) {
                                $childText = $child->getText();
                                $text .= is_string($childText) ? $childText : '';
                            }
                        }
                        $text .= "\n";
                    }
                }
            }

            return trim($text);
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }

    public function destroyCourseTopic(CourseTopic $courseTopic)
    {
        abort_unless($this->assignedCourseIds()->contains($courseTopic->course_id), 403);

        if ($courseTopic->module_path) {
            Storage::disk('public')->delete($courseTopic->module_path);
        }
        $courseTopic->delete();

        return back()->with('success', 'Topic removed.')->with('active_tab', 'topics');
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

        // Content sanity-check — this upload is specifically claimed to be the
        // official OBTL, so it should actually look like one.
        $check = (new AcademicDocumentValidator())->validate($file->getRealPath(), $file->getClientOriginalExtension(), 'obtl');
        if (!$check['valid']) {
            return back()->withErrors([
                'file' => ($check['reason'] ?? 'This file does not appear to be an OBTL document.')
                    . ' Please upload the official OBTL file for this course.',
            ])->with('active_tab', 'topics');
        }

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

        return back()->with('success', 'OBTL document uploaded successfully.')->with('active_tab', 'topics');
    }

    public function destroyMaterial(CourseMaterial $material)
    {
        abort_unless(
            $material->type === 'obtl' && $this->assignedCourseIds()->contains($material->course_id),
            403
        );

        Storage::disk('public')->delete($material->file_path);
        $material->delete();

        return back()->with('success', 'OBTL document deleted.')->with('active_tab', 'topics');
    }
}
