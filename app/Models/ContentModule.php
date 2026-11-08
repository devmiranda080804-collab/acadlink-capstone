<?php

namespace App\Models;

use App\Models\Concerns\HasEditableFile;
use Illuminate\Database\Eloquent\Model;

class ContentModule extends Model
{
    use HasEditableFile;

    const ONLYOFFICE_SOURCE = 'modules';

    protected $fillable = [
        'created_by', 'source_template_id', 'title', 'description', 'content',
        'google_doc_id', 'file_path', 'file_name', 'file_type', 'file_size',
    ];

    // What a new blank document can be: extension => label and icon.
    const BLANK_TYPES = [
        'docx' => ['label' => 'Document (Word)', 'icon' => '📝'],
        'xlsx' => ['label' => 'Spreadsheet (Excel)', 'icon' => '📊'],
        'pptx' => ['label' => 'Presentation (PowerPoint)', 'icon' => '📽️'],
    ];

    // PDF and old .doc files can be uploaded and read, but not changed.
    const UPLOAD_TYPES = ['docx', 'xlsx', 'pptx', 'pdf', 'doc'];

    protected function casts(): array
    {
        return ['last_edited_at' => 'datetime'];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function shares()
    {
        return $this->hasMany(ContentModuleShare::class);
    }

    // Legacy — new modules no longer create Google Docs (see
    // ContentModuleController::store()), but older rows may still have one.
    public function sourceTemplate()
    {
        return $this->belongsTo(TemplateDocument::class, 'source_template_id');
    }

    // Programs whose library this document can be shared to: the owner's programs, and for a
    // copy of an official template only those the Dean sent the template to.
    public function libraryPrograms(User $user): array
    {
        $programs = $user->programCodes();
        if ($this->source_template_id && $this->sourceTemplate) {
            $programs = array_values(array_intersect($programs, $this->sourceTemplate->sentPrograms()));
        }

        return $programs;
    }

    // Its copy in the Shared Library, when the owner has shared it.
    public function libraryEntry()
    {
        return $this->hasOne(SharedResource::class, 'source_module_id');
    }

    public function isGoogleDoc(): bool
    {
        return $this->google_doc_id !== null;
    }

    // In-app WYSIWYG content, written and edited directly inside AcadLink.
    public function isWritten(): bool
    {
        return $this->google_doc_id === null && $this->file_path === null;
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->created_by === $user->id;
    }

    public function canView(User $user): bool
    {
        return $this->isOwnedBy($user) || $this->shares->contains('user_id', $user->id);
    }

    public function canEdit(User $user): bool
    {
        if (!$this->isEditableFile()) {
            return false;
        }

        return $this->isOwnedBy($user) || $this->shares->contains(fn ($share) => $share->user_id === $user->id && $share->can_edit);
    }

    public function getGoogleEditUrlAttribute(): ?string
    {
        return $this->google_doc_id
            ? "https://docs.google.com/document/d/{$this->google_doc_id}/edit"
            : null;
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
