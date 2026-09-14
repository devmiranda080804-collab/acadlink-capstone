<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseCoordinationController extends Controller
{
    public function index(Request $request)
    {
        $myProgram = auth()->user()->program;

        // Courses limited to the faculty member's own program
        $courses = Course::where('program', $myProgram)->orderBy('code')->get();

        $selectedCourse = null;
        $materials = collect();
        $courseOutcomes = collect();

        if ($request->filled('course_id')) {
            $selectedCourse = Course::where('id', $request->course_id)
                ->where('program', $myProgram) // security: own program only
                ->first();

            if ($selectedCourse) {
                $materials = $selectedCourse->materials()->latest()->get()->groupBy('type');
                $courseOutcomes = $selectedCourse->outcomes()->with('programOutcomes')->get();
            }
        }

        return view('faculty.course-coordination', compact('courses', 'selectedCourse', 'materials', 'courseOutcomes'));
    }
}