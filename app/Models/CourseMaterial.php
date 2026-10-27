<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseMaterial extends Model
{
    protected $fillable = [
        'course_id',
        'uploaded_by',
        'title',
        'type',
        'file_path',
        'file_name',
        'file_type',
        'version',
        'file_size',
        'filing_status',
        'filing_checked_by',
        'filing_checked_at',
        'filing_return_reason',
    ];

    protected $casts = [
        'filing_checked_at' => 'datetime',
    ];

    // Course Filing check by the Secretary; a file with no status is still waiting.
    const FILING_VALIDATED = 'validated';
    const FILING_RETURNED = 'returned';

    // The documents every course is filed with, in the order Course Filing lists them.
    const FILING_DOCUMENTS = [
        'syllabus'          => 'Syllabus',
        'tos'               => 'TOS',
        'exam_bank'         => 'Exam Bank',
        'teaching_material' => 'Teaching Materials',
    ];

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

    const TYPES = [
        'syllabus'         => 'Official Syllabus',
        'obtl'             => 'Official OBTL',
        'tos'              => 'Official TOS',
        'exam_bank'        => 'Standard Exam Bank',
        'teaching_material' => 'Core Teaching Materials',
    ];

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? 'Core Teaching Materials';
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Human-readable file size (e.g. "2.4 MB")
    public function getReadableSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}