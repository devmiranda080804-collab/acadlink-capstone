<?php

namespace App\Models;

use App\Support\AcademicTerm;
use Illuminate\Database\Eloquent\Model;

class SubmissionRequirement extends Model
{
    protected $fillable = [
        'created_by', 'program', 'title', 'description', 'type', 'file_types', 'per_course', 'school_year', 'semester', 'priority', 'deadline',
    ];

    // Highest first — the order lists are sorted in.
    const PRIORITIES = [
        'urgent'    => 'Urgent',
        'important' => 'Important',
        'standard'  => 'Standard',
    ];

    public static function priorityLabel(?string $priority): string
    {
        return self::PRIORITIES[$priority] ?? self::PRIORITIES['standard'];
    }

    public static function priorityRank(?string $priority): int
    {
        $rank = array_search($priority, array_keys(self::PRIORITIES), true);

        return $rank === false ? count(self::PRIORITIES) - 1 : $rank;
    }

    // Urgent first, then Important, then Standard; nearest deadline first within each.
    public function scopeOrderByPriority($query)
    {
        $cases = collect(array_keys(self::PRIORITIES))
            ->map(fn($key, $rank) => "WHEN '{$key}' THEN {$rank}")
            ->implode(' ');

        return $query->orderByRaw("CASE priority {$cases} ELSE " . count(self::PRIORITIES) . ' END')
            ->orderBy('deadline');
    }

    protected $casts = [
        'deadline'   => 'date',
        'per_course' => 'boolean',
        'file_types' => 'array',
    ];

    // File types the Program Head can accept for a requirement.
    const FILE_TYPES = [
        'pdf'        => ['label' => 'PDF',        'extensions' => ['pdf']],
        'word'       => ['label' => 'Word',       'extensions' => ['doc', 'docx']],
        'excel'      => ['label' => 'Excel',      'extensions' => ['xls', 'xlsx']],
        'powerpoint' => ['label' => 'PowerPoint', 'extensions' => ['ppt', 'pptx']],
        'image'      => ['label' => 'Image',      'extensions' => ['jpg', 'jpeg', 'png']],
    ];

    // Used when none is chosen, and by every requirement made before the choice existed.
    const DEFAULT_FILE_TYPES = ['pdf', 'word'];

    public function allowedFileTypes(): array
    {
        $types = array_values(array_intersect(array_keys(self::FILE_TYPES), (array) $this->file_types));

        return $types ?: self::DEFAULT_FILE_TYPES;
    }

    public function allowedExtensions(): array
    {
        return array_merge(...array_map(fn ($t) => self::FILE_TYPES[$t]['extensions'], $this->allowedFileTypes()));
    }

    // "PDF or Word", "PDF, Word or Excel".
    public function fileTypesLabel(): string
    {
        $labels = array_map(fn ($t) => self::FILE_TYPES[$t]['label'], $this->allowedFileTypes());
        $last = array_pop($labels);

        return $labels ? implode(', ', $labels) . ' or ' . $last : $last;
    }

    // For the file picker: ".pdf,.doc,.docx".
    public function acceptAttribute(): string
    {
        return implode(',', array_map(fn ($e) => '.' . $e, $this->allowedExtensions()));
    }

    // The term the requirement belongs to. Older requirements have none and follow the current one.
    public function termSchoolYear(): string
    {
        return $this->school_year ?: AcademicTerm::currentSchoolYear();
    }

    public function termSemester(): string
    {
        return $this->semester ?: AcademicTerm::currentSemester();
    }

    public function termLabel(): string
    {
        return $this->termSemester() . ', A.Y. ' . $this->termSchoolYear();
    }

    // The courses of this requirement's program that have a faculty member assigned in its
    // term, or only $faculty's own courses. These are what a per-course requirement covers.
    public function courses(?User $faculty = null)
    {
        $courseIds = ProgramAssignment::where('school_year', $this->termSchoolYear())
            ->where('semester', $this->termSemester())
            ->when($faculty, fn ($q) => $q->where('faculty_id', $faculty->id))
            ->pluck('course_id');

        return Course::whereIn('id', $courseIds)->where('program', $this->program)->orderBy('code')->get();
    }

