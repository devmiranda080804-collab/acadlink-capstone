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
        'academic_year' => 'Academic Year',
        'google_email'  => 'Google Email',
    ];

    public static function send(User $user, string $event, array $changes = []): void
    {
        if ($event === 'details' && $changes) {
            Notify::send($user, 'Your account was updated',
                collect($changes)->map(fn ($c) => $c['field'] . ': ' . $c['to'])->implode(' · '),
                Notify::rolePath('/dashboard'), 'account');
        }

        try {
            Mail::to($user->email)->send(new AccountUpdated($user, $event, $changes, auth()->user()));
        } catch (\Throwable $e) {
            report($e);
        }
    }

    // Call right after $user->update(): emails the tracked fields that actually changed.
    // $programs is what User::syncPrograms() returned.
    public static function sendDetailChanges(User $user, ?array $programs = null): void
    {
        $previous = $user->getPrevious();
        $changes = [];
        if ($programs) {
            $changes[] = ['field' => 'Program', 'from' => $programs[0] ?: '—', 'to' => $programs[1] ?: '—'];
        }
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
