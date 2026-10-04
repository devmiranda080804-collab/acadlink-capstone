<?php

namespace App\Http\Controllers\ProgramHead;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use App\Support\AcademicTerm;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index(Request $request, AnalyticsService $analytics)
    {
        $program = auth()->user()->program;
        $schoolYear = $request->query('school_year') ?: AcademicTerm::currentSchoolYear();
        $semester = $request->query('semester') ?: AcademicTerm::currentSemester();

        return view('program-head.analytics', [
            'schoolYear'  => $schoolYear,
            'semester'    => $semester,
            'schoolYears' => AcademicTerm::selectableSchoolYears(),
            'semesters'   => AcademicTerm::SEMESTERS,
            'compliance'   => $analytics->complianceReport($program),
            'coverage'     => $analytics->assessmentCoverageReport($program),
            'activity'     => $analytics->facultyActivitySummary($program, null, $schoolYear, $semester),
            'coordination' => $analytics->courseCoordinationStatusReport($program, $schoolYear, $semester),
        ]);
    }
}
