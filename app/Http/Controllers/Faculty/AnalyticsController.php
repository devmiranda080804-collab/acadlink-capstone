<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;

class AnalyticsController extends Controller
{
    public function index(AnalyticsService $analytics)
    {
        $facultyId = auth()->id();
        $program = auth()->user()->program;

        return view('faculty.analytics', [
            'compliance'   => $analytics->complianceReport($program, $facultyId),
            'coverage'     => $analytics->assessmentCoverageReport($program, $facultyId),
            'activity'     => $analytics->facultyActivitySummary($program, $facultyId),
            'coordination' => $analytics->courseCoordinationStatusReport($program),
        ]);
    }
}
