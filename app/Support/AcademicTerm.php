<?php

namespace App\Support;

use App\Models\Setting;

// Single source of truth for "what school year / semester is it right now" —
// change the calendar rule here once instead of in every controller that needs it.
// The Admin can set the term by hand in Settings (e.g. when a semester starts early
// or runs late); otherwise it follows the calendar.
class AcademicTerm
{
    const SEMESTERS = ['First Semester', 'Second Semester', 'Summer'];

    const SETTING_MODE = 'term.mode';
    const SETTING_SCHOOL_YEAR = 'term.school_year';
    const SETTING_SEMESTER = 'term.semester';

    const MODE_AUTO = 'auto';
    const MODE_MANUAL = 'manual';

    // Read once per request — these are called from many places on one page.
    protected static ?array $manual = null;
    protected static bool $loaded = false;

    public static function currentSchoolYear(): string
    {
        return self::manualTerm()['school_year'] ?? self::calendarSchoolYear();
    }

    public static function currentSemester(): string
    {
        return self::manualTerm()['semester'] ?? self::calendarSemester();
    }

    public static function calendarSchoolYear(): string
    {
        $now = now();
        $year = $now->year;
        return $now->month >= 8 ? $year . '-' . ($year + 1) : ($year - 1) . '-' . $year;
    }

    public static function calendarSemester(): string
    {
        $month = now()->month;
        if ($month >= 8 && $month <= 12) return 'First Semester';
        if ($month >= 1 && $month <= 5)  return 'Second Semester';
        return 'Summer';
    }

    public static function isManual(): bool
    {
        return self::manualTerm() !== null;
    }

    // Analytics only reports on the present school year.
    public static function selectableSchoolYears(): array
    {
        return [self::currentSchoolYear()];
    }

    // Dropdown options for school-year pickers: the current school year and the
    // next one, ascending. Follows the current term, so it moves forward each
    // school year on its own.
    public static function schoolYearOptions(): array
    {
        $startYear = (int) explode('-', self::currentSchoolYear())[0];

        return [
            $startYear . '-' . ($startYear + 1),
            ($startYear + 1) . '-' . ($startYear + 2),
        ];
    }

    // School years the Admin can choose from: the calendar's one year back to two ahead.
    public static function settingsSchoolYearOptions(): array
    {
        $start = (int) explode('-', self::calendarSchoolYear())[0];

        return array_map(fn($y) => $y . '-' . ($y + 1), range($start - 1, $start + 2));
    }

    public static function isValidSchoolYear(?string $value): bool
    {
        return (bool) preg_match('/^(\d{4})-(\d{4})$/', (string) $value, $m) && (int) $m[2] === (int) $m[1] + 1;
    }

    // Forget the cached term, e.g. right after the Admin changes it.
    public static function flush(): void
    {
        self::$manual = null;
        self::$loaded = false;
    }

    // The Admin's term, or null when it follows the calendar.
    protected static function manualTerm(): ?array
    {
        if (!self::$loaded) {
            self::$loaded = true;
            try {
                $mode = Setting::get(self::SETTING_MODE);
                $schoolYear = Setting::get(self::SETTING_SCHOOL_YEAR);
                $semester = Setting::get(self::SETTING_SEMESTER);
            } catch (\Throwable $e) {
                // Settings table not migrated yet — fall back to the calendar.
                return self::$manual = null;
            }

            self::$manual = ($mode === self::MODE_MANUAL && self::isValidSchoolYear($schoolYear) && in_array($semester, self::SEMESTERS, true))
                ? ['school_year' => $schoolYear, 'semester' => $semester]
                : null;
        }

        return self::$manual;
    }
}
