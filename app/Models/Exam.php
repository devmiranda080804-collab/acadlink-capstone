<?php

namespace App\Models;

use App\Support\BloomLevels;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'program_assignment_id', 'faculty_id', 'title', 'grading_period',
        'duration_minutes', 'target_items', 'status', 'finalized_at',
        'export_file_path', 'export_file_type',
        'print_font', 'print_font_size', 'print_paper', 'print_layout',
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

        // The exam must match its TOS: every question has a topic, and every topic has
        // exactly the number of items the TOS sets (Panel: finalize is blocked until the
        // target item count is met).
        $counts = [];
        $untagged = 0;
        foreach ($this->sections as $section) {
            self::tallyTopics($section->questions, $counts, $untagged);
        }

        return array_merge(
            $reasons,
            $this->tosCountIssues($counts, $untagged, true),
            \App\Support\QuestionSimilarity::exactDuplicateIssues(\App\Support\QuestionSimilarity::examItems($this->sections))
        );
    }

    // Counts the items per topic in a list of questions (models or the builder's
    // payload arrays). Every real question counts, Case Analysis sub-questions included;
    // the Case Analysis container itself is not an item.
    public static function tallyTopics(iterable $questions, array &$counts, int &$untagged): void
    {
        foreach ($questions as $question) {
            if (data_get($question, 'type') !== 'case-analysis') {
                $topic = data_get($question, 'topic');
                if ($topic) {
                    $counts[$topic] = ($counts[$topic] ?? 0) + 1;
                } else {
                    $untagged++;
                }
            }

            self::tallyTopics(data_get($question, 'children') ?? [], $counts, $untagged);
        }
    }

    // The Bloom's levels each section covers, by section index, when the exam's sections
    // line up with its TOS tests (one section per test, as "Start Exam from this TOS"
    // creates them). Empty when they don't, so levels resolve across the whole topic.
    public function sectionLevels(int $sectionCount): array
    {
        $parts = Tos::where('program_assignment_id', $this->program_assignment_id)
            ->where('grading_period', $this->grading_period)
            ->value('parts');
        $parts = is_string($parts) ? json_decode($parts, true) : $parts;

        if (!is_array($parts) || count($parts) < 2 || count($parts) !== $sectionCount || empty($parts[0]['levels'])) {
            return [];
        }

        return array_map(fn($part) => $part['levels'] ?? null, array_values($parts));
    }

    // Items the TOS sets for each topic (topic name => count), or [] when the exam has
    // no target item count to check against.
    public function topicTargets(): array
    {
        if (!$this->target_items) {
            return [];
        }

        $breakdown = CourseTopic::targetBreakdown(
            $this->programAssignment->course_id,
            $this->grading_period,
            (int) $this->target_items
        );

        return collect($breakdown['topics'])->mapWithKeys(fn($t) => [$t['topic'] => (int) $t['target_items']])->all();
    }

    // The finished TOS: each topic's hours, % and items, plus the Bloom's level of every
    // question the instructor actually put in the exam, with the item numbers the printed
    // exam uses. "issues" says what is still missing; the TOS is ready when it is empty.
    public function finalTos(): array
    {
        $breakdown = CourseTopic::targetBreakdown(
            $this->programAssignment->course_id,
            $this->grading_period,
            (int) $this->target_items
        );

        $counts = [];
        $untagged = 0;
        foreach ($this->sections as $section) {
            self::tallyTopics($section->questions, $counts, $untagged);
        }
        $issues = $this->tosCountIssues($counts, $untagged, true);
        if (empty($breakdown['topics'])) {
            $issues[] = 'This exam has no TOS topics yet. Generate the TOS for this subject and grading period first.';
        }

        // Numbered like ExamDocumentBuilder: continuous across the sections, with a
        // sub-question printed under its question's number as "12.a".
        $cells = [];
        $unclassified = 0;
        $add = function ($question, $label) use (&$cells, &$unclassified) {
            if (!$question->bloom_level) {
                $unclassified++;

                return;
            }
            $cell = &$cells[$question->topic][$question->bloom_level];
            $cell['labels'][] = $label;
            $cell['points'] = ($cell['points'] ?? 0) + (int) $question->points;
        };

        $number = 0;
        foreach ($this->sections as $section) {
            foreach ($section->questions as $question) {
                $number++;
                foreach ($question->children as $i => $child) {
                    $add($child, $number . '.' . chr(97 + $i));
                }
                if ($question->children->isEmpty() && $question->type !== 'case-analysis') {
                    $add($question, $number);
                }
            }
        }
        if ($unclassified > 0) {
            $issues[] = $unclassified === 1
                ? "1 question has no Bloom's level yet. Choose its level."
                : "{$unclassified} questions have no Bloom's level yet. Choose a level for each one.";
        }

        $totalPoints = 0;
        $breakdown['topics'] = array_map(function ($topic) use ($cells, &$totalPoints) {
            $topic['topic_points'] = 0;
            foreach (BloomLevels::LEVELS as $level) {
                $cell = $cells[$topic['topic']][$level] ?? null;
                $topic['levels'][$level] = [
                    'count'           => $cell ? count($cell['labels']) : 0,
                    'range'           => $cell ? self::itemRange($cell['labels']) : null,
                    'points'          => $cell['points'] ?? 0,
                    'points_per_item' => 1,
                ];
                $topic['topic_points'] += $cell['points'] ?? 0;
            }
            $totalPoints += $topic['topic_points'];

            return $topic;
        }, $breakdown['topics']);

        unset($breakdown['parts']);
        $breakdown['total_points'] = $totalPoints;
        $breakdown['issues'] = $issues;

        return $breakdown;
    }

    // [1, 2, 3, 7, '9.a'] -> "1-3, 7, 9.a"
    protected static function itemRange(array $labels): string
    {
        $ranges = [];
        $start = $previous = null;
        $close = function () use (&$ranges, &$start, &$previous) {
            if ($start !== null) {
                $ranges[] = $start === $previous ? (string) $start : "{$start}-{$previous}";
            }
            $start = $previous = null;
        };

        foreach ($labels as $label) {
            if (!is_int($label)) {
                $close();
                $ranges[] = $label;
            } elseif ($previous !== null && $label === $previous + 1) {
                $previous = $label;
            } else {
                $close();
                $start = $previous = $label;
            }
        }
        $close();

        return implode(', ', $ranges);
    }

    // Plain-language problems with the item count per topic. Too many items, or a
    // topic outside the TOS, is never allowed; missing items and questions without a
    // topic only block finalizing, so a draft can still be saved while in progress.
    public function tosCountIssues(array $counts, int $untagged, bool $forFinalize): array
    {
        $targets = $this->topicTargets();
        if (empty($targets)) {
            return [];
        }

        $issues = [];
        foreach ($counts as $topic => $have) {
            if (!array_key_exists($topic, $targets)) {
                $issues[] = "\"{$topic}\" is not one of the topics in this exam's TOS. Choose a TOS topic for its " . ($have === 1 ? 'question' : "{$have} questions") . '.';
            } elseif ($have > $targets[$topic]) {
                $extra = $have - $targets[$topic];
                $issues[] = "\"{$topic}\" has {$have} questions, but the TOS sets {$targets[$topic]}. Remove {$extra}.";
            }
        }

        if ($forFinalize) {
            foreach ($targets as $topic => $need) {
                $have = $counts[$topic] ?? 0;
                if ($have < $need) {
                    $missing = $need - $have;
                    $issues[] = "\"{$topic}\" has {$have} of {$need} questions. Add {$missing} more.";
                }
            }
            if ($untagged > 0) {
                $issues[] = $untagged === 1
                    ? '1 question has no topic yet. Choose its topic.'
                    : "{$untagged} questions have no topic yet. Choose a topic for each one.";
            }
        }

        return $issues;
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
