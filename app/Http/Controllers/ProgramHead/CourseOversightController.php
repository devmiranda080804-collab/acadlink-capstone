<?php

namespace App\Http\Controllers\ProgramHead;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseOversightController extends Controller
{
    public function index(Request $request)
    {
        $myProgram = auth()->user()->program;

        // Courses sa sariling program lang ng PH
        $courses = Course::where('program', $myProgram)->orderBy('code')->get();

        $selectedCourse = null;
        $materials = collect();

        if ($request->filled('course_id')) {
            $selectedCourse = Course::where('id', $request->course_id)
                ->where('program', $myProgram) // security: sariling program lang
                ->first();

            if ($selectedCourse) {
                $materials = $selectedCourse->materials()->latest()->get();
            }
        }

        return view('program-head.course-oversight', compact('courses', 'selectedCourse', 'materials', 'myProgram'));
    }

    public function store(Request $request)
    {
        $myProgram = auth()->user()->program;

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title'     => 'required|string|max:255',
            'version'   => 'nullable|string|max:20',
            'file'      => 'required|file|mimes:pdf,doc,docx,xls,xlsx,zip|max:20480', // 20MB max
        ]);

        // Security: tiyakin na sariling program lang ang course
        $course = Course::where('id', $request->course_id)
            ->where('program', $myProgram)
            ->firstOrFail();

        $file = $request->file('file');
        $path = $file->store('course-materials', 'public');

        CourseMaterial::create([
            'course_id'   => $course->id,
            'uploaded_by' => auth()->id(),
            'title'       => $request->title,
            'file_path'   => $path,
            'file_name'   => $file->getClientOriginalName(),
            'file_type'   => $file->getClientOriginalExtension(),
            'version'     => $request->version ?: 'v1.0',
            'file_size'   => $file->getSize(),
        ]);

        return back()->with('success', 'Course material uploaded successfully.');
    }

    public function destroy(CourseMaterial $material)
    {
        $myProgram = auth()->user()->program;

        // Security: sariling program lang ang pwedeng burahin
        abort_unless($material->course->program === $myProgram, 403);

        // Burahin ang file sa storage
        Storage::disk('public')->delete($material->file_path);
        $material->delete();

        return back()->with('success', 'Course material deleted.');
    }
}