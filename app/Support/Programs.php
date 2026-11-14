<?php

namespace App\Support;

// Every program code used across the system. The list itself is managed by the Admin in
// System CMS → Dropdown Lists; DEFAULTS is only used before that table exists.
class Programs
{
    public const DEFAULTS = [
        'BSA'      => 'Bachelor of Science in Accountancy',
        'BSMA'     => 'Bachelor of Science in Management Accounting',
        'BSOA'     => 'Bachelor of Science in Office Administration',
        'BSBA-HRM' => 'Bachelor of Science in Business Administration — Major in Human Resource Management',
        'BSBA-FM'  => 'Bachelor of Science in Business Administration — Major in Financial Management',
        'BSBA-MM'  => 'Bachelor of Science in Business Administration — Major in Marketing Management',
    ];

    // Every known code, hidden ones too — for validation, so records that already use a
    // hidden program can still be saved.
    public static function codes(): array
    {
        return array_keys(self::all());
    }

    // code => full name of the programs shown in dropdowns (hidden ones left out).
    public static function options(): array
    {
        return CmsLists::options(CmsLists::PROGRAMS, self::DEFAULTS);
    }

    public static function label(?string $code): string
    {
        return self::all()[$code] ?? ($code ?: '—');
    }

    // "BSA — Bachelor of Science in Accountancy" — for dropdown option text
    public static function codeAndLabel(string $code): string
    {
        return $code . ' — ' . self::label($code);
    }

    // The chosen programs plus any hidden ones the person already had: hidden programs
    // aren't offered as checkboxes, so saving the form must not drop them.
    public static function keepHidden(array $chosen, array $current): array
    {
        $kept = array_filter($current, fn ($code) => self::isHidden($code));

        return array_values(array_unique(array_merge($chosen, $kept)));
    }

    public static function isHidden(string $code): bool
    {
        return CmsLists::isHidden(CmsLists::PROGRAMS, $code);
    }

    protected static function all(): array
    {
        return CmsLists::options(CmsLists::PROGRAMS, self::DEFAULTS, true);
    }
}
