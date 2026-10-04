<?php

namespace App\Support;

// Single source of truth for "what school year / semester is it right now" —
// change the calendar rule here once instead of in every controller that needs it.
class AcademicTerm
{
    public static function currentSchoolYear(): string
    {
        $now = now();
        $year = $now->year;
        return $now->month >= 8 ? $year . '-' . ($year + 1) : ($year - 1) . '-' . $year;
    }

    public static function currentSemester(): string
    {
        $month = now()->month;
        if ($month >= 8 && $month <= 12) return 'First Semester';
        if ($month >= 1 && $month <= 5)  return 'Second Semester';
        return 'Summer';
    }

    // The current school year plus the two before it — a small, reasonable
    // set of past terms to pick from in a selector (e.g. Analytics), without
    // needing a query against historical data just to populate the dropdown.
    public static function selectableSchoolYears(): array
    {
        $startYear = (int) explode('-', self::currentSchoolYear())[0];

        return [
            ($startYear) . '-' . ($startYear + 1),
            ($startYear - 1) . '-' . ($startYear),
            ($startYear - 2) . '-' . ($startYear - 1),
        ];
    }

    const SEMESTERS = ['First Semester', 'Second Semester', 'Summer'];
}
