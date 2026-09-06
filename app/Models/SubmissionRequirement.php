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

    // Ilang araw na lang bago mag-due (negative = late na)
    public function getDaysLeftAttribute(): int
    {
        return now()->startOfDay()->diffInDays($this->deadline, false);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->deadline->isPast() && !$this->deadline->isToday();
    }
}