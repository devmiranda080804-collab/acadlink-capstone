<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionRequirement extends Model
{
    protected $fillable = [
        'created_by', 'program', 'title', 'description', 'type', 'deadline',
    ];

    protected $casts = [
        'deadline' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'requirement_id');
    }

    // Number of days left before the due date (negative = overdue)
    public function getDaysLeftAttribute(): int
    {
        return now()->startOfDay()->diffInDays($this->deadline, false);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->deadline->isPast() && !$this->deadline->isToday();
    }

    // Single definition of "due soon" — used across the Submissions pages
    // instead of each one repeating its own "<= 3 days" threshold
    public function getIsDueSoonAttribute(): bool
    {
        return $this->days_left >= 0 && $this->days_left <= 3;
    }

    // How many of this faculty member's own requirements still need urgent
    // attention (overdue or due within 3 days, with nothing submitted yet) —
    // drives the nav badge, same role Announcement::unreadCountFor() plays.
    public static function urgentCountFor(User $user): int
    {
        return static::where('program', $user->program)
            ->get()
            ->filter(function ($req) use ($user) {
                if (!$req->is_overdue && !$req->is_due_soon) {
                    return false;
                }

                return !Submission::where('requirement_id', $req->id)
                    ->where('faculty_id', $user->id)
                    ->exists();
            })
            ->count();
    }
}