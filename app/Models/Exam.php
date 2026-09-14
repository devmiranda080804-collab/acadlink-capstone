<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'program_assignment_id', 'faculty_id', 'title', 'grading_period',
        'duration_minutes', 'status', 'finalized_at',
        'export_file_path', 'export_file_type',
    ];

    protected function casts(): array
    {
        return [
            'finalized_at' => 'datetime',
        ];
    }

    public function programAssignment()
    {
        return $this->belongsTo(ProgramAssignment::class);
    }

    public function faculty()
    {
        return $this->belongsTo(User::class, 'faculty_id');
    }

    public function sections()
    {
        return $this->hasMany(ExamSection::class)->orderBy('order');
    }

    public function isFinalized(): bool
    {
        return $this->status === 'finalized';
    }

    // Live TOS tally — grouped by topic and Bloom's level, computed from whatever
    // questions the faculty has actually added (no separate manually-entered TOS data)
    public function tosBreakdown(): array
    {
        $questions = $this->sections
            ->flatMap(fn($section) => $section->questions)
            ->flatMap(fn($question) => $question->children->isNotEmpty()
                ? $question->children
                : collect([$question]));

        $totalPoints = $questions->sum('points');

        $byTopic = $questions->groupBy(fn($q) => $q->topic ?? 'Untitled Topic')
            ->map(function ($topicQuestions) use ($totalPoints) {
                $topicPoints = $topicQuestions->sum('points');

                return [
                    'points'        => $topicPoints,
                    'weight_percent' => $totalPoints > 0 ? round($topicPoints / $totalPoints * 100, 1) : 0,
                    'bloom_breakdown' => $topicQuestions->groupBy(fn($q) => $q->bloom_level ?? 'unclassified')
                        ->map(fn($g) => ['count' => $g->count(), 'points' => $g->sum('points')]),
                ];
            });

        return [
            'total_points' => $totalPoints,
            'total_items'  => $questions->count(),
            'topics'       => $byTopic,
        ];
    }
}
