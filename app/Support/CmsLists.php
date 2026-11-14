<?php

namespace App\Support;

use App\Models\CmsOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Dropdown lists the Admin manages in System CMS → Dropdown Lists. Records keep the key
// (e.g. "BSA", "2026-2027"); people see the label. Hidden items leave new dropdowns but
// still count as valid on records that already use them.
class CmsLists
{
    const PROGRAMS = 'programs';
    const SCHOOL_YEARS = 'school_years';
    const SEMESTERS = 'semesters';

    const LISTS = [
        self::PROGRAMS => [
            'title'  => 'Programs',
            'hint'   => 'Shown in Account Management, courses, templates, requirements and announcements. The code can\'t change once added, because records keep it.',
            'add'    => true,
            'rename' => true,
        ],
        self::SCHOOL_YEARS => [
            'title'  => 'Academic Years',
            'hint'   => 'The years offered in Account Management, Program Assignment, Course Filing and Settings. Add the next year before it starts.',
            'add'    => true,
            'rename' => false,
        ],
        self::SEMESTERS => [
            'title'  => 'Semesters',
            'hint'   => 'Hide one the college doesn\'t use (for example Summer). The three semesters are fixed, because the calendar and the term rules depend on them.',
            'add'    => false,
            'rename' => false,
        ],
    ];

    protected static array $cache = [];

    // key => label, in order. Hidden items only when $withHidden.
    public static function options(string $list, array $fallback = [], bool $withHidden = false): array
    {
        $rows = self::rows($list);
        if ($rows === null) {
            return $fallback;
        }

        $options = [];
        foreach ($rows as $row) {
            if ($withHidden || $row->is_active) {
                $options[$row->key] = $row->label;
            }
        }

        return $options;
    }

    // Just the keys; $fallback is a plain list of keys.
    public static function keys(string $list, array $fallback = [], bool $withHidden = false): array
    {
        return self::rows($list) === null ? $fallback : array_keys(self::options($list, [], $withHidden));
    }

    public static function isHidden(string $list, string $key): bool
    {
        foreach (self::rows($list) ?? [] as $row) {
            if ($row->key === $key) {
                return !$row->is_active;
            }
        }

        return false;
    }

    public static function flush(): void
    {
        self::$cache = [];
    }

    // How many records use each key, so the Admin sees what a change affects.
    public static function usage(string $list): array
    {
        $counts = [];
        $add = function (string $table, string $column) use (&$counts) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, $column)) {
                return;
            }
            foreach (DB::table($table)->whereNotNull($column)->groupBy($column)->selectRaw("$column as k, count(*) as n")->get() as $row) {
                $counts[$row->k] = ($counts[$row->k] ?? 0) + $row->n;
            }
        };

        match ($list) {
            self::PROGRAMS     => [$add('user_programs', 'program'), $add('courses', 'program')],
            self::SCHOOL_YEARS => [$add('users', 'academic_year'), $add('program_assignments', 'school_year'), $add('submission_requirements', 'school_year')],
            self::SEMESTERS    => [$add('program_assignments', 'semester'), $add('submission_requirements', 'semester')],
            default            => null,
        };

        return $counts;
    }

    // Null when the table isn't there yet (before migrating), so callers use their fallback.
    protected static function rows(string $list): ?array
    {
        if (!array_key_exists($list, self::$cache)) {
            try {
                self::$cache[$list] = CmsOption::where('list', $list)->orderBy('sort_order')->orderBy('id')->get()->all();
            } catch (\Throwable) {
                self::$cache[$list] = null;
            }
        }

        $rows = self::$cache[$list];

        return $rows ? $rows : null;
    }
}
