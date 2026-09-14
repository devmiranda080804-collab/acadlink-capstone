<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamQuestion extends Model
{
    protected $fillable = [
        'exam_section_id', 'parent_id', 'type', 'topic', 'bloom_level',
        'question_text', 'points', 'options', 'order',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
        ];
    }

    public function section()
    {
        return $this->belongsTo(ExamSection::class, 'exam_section_id');
    }

    public function parent()
    {
        return $this->belongsTo(ExamQuestion::class, 'parent_id');
    }

    // Case Analysis sub-questions under this scenario question
    public function children()
    {
        return $this->hasMany(ExamQuestion::class, 'parent_id')->orderBy('order');
    }
}
