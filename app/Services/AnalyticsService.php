<?php

namespace App\Services;

use App\Models\ContentModule;
use App\Models\CourseTopic;
use App\Models\Exam;
use App\Models\ProgramAssignment;
use App\Models\SharedResource;
use App\Models\Submission;
use App\Models\SubmissionRequirement;
use App\Models\User;
use App\Support\AcademicTerm;
use App\Support\BloomLevels;

// Real, data-driven implementations of the 4 "Instructional Reports and Analysis" reports
// (Objective 1.d) — replaces what was previously a static page with hardcoded Chart.js
// arrays. Every method takes an optional $program filter (null = every program, for
// Admin/Dean) and/or $facultyId (for a faculty member's own view), so one implementation
// backs the Faculty, Program Head, and Admin analytics pages.
class AnalyticsService
{
    // (1) Instructional Compliance Report — built entirely on the existing Submissions &
    // Deadline data (SubmissionRequirement + Submission), not a new concept.
    public function complianceReport(?string $program = null, ?int $facultyId = null): array
    {
        $requirements = SubmissionRequirement::when($program, fn($q) => $q->where('program', $program))
            ->with(['submissions' => fn($q) => $facultyId ? $q->where('faculty_id', $facultyId) : $q])
            ->get();

        $facultyCountByProgram = [];
        $facultyCount = function (string $prog) use (&$facultyCountByProgram) {
            return $facultyCountByProgram[$prog] ??= User::where('role', 'faculty')
                ->whereNull('archived_at')->where('program', $prog)->count();
        };

        $byType = [];
        $totalExpected = 0;
        $totalSubmitted = 0;
        $totalOnTime = 0;
        $outstanding = [];

        foreach ($requirements as $req) {
            $expectedCount = $facultyId ? 1 : $facultyCount($req->program);
            $submittedIds = $req->submissions->pluck('faculty_id')->all();
            $onTimeCount = $req->submissions->where('status', 'submitted')->count();

            $byType[$req->type] ??= ['expected' => 0, 'submitted' => 0, 'on_time' => 0];
            $byType[$req->type]['expected'] += $expectedCount;
            $byType[$req->type]['submitted'] += count($submittedIds);
            $byType[$req->type]['on_time'] += $onTimeCount;

            $totalExpected += $expectedCount;
            $totalSubmitted += count($submittedIds);
            $totalOnTime += $onTimeCount;

            if (!$facultyId) {
                $missingFaculty = User::where('role', 'faculty')->whereNull('archived_at')
                    ->where('program', $req->program)
                    ->whereNotIn('id', $submittedIds)
                    ->get(['id', 'name']);
                foreach ($missingFaculty as $f) {
                    $outstanding[] = [
                        'faculty'     => $f->name,
                        'requirement' => $req->title,
                        'type'        => $req->type,
                        'deadline'    => $req->deadline?->format('Y-m-d'),
                        'is_overdue'  => $req->is_overdue,
                    ];
                }
            }
        }

        return [
            'total_expected'  => $totalExpected,
            'total_submitted' => $totalSubmitted,
            'compliance_rate' => $totalExpected > 0 ? round($totalSubmitted / $totalExpected * 100, 1) : 0,
            'on_time_rate'    => $totalExpected > 0 ? round($totalOnTime / $totalExpected * 100, 1) : 0,
            'by_type'         => $byType,
            'outstanding'     => $outstanding,
        ];
    }

