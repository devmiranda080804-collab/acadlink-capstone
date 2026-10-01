<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseMaterial;
use Illuminate\Http\Request;

class CourseFilingController extends Controller
{
    public function index(Request $request)
    {
        $program = $request->get('program');

        $courses = Course::when($program, fn($q) => $q->where('program', $program))
            ->orderBy('program')
            ->curriculumOrder()
            ->get();

        // For each course, check the filing status
        $filing = $courses->map(function ($course) {
            // Materials for this course
            $materials = CourseMaterial::where('course_id', $course->id)->get();

            // Filing status is read straight off each material's own category
            // (CourseMaterial.type), set when it was uploaded — not guessed
            // from the filename.
            $hasSyllabus = $materials->contains(fn($m) => $m->type === 'syllabus');
            $hasTos      = $materials->contains(fn($m) => $m->type === 'tos');
            $hasExamBank = $materials->contains(fn($m) => $m->type === 'exam_bank');

            $done = ($hasSyllabus ? 1 : 0) + ($hasTos ? 1 : 0) + ($hasExamBank ? 1 : 0) + ($materials->count() > 0 ? 1 : 0);

            return [
                'code'            => $course->code,
                'title'           => $course->title,
                'program'         => $course->program,
                'has_syllabus'    => $hasSyllabus,
                'has_tos'         => $hasTos,
                'has_exam_bank'   => $hasExamBank,
                'materials'       => $materials->count(),
                'completion_pct'  => round(($done / 4) * 100),
            ];
        });

        return view('secretary.course-filing', compact('filing', 'program'));
    }
}