<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'program_assignment_id', 'faculty_id', 'title', 'grading_period',
        'duration_minutes', 'target_items', 'status', 'finalized_at',
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

    // What's still missing before this exam can be finalized — empty array means
    // it's ready. Checked instead of just "has questions", since a finalized exam
    // can no longer be deleted (see ExamGeneratorController::destroy()), so an
    // accidentally-finalized half-built exam would otherwise be stuck forever.
    public function incompletenessReasons(): array
    {
        $reasons = [];

        if ($this->sections->isEmpty()) {
            return ['Add at least one section.'];
        }

        foreach ($this->sections as $section) {
            if ($section->questions->isEmpty()) {
                $reasons[] = '"' . $section->title . '" has no questions yet.';
                continue;
            }

            foreach ($section->questions as $i => $question) {
                $label = '"' . $section->title . '" #' . ($i + 1);
                $reasons = array_merge($reasons, $this->questionIncompletenessReasons($question, $label));
            }
        }

        return $reasons;
    }

    protected function questionIncompletenessReasons(ExamQuestion $question, string $label): array
    {
        $reasons = [];

        if (trim((string) $question->question_text) === '') {
            $reasons[] = "{$label}: question text is empty.";
        }

        $options = $question->options ?? [];

        switch ($question->type) {
            case 'mc-single':
                $choices = array_filter($options['choices'] ?? [], fn($c) => trim((string) $c) !== '');
                if (count($choices) < 2) {
                    $reasons[] = "{$label}: needs at least 2 filled-in choices.";
                }
                $correct = $options['correct'] ?? null;
                if (!is_int($correct) || trim((string) ($options['choices'][$correct] ?? '')) === '') {
                    $reasons[] = "{$label}: no correct choice selected.";
                }
                break;

            case 'identification':
            case 'problem-solving':
                if (trim((string) ($options['answer'] ?? '')) === '') {
                    $reasons[] = "{$label}: answer is empty.";
                }
                break;

            case 'enumeration':
                $answers = array_filter($options['answers'] ?? [], fn($a) => trim((string) $a) !== '');
                if (count($answers) < 1) {
                    $reasons[] = "{$label}: needs at least one answer.";
                }
                break;

            // 'true-false' is always complete (a toggle always has a value) and
            // 'short-answer' (Essay) has no single fixed answer to require.
        }

        return $reasons;
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
                    'items'         => $topicQuestions->count(),
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
