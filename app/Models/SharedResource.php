<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SharedResource extends Model
{
    protected $fillable = [
        'shared_by', 'program', 'course_id', 'source_module_id', 'title', 'type', 'description',
        'file_path', 'file_name', 'file_type', 'file_size',
    ];

    // The four original categories (kept so already-shared files still label correctly),
    // plus the same document types used for Admin templates.
    const LEGACY_TYPES = [
        'lecture_slides'    => 'Lecture Slides',
        'case_study'        => 'Case Study',
        'activity_guide'    => 'Activity Guide',
        'assessment_sample' => 'Assessment Sample',
    ];

    public static function typeOptions(): array
    {
        $templateTypes = collect(TemplateDocument::types())->map(fn($meta) => $meta['label'])->all();

        return self::LEGACY_TYPES + $templateTypes;
    }

    public function getTypeLabelAttribute(): string
    {
        return self::typeOptions()[$this->type]
            ?? (isset(TemplateType::catalog()[$this->type]) ? TemplateDocument::typeLabel($this->type) : 'Lecture Slides');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function sharer()
    {
        return $this->belongsTo(User::class, 'shared_by');
    }

    public function getReadableSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}