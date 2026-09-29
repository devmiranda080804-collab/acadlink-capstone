<?php

namespace App\Support;

// Single source of truth for every program code used across the system —
// add a new program here and it automatically shows up in every dropdown,
// filter, and validation rule that reads from this list.
class Programs
{
    protected const CATALOG = [
        'BSA'      => 'Bachelor of Science in Accountancy',
        'BSMA'     => 'Bachelor of Science in Management Accounting',
        'BSOA'     => 'Bachelor of Science in Office Administration',
        'BSBA-HRM' => 'Bachelor of Science in Business Administration — Major in Human Resource Management',
        'BSBA-FM'  => 'Bachelor of Science in Business Administration — Major in Financial Management',
        'BSBA-MM'  => 'Bachelor of Science in Business Administration — Major in Marketing Management',
    ];

    // Just the codes, e.g. ['BSA', 'BSMA', ...] — for validation rules and loops
    public static function codes(): array
    {
        return array_keys(self::CATALOG);
    }

    // code => full name, e.g. ['BSA' => 'Bachelor of Science in Accountancy', ...] — for dropdowns
    public static function options(): array
    {
        return self::CATALOG;
    }

    public static function label(?string $code): string
    {
        return self::CATALOG[$code] ?? ($code ?: '—');
    }

    // "BSA — Bachelor of Science in Accountancy" — for dropdown option text
    public static function codeAndLabel(string $code): string
    {
        return $code . ' — ' . self::label($code);
    }
}