    // Whether $faculty still has something to submit: a course of theirs with no file yet
    // (anyone teaching it may submit), or their own file when it is one per faculty member.
    public function isMissingFor(User $faculty): bool
    {
        if (!$this->per_course) {
            return !$this->groupSubmissionFor($faculty);
        }

        $courseIds = $this->courses($faculty)->pluck('id');
        if ($courseIds->isEmpty()) {
            return false;
        }

        return $this->submissions()->whereIn('course_id', $courseIds)->distinct()->count('course_id') < $courseIds->count();
    }

    // Group count for faculty shared by several programs (panel comment #27): the same kind
    // of requirement, for the same term, in the faculty member's other programs. One file
    // sent to any of them counts for all, so a shared faculty member submits once.
    // Per-course requirements are left out: each course already belongs to one program.
    public function counterparts()
    {
        if ($this->per_course || $this->type === 'other') {
            return collect();
        }

        return static::where('id', '!=', $this->id)
            ->where('program', '!=', $this->program)
            ->where('type', $this->type)
            ->where('per_course', false)
            ->get()
            ->filter(fn ($other) => $other->termLabel() === $this->termLabel())
            ->values();
    }

    // The file that counts for $faculty here: their own, or the one they sent for the same
    // kind of requirement in another of their programs.
    public function groupSubmissionFor(User $faculty): ?Submission
    {
        $own = $this->relationLoaded('submissions')
            ? $this->submissions->firstWhere('faculty_id', $faculty->id)
            : $this->submissions()->where('faculty_id', $faculty->id)->first();
        if ($own) {
            return $own;
        }

        $ids = $this->counterparts()->whereIn('program', $faculty->programCodes())->pluck('id');

        return $ids->isEmpty() ? null : Submission::with('requirement')
            ->whereIn('requirement_id', $ids)
            ->where('faculty_id', $faculty->id)
            ->latest('id')
            ->first();
    }

    // Files from other programs that count here, keyed by faculty id: shared faculty of this
    // program who submitted the same kind of requirement elsewhere but not here.
    public function coveringSubmissions()
    {
        $ids = $this->counterparts()->pluck('id');
        if ($ids->isEmpty()) {
            return collect();
        }

        $ownFaculty = $this->submissions()->pluck('faculty_id')->all();

        return Submission::with(['requirement', 'faculty'])
            ->whereIn('requirement_id', $ids)
            ->whereNotIn('faculty_id', $ownFaculty)
            ->latest('id')
            ->get()
            ->filter(fn ($s) => $s->faculty && !$s->faculty->archived_at && $s->faculty->role === 'faculty' && $s->faculty->belongsToProgram($this->program))
            ->unique('faculty_id')
            ->keyBy('faculty_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // The document kinds a requirement can ask for: the same types used for Admin
    // templates, plus the TOS and Exam Bank a course submission usually needs.
    public static function typeOptions(): array
    {
        $options = [];
        foreach (TemplateDocument::types() as $key => $meta) {
            $options[$key] = $meta['label'];
        }

        return $options + ['tos' => 'TOS', 'exam_bank' => 'Exam Bank', 'other' => 'Other'];
    }

    public static function typeLabel(string $type): string
    {
        return self::typeOptions()[$type] ?? TemplateDocument::typeLabel($type);
    }

    public static function typeIcon(string $type): string
    {
        return match ($type) {
            'tos'         => '📊',
            'exam_bank'   => '📝',
            default       => TemplateDocument::typeIcon($type),
        };
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
        return static::whereIn('program', $user->programCodes())
            ->get()
            ->filter(function ($req) use ($user) {
                if (!$req->is_overdue && !$req->is_due_soon) {
                    return false;
                }

                return $req->isMissingFor($user);
            })
            ->count();
    }
}