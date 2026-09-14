<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateElement extends Model
{
    protected $fillable = [
        'template_type', 'label', 'instructions', 'field_type',
        'is_required', 'order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'is_active'   => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForType($query, string $templateType)
    {
        return $query->where('template_type', $templateType);
    }

    // Every template_type value currently in use, in a stable display order
    public static function knownTypes(): array
    {
        $seen = static::query()->distinct()->orderBy('template_type')->pluck('template_type')->toArray();

        $default = ['syllabus', 'lesson_plan', 'course_guide', 'module'];

        return array_values(array_unique(array_merge($default, $seen)));
    }
}
