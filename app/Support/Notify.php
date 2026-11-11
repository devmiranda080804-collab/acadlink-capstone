<?php

namespace App\Support;

use App\Models\ProgramAssignment;
use App\Models\User;
use App\Notifications\SystemNotice;

// Sends bell notifications. A failure is reported but never stops the action that caused it.
class Notify
{
    // Each role's pages start here, e.g. '/faculty' . '/submissions'.
    public const ROLE_PREFIX = [
        'admin'        => '/admin',
        'secretary'    => '/secretary',
        'program_head' => '/program-head',
        'faculty'      => '/faculty',
    ];

    // $path is a path, or a function of the user for links that differ by role. $key, when
    // given, sends the notification at most once to each user.
    public static function send(iterable|User|null $users, string $title, string $body, string|callable $path, string $icon = 'bell', ?string $key = null, bool $includeActor = false): void
    {
        if (!$users) {
            return;
        }

        try {
            $users = collect($users instanceof User ? [$users] : $users)
                ->filter(fn ($user) => $user instanceof User && $user->is_active !== false && !$user->archived_at && ($includeActor || $user->id !== auth()->id()))
                ->unique('id');

            foreach ($users as $user) {
                if ($key && $user->notifications()->where('data->key', $key)->exists()) {
                    continue;
                }
                $user->notify(new SystemNotice($title, $body, is_callable($path) ? $path($user) : $path, $icon, $key));
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    // The same page in each role's area, e.g. rolePath('/announcements').
    public static function rolePath(string $page): callable
    {
        return fn (User $user) => (self::ROLE_PREFIX[$user->role] ?? '') . $page;
    }

    public static function admins()
    {
        return User::where('role', 'admin')->whereNull('archived_at')->get();
    }

    public static function secretaries()
    {
        return User::where('role', 'secretary')->whereNull('archived_at')->get();
    }

    public static function programHeads(string|array $programs)
    {
        return User::where('role', 'program_head')->whereNull('archived_at')->inProgram($programs)->get();
    }

    public static function faculty(string|array $programs)
    {
        return User::where('role', 'faculty')->whereNull('archived_at')->inProgram($programs)->get();
    }

    // Faculty teaching a course in the current term.
    public static function courseFaculty(int $courseId)
    {
        $ids = ProgramAssignment::where('course_id', $courseId)
            ->where('school_year', AcademicTerm::currentSchoolYear())
            ->where('semester', AcademicTerm::currentSemester())
            ->pluck('faculty_id');

        return User::whereIn('id', $ids)->whereNull('archived_at')->get();
    }

    public static function actor(): string
    {
        return auth()->user()?->name ?? 'Someone';
    }
}
