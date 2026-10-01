<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index(Request $request, AnalyticsService $analytics)
    {
        $program = $request->query('program') ?: null;
        $programs = Course::query()->select('program')->distinct()->orderBy('program')->pluck('program');

        return view('admin.analytics', [
            'programs'     => $programs,
            'selectedProgram' => $program,
            'compliance'   => $analytics->complianceReport($program),
            'coverage'     => $analytics->assessmentCoverageReport($program),
            'activity'     => $analytics->facultyActivitySummary($program),
            'coordination' => $analytics->courseCoordinationStatusReport($program),
        ]);
    }
}
