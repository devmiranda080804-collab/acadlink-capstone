<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = ['user_id', 'title', 'body', 'expires_at'];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function programs()
    {
        return $this->hasMany(AnnouncementProgram::class);
    }

    // Users who have already viewed this announcement
    public function readers()
    {
        return $this->belongsToMany(User::class, 'announcement_reads')
            ->withPivot('read_at')
            ->withTimestamps(false);
    }

    // Helper: get the list of program codes (e.g. ['BSA', 'BSMA'])
    public function getProgramListAttribute(): array
    {
        return $this->programs->pluck('program')->toArray();
    }

    // Excludes announcements that have already expired
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }

    // Same per-role visibility rules used across the Announcements pages
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return match ($user->role) {
            'admin', 'secretary' => $query,
            'program_head' => $query->where(function ($q) use ($user) {
                $q->where(function ($q2) use ($user) {
                    $q2->whereHas('user', fn($u) => $u->whereIn('role', ['admin', 'secretary']))
                       ->whereHas('programs', fn($p) => $p->where('program', $user->program));
                })->orWhere('user_id', $user->id);
            }),
            'faculty' => $query->whereHas('programs', fn($p) => $p->where('program', $user->program)),
            default => $query->whereRaw('0 = 1'),
        };
    }

    // Announcements visible to this user that they haven't opened yet
    public function scopeUnreadBy(Builder $query, User $user): Builder
    {
        return $query->whereDoesntHave('readers', fn($q) => $q->where('user_id', $user->id));
    }

    public static function unreadCountFor(User $user): int
    {
        return static::active()->visibleTo($user)->unreadBy($user)->count();
    }

    // Marks every announcement in the given collection as read by this user
    public static function markReadBy(\Illuminate\Support\Collection $announcements, User $user): void
    {
        $ids = $announcements->pluck('id');
        if ($ids->isEmpty()) {
            return;
        }

        $alreadyRead = \Illuminate\Support\Facades\DB::table('announcement_reads')
            ->where('user_id', $user->id)
            ->whereIn('announcement_id', $ids)
            ->pluck('announcement_id');

        $unreadIds = $ids->diff($alreadyRead);

        if ($unreadIds->isNotEmpty()) {
            $now = now();
            \Illuminate\Support\Facades\DB::table('announcement_reads')->insert(
                $unreadIds->map(fn($id) => [
                    'announcement_id' => $id,
                    'user_id'         => $user->id,
                    'read_at'         => $now,
                ])->values()->toArray()
            );
        }
    }
}