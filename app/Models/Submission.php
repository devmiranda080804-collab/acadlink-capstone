<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = [
        'requirement_id', 'faculty_id', 'course_id', 'file_path', 'file_name',
        'file_type', 'file_size', 'file_hash', 'status', 'submitted_at',
        'filing_status', 'filing_checked_by', 'filing_checked_at', 'filing_return_reason',
    ];

    protected $casts = [
        'submitted_at'      => 'datetime',
        'filing_checked_at' => 'datetime',
    ];

    // The Secretary's check in Course Filing; empty means it is still waiting.
    const FILING_VALIDATED = 'validated';
    const FILING_RETURNED = 'returned';

    public function requirement()
    {
        return $this->belongsTo(SubmissionRequirement::class, 'requirement_id');
    }

    public function faculty()
    {
        return $this->belongsTo(User::class, 'faculty_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function checker()
    {
        return $this->belongsTo(User::class, 'filing_checked_by');
    }

    public function getIsValidatedAttribute(): bool
    {
        return $this->filing_status === self::FILING_VALIDATED;
    }

    public function getIsReturnedAttribute(): bool
    {
        return $this->filing_status === self::FILING_RETURNED;
    }

    public function getReadableSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}
