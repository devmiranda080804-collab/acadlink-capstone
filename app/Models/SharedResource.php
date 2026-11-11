<?php

namespace App\Models;

use App\Models\Concerns\HasEditableFile;
use App\Support\AcademicTerm;
use Illuminate\Database\Eloquent\Model;

class SharedResource extends Model
{
    use HasEditableFile;

    const ONLYOFFICE_SOURCE = 'shared';

    protected $fillable = [
        'shared_by', 'program', 'course_id', 'source_module_id', 'title', 'type', 'description',
        'file_path', 'file_name', 'file_type', 'file_size',
    ];

    protected $casts = [
        'last_edited_at' => 'datetime',
    ];

    // Who shared it, and the faculty teaching its course this term, edit it together. A
    // resource for the whole program is edited by whoever shared it; everyone else views it.
    public function canEdit(User $user): bool
    {
        if (!$this->isEditableFile() || $user->role !== 'faculty') {
            return false;
        }

        return $this->shared_by === $user->id || in_array($this->course_id, self::coursesTaughtBy($user), true);
    }

    public function canView(User $user): bool
    {
        return $user->role === 'admin' || $user->belongsToProgram($this->program);
    }

    // Course ids $user teaches in the current term.
    public static function coursesTaughtBy(User $user): array
    {
        return ProgramAssignment::where('faculty_id', $user->id)
            ->where('school_year', AcademicTerm::currentSchoolYear())
            ->where('semester', AcademicTerm::currentSemester())
            ->pluck('course_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

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

    // "program:BSA" (the whole program) or "course:12" (one course), limited to $programs.
    // Returns ['program' => ..., 'course_id' => ...].
    public static function resolveTarget(?string $target, array $programs): array
    {
        [$kind, $value] = array_pad(explode(':', (string) $target, 2), 2, null);

        if ($kind === 'program' && in_array($value, $programs, true)) {
            return ['program' => $value, 'course_id' => null];
        }

        if ($kind === 'course' && ctype_digit((string) $value)) {
            $course = Course::whereKey((int) $value)->whereIn('program', $programs)->first();
            if ($course) {
                return ['program' => $course->program, 'course_id' => $course->id];
            }
        }

        throw \Illuminate\Validation\ValidationException::withMessages(['target' => 'Choose a program or course you can share to.']);
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