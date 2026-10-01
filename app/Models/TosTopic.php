<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TosTopic extends Model
{
    protected $fillable = [
        'tos_id', 'course_topic_id', 'topic', 'hours',
        'weight_percent', 'target_items', 'topic_points', 'levels',
    ];

    protected function casts(): array
    {
        return [
            'levels' => 'array',
        ];
    }

    public function tos()
    {
        return $this->belongsTo(Tos::class);
    }

    public function courseTopic()
    {
        return $this->belongsTo(CourseTopic::class);
    }
}
