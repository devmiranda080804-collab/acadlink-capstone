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

    // Semesters shown in dropdowns, as set in System CMS → Dropdown Lists.
    public static function semesters(): array
    {
        return CmsLists::keys(CmsLists::SEMESTERS, self::SEMESTERS) ?: self::SEMESTERS;
    }

    // Academic years shown in dropdowns, as set in System CMS → Dropdown Lists. The current
    // one is always included, so the present term can always be picked.
    public static function schoolYearOptions(): array
    {
        $years = CmsLists::keys(CmsLists::SCHOOL_YEARS, self::defaultSchoolYears());
        if (!in_array(self::currentSchoolYear(), $years, true)) {
            $years[] = self::currentSchoolYear();
        }
        sort($years);

        return array_values($years);
    }

    // The years the Admin can set the term to in Settings: the same list.
    public static function settingsSchoolYearOptions(): array
    {
        return self::schoolYearOptions();
    }

    // Every academic year on the list, hidden ones too — for validating records that use one.
    public static function allSchoolYears(): array
    {
        return array_values(array_unique(array_merge(
            CmsLists::keys(CmsLists::SCHOOL_YEARS, self::defaultSchoolYears(), true),
            [self::currentSchoolYear()]
        )));
    }

    // What the dropdowns offered before the list was managed: this year and the next.
    protected static function defaultSchoolYears(): array
    {
        $startYear = (int) explode('-', self::calendarSchoolYear())[0];

        return [$startYear . '-' . ($startYear + 1), ($startYear + 1) . '-' . ($startYear + 2)];
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
