<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateDocument extends Model
{
    protected $fillable = [
        'created_by', 'title', 'type', 'version', 'root_template_id', 'file_path', 'file_name',
        'file_type', 'file_size', 'forwarded_by', 'forwarded_at', 'superseded_at',
    ];

    // Single source of truth for every document type the Template Library
    // accepts — covers both instructional formats (syllabus/course guide/
    // module) and the administrative/academic forms (memo, request letter,
    // etc.) the manuscript's Faculty Document and Template Library describes.
    // Feeds the Admin upload form, and every role's folder/card icon + label.
    const TYPES = [
        'syllabus'          => ['label' => 'Syllabus', 'icon' => '📘'],
        'course_guide'      => ['label' => 'Course Guide', 'icon' => '📙'],
        'module'            => ['label' => 'Module', 'icon' => '📝'],
        'research_template' => ['label' => 'Research Template', 'icon' => '🔬'],
        'memorandum'        => ['label' => 'Memorandum', 'icon' => '📰'],
        'request_letter'    => ['label' => 'Request Letter', 'icon' => '✉️'],
        'consultation_form' => ['label' => 'Consultation Form', 'icon' => '🗒️'],
        'activity_proposal' => ['label' => 'Activity Proposal', 'icon' => '📋'],
        'monitoring_sheet'  => ['label' => 'Monitoring Sheet', 'icon' => '📊'],
    ];

    public static function typeLabel(string $type): string
    {
        return self::TYPES[$type]['label'] ?? str_replace('_', ' ', ucfirst($type));
    }

    public static function typeIcon(string $type): string
    {
        return self::TYPES[$type]['icon'] ?? '📄';
    }

    protected static function booted(): void
    {
        // Every version of a template (including its very first) shares one
        // root_template_id, set to its own id right after creation — lets
        // "all versions of this template" be a single flat query instead of
        // walking a previous-version chain.
        static::created(function (TemplateDocument $document) {
            if ($document->root_template_id === null) {
                $document->root_template_id = $document->id;
                $document->saveQuietly();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'forwarded_at'  => 'datetime',
            'superseded_at' => 'datetime',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function forwarder()
    {
        return $this->belongsTo(User::class, 'forwarded_by');
    }

    public function programs()
    {
        return $this->hasMany(TemplateDocumentProgram::class);
    }

    // Every version ever uploaded for this template's lineage, newest first —
    // the actual "version-controlled" history, not just the current row.
    public function allVersions()
    {
        return self::where('root_template_id', $this->root_template_id)
            ->orderByDesc('version');
    }

    public function isForwarded(): bool
    {
        return $this->forwarded_at !== null;
    }

    public function isSuperseded(): bool
    {
        return $this->superseded_at !== null;
    }

    public function programRow(string $program): ?TemplateDocumentProgram
    {
        return $this->programs->firstWhere('program', $program);
    }

    public function isDistributedTo(string $program): bool
    {
        return (bool) $this->programRow($program)?->distributed_at;
    }

    // The template currently distributed (or most recently forwarded) to a
    // program for a given type — e.g. letting a faculty member jump straight
    // from "upload my syllabus" in Course Coordination to the official
    // Syllabus format for their own program, without hunting through the
    // whole library. Only ever the CURRENT (non-superseded) version.
    public static function currentForProgramAndType(string $program, string $type): ?self
    {
        return self::whereNull('superseded_at')
            ->where('type', $type)
            ->whereHas('programs', fn($p) => $p->where('program', $program)->whereNotNull('distributed_at'))
            ->latest()
            ->first();
    }

    public function getReadableSizeAttribute(): ?string
    {
        $bytes = $this->file_size;
        if ($bytes === null) return null;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}
