<?php

namespace App\Models;

use App\Models\Concerns\HasEditableFile;
use Illuminate\Database\Eloquent\Model;

class TemplateDocument extends Model
{
    use HasEditableFile;

    const ONLYOFFICE_SOURCE = 'templates';

    protected $fillable = [
        'created_by', 'title', 'type', 'version', 'root_template_id', 'file_path', 'file_name',
        'file_type', 'file_size', 'superseded_at',
        'status', 'reviewed_by', 'reviewed_at', 'return_reason',
    ];

    // Workflow: the Secretary uploads (pending), or edits a new version in the system (draft)
    // and submits it (pending) → the Dean approves and sends it to programs, or returns it
    // to the Secretary with a reason. Only the Secretary ever sees a draft.
    const STATUS_DRAFT    = 'draft';
    const STATUS_PENDING  = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_RETURNED = 'returned';

    // File types a template can be. PDF and Word open in the viewer; the others download.
    const ALLOWED_EXTENSIONS = ['pdf', 'doc', 'docx', 'xlsx', 'pptx'];

    // The kinds of template that can be chosen for a new upload (see TemplateType).
    public static function types(): array
    {
        return array_filter(TemplateType::catalog(), fn ($type) => $type['active']);
    }

    public static function fileRule(): string
    {
        return 'required|file|mimes:' . implode(',', self::ALLOWED_EXTENSIONS) . '|max:20480';
    }

    public static function fileAccept(): string
    {
        return '.' . implode(',.', self::ALLOWED_EXTENSIONS);
    }

    public static function typeLabel(string $type): string
    {
        return TemplateType::catalog()[$type]['label'] ?? str_replace('_', ' ', ucfirst($type));
    }

    public static function typeIcon(string $type): string
    {
        return TemplateType::catalog()[$type]['icon'] ?? '📄';
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
            'reviewed_at'   => 'datetime',
            'superseded_at' => 'datetime',
            'last_edited_at' => 'datetime',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
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
            ->where('status', '!=', self::STATUS_DRAFT)
            ->orderByDesc('version');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', '!=', self::STATUS_DRAFT);
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function canEdit(User $user): bool
    {
        return $user->role === 'secretary' && $this->isDraft() && $this->isEditableFile();
    }

    // A save can still arrive just after the Secretary submits; never after the Dean approves.
    public function acceptsEditorSave(): bool
    {
        return $this->file_path !== null && ($this->isDraft() || $this->isPending());
    }

    // Templates already keep every version as its own record.
    public function keepsVersionHistory(): bool
    {
        return false;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isReturned(): bool
    {
        return $this->status === self::STATUS_RETURNED;
    }

    // Programs the Dean has actually sent this template to (faculty there can see it).
    public function sentPrograms(): array
    {
        return $this->programs->whereNotNull('distributed_at')->pluck('program')->values()->all();
    }

    public function isSent(): bool
    {
        return $this->sentPrograms() !== [];
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

    // The template currently sent by the Dean to a program for a given type — e.g. letting a faculty member jump straight
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

    public function views()
    {
        return $this->hasMany(TemplateDocumentView::class);
    }

    // Every template currently distributed to a faculty member's program,
    // scoped to the ones THEY haven't opened yet — what the "NEW" badge (card
    // + folder + sidebar nav count) is based on. Viewing is recorded by
    // Faculty\TemplateController::markViewed(), fired when they click View/Download.
    public static function newForFaculty(\App\Models\User $user)
    {
        return self::whereNull('superseded_at')
            ->whereHas('programs', fn($p) => $p->whereIn('program', $user->programCodes())->whereNotNull('distributed_at'))
            ->whereDoesntHave('views', fn($v) => $v->where('user_id', $user->id));
    }

    public static function newCountFor(\App\Models\User $user): int
    {
        return self::newForFaculty($user)->count();
    }
}
