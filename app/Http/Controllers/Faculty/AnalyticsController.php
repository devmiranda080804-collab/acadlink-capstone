<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use App\Support\AcademicTerm;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index(Request $request, AnalyticsService $analytics)
    {
        $facultyId = auth()->id();
        $program = auth()->user()->program;
        $schoolYear = $request->query('school_year') ?: AcademicTerm::currentSchoolYear();
        $semester = $request->query('semester') ?: AcademicTerm::currentSemester();

        return view('faculty.analytics', [
            'schoolYear'  => $schoolYear,
            'semester'    => $semester,
            'schoolYears' => AcademicTerm::selectableSchoolYears(),
            'semesters'   => AcademicTerm::SEMESTERS,
            'compliance'   => $analytics->complianceReport($program, $facultyId),
            'coverage'     => $analytics->assessmentCoverageReport($program, $facultyId),
            'activity'     => $analytics->facultyActivitySummary($program, $facultyId, $schoolYear, $semester),
            'coordination' => $analytics->courseCoordinationStatusReport($program, $schoolYear, $semester),
        ]);
    }
}