    // (2) Assessment Coverage Report — actual Bloom's-level item counts (from finalized
    // exams' real ExamQuestion.bloom_level, via Exam::tosBreakdown()) vs. each course's own
    // TOS target for the same grading period (via CourseTopic::targetBreakdown()).
    public function assessmentCoverageReport(?string $program = null, ?int $facultyId = null): array
    {
        $exams = Exam::where('status', 'finalized')
            ->whereNotNull('target_items')
            ->with(['sections.questions.children', 'programAssignment.course'])
            ->when($facultyId, fn($q) => $q->where('faculty_id', $facultyId))
            ->when($program, fn($q) => $q->whereHas('programAssignment.course', fn($c) => $c->where('program', $program)))
            ->get();

        $actualByLevel = array_fill_keys(BloomLevels::LEVELS, 0);
        $targetByLevel = array_fill_keys(BloomLevels::LEVELS, 0);
        $perCourse = [];

        foreach ($exams as $exam) {
            $actual = $exam->tosBreakdown();
            foreach ($actual['topics'] as $topicData) {
                foreach ($topicData['bloom_breakdown'] as $level => $data) {
                    if (isset($actualByLevel[$level])) {
                        $actualByLevel[$level] += $data['count'];
                    }
                }
            }

            $courseId = $exam->programAssignment->course_id;
            $target = CourseTopic::targetBreakdown($courseId, $exam->grading_period, (int) $exam->target_items);
            foreach ($target['topics'] as $t) {
                foreach (BloomLevels::LEVELS as $level) {
                    $targetByLevel[$level] += $t['levels'][$level]['count'];
                }
            }

            $courseTitle = $exam->programAssignment->course->code ?? 'Unknown';
            $perCourse[$courseTitle] ??= ['actual' => 0, 'target' => 0];
            $perCourse[$courseTitle]['actual'] += $actual['total_items'];
            $perCourse[$courseTitle]['target'] += $target['total_items'];
        }

        return [
            'by_level' => collect(BloomLevels::LEVELS)->mapWithKeys(fn($l) => [$l => [
                'actual' => $actualByLevel[$l], 'target' => $targetByLevel[$l],
            ]])->all(),
            'per_course'     => $perCourse,
            'exams_analyzed' => $exams->count(),
        ];
    }

    // (3) Faculty Activity Summary — per-faculty counts of real activity for a term
    // (defaults to the current one when not given, so existing callers are unaffected).
    public function facultyActivitySummary(?string $program = null, ?int $facultyId = null, ?string $schoolYear = null, ?string $semester = null): array
    {
        $schoolYear ??= AcademicTerm::currentSchoolYear();
        $semester ??= AcademicTerm::currentSemester();

        $facultyQuery = User::where('role', 'faculty')->whereNull('archived_at')
            ->when($program, fn($q) => $q->where('program', $program))
            ->when($facultyId, fn($q) => $q->where('id', $facultyId));

        $rows = [];
        foreach ($facultyQuery->get(['id', 'name']) as $faculty) {
            $assignmentIds = ProgramAssignment::where('school_year', $schoolYear)
                ->where('semester', $semester)
                ->where('faculty_id', $faculty->id)
                ->pluck('id');

            $rows[] = [
                'faculty'           => $faculty->name,
                'exams_created'     => Exam::whereIn('program_assignment_id', $assignmentIds)->count(),
                'exams_finalized'   => Exam::whereIn('program_assignment_id', $assignmentIds)->where('status', 'finalized')->count(),
                'topics_filed'      => CourseTopic::where('created_by', $faculty->id)->count(),
                'content_modules'   => ContentModule::where('created_by', $faculty->id)->count(),
                'shared_resources'  => SharedResource::where('shared_by', $faculty->id)->count(),
                'submissions_filed' => Submission::where('faculty_id', $faculty->id)->count(),
            ];
        }

        return ['school_year' => $schoolYear, 'semester' => $semester, 'faculty' => $rows];
    }

    // (4) Course Coordination Status Report — courses with 2+ faculty assigned in a term
    // (multi-section), and whether each assigned faculty has finalized their exam per period.
    // Defaults to the current term when not given, so existing callers are unaffected.
    public function courseCoordinationStatusReport(?string $program = null, ?string $schoolYear = null, ?string $semester = null): array
    {
        $schoolYear ??= AcademicTerm::currentSchoolYear();
        $semester ??= AcademicTerm::currentSemester();

        $byCourse = ProgramAssignment::where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->with(['course', 'faculty'])
            ->when($program, fn($q) => $q->whereHas('course', fn($c) => $c->where('program', $program)))
            ->get()
            ->groupBy('course_id');

        $rows = [];
        foreach ($byCourse as $group) {
            if ($group->count() < 2) continue; // only multi-section courses are a coordination concern

            $course = $group->first()->course;
            $periods = [];
            foreach (['Prelim', 'Midterm', 'Final'] as $period) {
                $finalizedCount = 0;
                foreach ($group as $assignment) {
                    $finalizedCount += Exam::where('program_assignment_id', $assignment->id)
                        ->where('grading_period', $period)
                        ->where('status', 'finalized')
                        ->exists() ? 1 : 0;
                }
                $periods[$period] = ['finalized' => $finalizedCount, 'of' => $group->count()];
            }

            $rows[] = [
                'course'  => $course ? ($course->code . ' — ' . $course->title) : 'Unknown',
                'faculty' => $group->pluck('faculty.name')->all(),
                'periods' => $periods,
            ];
        }

        return ['school_year' => $schoolYear, 'semester' => $semester, 'courses' => $rows];
    }
}
