<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\CourseOutcome;
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

            // OBE alignment: the course must actually have Course Outcomes
            // defined, and every one of them mapped to at least one Program
            // Outcome (Course Oversight's CO-PO mapping) — not just that the
            // Outcomes tab was opened once. An unmapped CO means that part of
            // the course's contribution to the program's outcomes was never
            // actually recorded, so it doesn't count as aligned yet.
            $outcomes = CourseOutcome::where('course_id', $course->id)->with('programOutcomes')->get();
            $hasObeAlignment = $outcomes->isNotEmpty() && $outcomes->every(fn($co) => $co->programOutcomes->isNotEmpty());

            $done = ($hasSyllabus ? 1 : 0) + ($hasTos ? 1 : 0) + ($hasExamBank ? 1 : 0)
                + ($materials->count() > 0 ? 1 : 0) + ($hasObeAlignment ? 1 : 0);

            return [
                'code'              => $course->code,
                'title'             => $course->title,
                'program'           => $course->program,
                'has_syllabus'      => $hasSyllabus,
                'has_tos'           => $hasTos,
                'has_exam_bank'     => $hasExamBank,
                'has_obe_alignment' => $hasObeAlignment,
                'materials'         => $materials->count(),
                'completion_pct'    => round(($done / 5) * 100),
            ];
        });

        return view('secretary.course-filing', compact('filing', 'program'));
    }
}