<?php

namespace App\Http\Controllers\ProgramHead;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;

class AnalyticsController extends Controller
{
    public function index(AnalyticsService $analytics)
    {
        $program = auth()->user()->program;

        return view('program-head.analytics', [
            'compliance'   => $analytics->complianceReport($program),
            'coverage'     => $analytics->assessmentCoverageReport($program),
            'activity'     => $analytics->facultyActivitySummary($program),
            'coordination' => $analytics->courseCoordinationStatusReport($program),
        ]);
    }
}
