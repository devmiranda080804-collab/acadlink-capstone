<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSection extends Model
{
    protected $fillable = ['exam_id', 'title', 'instructions', 'order'];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    // Top-level questions only — Case Analysis sub-questions are reached via ->children
    public function questions()
    {
        return $this->hasMany(ExamQuestion::class)->whereNull('parent_id')->orderBy('order');
    }
}
