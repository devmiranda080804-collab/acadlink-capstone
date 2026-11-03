<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// The kinds of template the college uses. The Admin manages them in Settings, so a new kind
// of document never needs a code change. Hidden types leave the upload pickers, but their
// existing templates keep their name and folder.
class TemplateType extends Model
{
    protected $fillable = ['key', 'label', 'icon', 'description', 'is_active'];

    // Parts of the system look these up by key: the faculty dashboard counts and the official
    // Course Guide in Course Coordination. They can be renamed, but never hidden or deleted.
    const PROTECTED_KEYS = ['syllabus', 'course_guide', 'module'];

    // Keys that already mean something else in the type pickers built on this list.
    const RESERVED_KEYS = ['tos', 'exam_bank', 'obtl', 'other', 'lecture_slides', 'case_study', 'activity_guide', 'assessment_sample'];

    const ICONS = ['📄', '📘', '📙', '📝', '🔬', '📰', '✉️', '🗒️', '📋', '📊', '📁', '📑', '📈', '📅', '🧾', '🎓'];

    // Where a type's key is stored: table => column.
    const USED_IN = [
        'template_documents'      => 'type',
        'repository_documents'    => 'doc_type',
        'shared_resources'        => 'type',
        'submission_requirements' => 'type',
    ];

    protected static ?array $catalog = null;

    protected static function booted(): void
    {
        static::saved(fn () => self::$catalog = null);
        static::deleted(fn () => self::$catalog = null);
    }

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    // Every type, hidden ones included: key => [label, icon, description, active].
    public static function catalog(): array
    {
        return self::$catalog ??= self::orderBy('id')->get()->mapWithKeys(fn (self $type) => [
            $type->key => [
                'label'       => $type->label,
                'icon'        => $type->icon,
                'description' => $type->description,
                'active'      => $type->is_active,
            ],
        ])->all();
    }

    public static function keyFor(string $label): string
    {
        return Str::limit(Str::slug($label, '_'), 80, '');
    }

    public function isProtected(): bool
    {
        return in_array($this->key, self::PROTECTED_KEYS, true);
    }

    // How many records use each type: key => count.
    public static function usageCounts(): array
    {
        $counts = [];
        foreach (self::USED_IN as $table => $column) {
            foreach (DB::table($table)->select($column, DB::raw('count(*) as total'))->groupBy($column)->get() as $row) {
                $counts[$row->{$column}] = ($counts[$row->{$column}] ?? 0) + $row->total;
            }
        }

        return $counts;
    }

    public function isInUse(): bool
    {
        foreach (self::USED_IN as $table => $column) {
            if (DB::table($table)->where($column, $this->key)->exists()) {
                return true;
            }
        }

        return false;
    }
}
