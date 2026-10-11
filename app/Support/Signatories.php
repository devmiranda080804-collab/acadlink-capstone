<?php

namespace App\Support;

use App\Models\Setting;
use App\Models\User;

// Who signs official documents (e.g. the TOS): the program's Program Head comes from
// their account; the Dean's name and title are set by the Admin in Settings, since the
// Admin account itself isn't the Dean.
class Signatories
{
    const DEAN_NAME = 'signatory.dean_name';
    const DEAN_TITLE = 'signatory.dean_title';
    const DEFAULT_DEAN_TITLE = 'Dean, College of Business Management and Accountancy';

    /** @return array{name: ?string, title: string} */
    public static function programHead(?string $program): array
    {
        $head = $program
            ? User::where('role', 'program_head')->where('program', $program)->whereNull('archived_at')->orderBy('id')->first()
            : null;

        return ['name' => $head?->name, 'title' => 'Program Head' . ($program ? ', ' . $program : '')];
    }

    /** @return array{name: ?string, title: string} */
    public static function dean(): array
    {
        return [
            'name'  => Setting::get(self::DEAN_NAME),
            'title' => Setting::get(self::DEAN_TITLE, self::DEFAULT_DEAN_TITLE),
        ];
    }
}
