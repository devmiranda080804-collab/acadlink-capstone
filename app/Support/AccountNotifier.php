<?php

namespace App\Support;

use App\Mail\AccountUpdated;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

// Emails the account owner a confirmation of changes to their account. A mail failure
// (e.g. SMTP down) is reported but never blocks the change itself.
class AccountNotifier
{
    const TRACKED_FIELDS = [
        'name'          => 'Name',
        'role'          => 'Role',
        'program'       => 'Program',
        'academic_year' => 'Academic Year',
        'google_email'  => 'Google Email',
    ];

    public static function send(User $user, string $event, array $changes = []): void
    {
        try {
            Mail::to($user->email)->send(new AccountUpdated($user, $event, $changes, auth()->user()));
        } catch (\Throwable $e) {
            report($e);
        }
    }

    // Call right after $user->update(): emails the tracked fields that actually changed.
    public static function sendDetailChanges(User $user): void
    {
        $previous = $user->getPrevious();
        $changes = [];
        foreach (self::TRACKED_FIELDS as $field => $label) {
            if (!$user->wasChanged($field)) {
                continue;
            }
            $from = self::display($field, $previous[$field] ?? null);
            $to = self::display($field, $user->{$field});
            // e.g. empty string vs null — a change in storage only, not to the person.
            if ($from !== $to) {
                $changes[] = ['field' => $label, 'from' => $from, 'to' => $to];
            }
        }

        if ($changes) {
            self::send($user, 'details', $changes);
        }
    }

    protected static function display(string $field, $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        return $field === 'role' ? ucwords(str_replace('_', ' ', $value)) : (string) $value;
    }
}
