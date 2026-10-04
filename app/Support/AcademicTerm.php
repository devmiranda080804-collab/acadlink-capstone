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

    // Analytics only reports on the present school year.
    public static function selectableSchoolYears(): array
    {
        return [self::currentSchoolYear()];
    }

    // Dropdown options for school-year pickers: the current school year and the
    // next one, ascending. Derived from the calendar, so it moves forward each
    // school year on its own.
    public static function schoolYearOptions(): array
    {
        $startYear = (int) explode('-', self::currentSchoolYear())[0];

        return [
            $startYear . '-' . ($startYear + 1),
            ($startYear + 1) . '-' . ($startYear + 2),
        ];
    }

    const SEMESTERS = ['First Semester', 'Second Semester', 'Summer'];
}
