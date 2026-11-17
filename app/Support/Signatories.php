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
            ? User::where('role', 'program_head')->inProgram($program)->whereNull('archived_at')->orderBy('id')->first()
            : null;

        return ['name' => $head?->name, 'title' => 'Program Head' . ($program ? ', ' . $program : '')];
    }

    // One signature per Program Head of these programs. A head of several of them signs
    // once, with every program in the title ("Program Head, BSA / BSMA"); a program with
    // no head yet still gets its own line to sign.
    /** @return list<array{name: ?string, title: string}> */
    public static function programHeads(array $programs): array
    {
        $groups = [];
        foreach (array_values(array_unique(array_filter($programs))) as $program) {
            $head = User::where('role', 'program_head')->inProgram($program)->whereNull('archived_at')->orderBy('id')->first();
            $key = $head ? 'head-' . $head->id : 'none-' . $program;
            $groups[$key]['name'] = $head?->name;
            $groups[$key]['programs'][] = $program;
        }
        if (!$groups) {
            return [self::programHead(null)];
        }

        return array_values(array_map(fn ($g) => [
            'name'  => $g['name'],
            'title' => 'Program Head, ' . implode(' / ', $g['programs']),
        ], $groups));
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
