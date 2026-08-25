<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\Template;
use Illuminate\Http\Request;

class CourseFilingController extends Controller
{
    public function index(Request $request)
    {
        $program = $request->get('program');

        $courses = Course::when($program, fn($q) => $q->where('program', $program))
            ->orderBy('program')
            ->orderBy('code')
            ->get();

        // Para sa bawat course, tignan ang filing status
        $filing = $courses->map(function ($course) {
            // Materials ng course na ito
            $materials = CourseMaterial::where('course_id', $course->id)->get();

            // Syllabus: may approved syllabus template sa program, o material na may "syllabus"
            $hasSyllabus = Template::where('program', $course->program)
                ->where('type', 'syllabus')
                ->where('status', 'approved')
                ->exists()
                || $materials->contains(fn($m) => stripos($m->title, 'syllabus') !== false);

            // TOS: material na may "TOS" o "table of specification"
            $hasTos = $materials->contains(fn($m) =>
                stripos($m->title, 'tos') !== false ||
                stripos($m->title, 'specification') !== false
            );

            // Exam Bank: material na may "exam", "bank", o "item"
            $hasExamBank = $materials->contains(fn($m) =>
                stripos($m->title, 'exam') !== false ||
                stripos($m->title, 'bank') !== false ||
                stripos($m->title, 'item') !== false
            );

            return [
                'code'         => $course->code,
                'title'        => $course->title,
                'program'      => $course->program,
                'has_syllabus' => $hasSyllabus,
                'has_tos'      => $hasTos,
                'has_exam_bank'=> $hasExamBank,
                'materials'    => $materials->count(),
            ];
        });

        return view('secretary.course-filing', compact('filing', 'program'));
    }
}