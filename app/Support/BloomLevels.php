<?php

namespace App\Support;

// Single source of truth for question types + their auto-assigned Bloom's Taxonomy
// level (Capstone panel comment #11: Bloom's Level must be auto-filled by predefined
// logic, manual input must be prevented). Used by the "Choose Question Type" modal,
// the server-side auto-classification in ExamGeneratorController, and the TOS
// Generator's LOTS/HOTS split.
class BloomLevels
{
    // 'case-analysis' is a scenario container only — it carries no points/Bloom level
    // of its own; its child questions (added separately, of any other type below) are
    // classified normally. This is what gives Case Analysis its sub-questions.
    const TYPES = [
        'mc-single' => [
            'label' => 'Multiple Choice (Single)',
            'desc'  => 'Single correct answer from multiple options',
            'icon'  => '◎',
            'bloom' => 'Understanding',
            'category' => 'LOTS',
        ],
        'true-false' => [
            'label' => 'True or False',
            'desc'  => 'Single true/false question',
            'icon'  => '✓',
            'bloom' => 'Remembering',
            'category' => 'LOTS',
        ],
        'modified-true-false' => [
            'label' => 'Modified True or False',
            'desc'  => 'True/False — if false, student supplies the correct term',
            'icon'  => '✓',
            'bloom' => 'Understanding',
            'category' => 'LOTS',
        ],
        'identification' => [
            'label' => 'Identification',
            'desc'  => 'Identify the correct term or concept',
            'icon'  => '◎',
            'bloom' => 'Remembering',
            'category' => 'LOTS',
        ],
        'enumeration' => [
            'label' => 'Enumeration',
            'desc'  => 'List of answers',
            'icon'  => '≡',
            'bloom' => 'Remembering',
            'category' => 'LOTS',
        ],
        'fill-blank' => [
            'label' => 'Fill in the Blank',
            'desc'  => 'Each blank filled with frames',
            'icon'  => 'T',
            'bloom' => 'Remembering',
            'category' => 'LOTS',
        ],
        'matching' => [
            'label' => 'Matching Type',
            'desc'  => 'Matching items from two columns',
            'icon'  => '⇄',
            'bloom' => 'Understanding',
            'category' => 'LOTS',
        ],
        'ordering' => [
            'label' => 'Ordering / Sequencing',
            'desc'  => 'Arrange items in order',
            'icon'  => '↕',
            'bloom' => 'Applying',
            'category' => 'HOTS',
        ],
        'diagram' => [
            'label' => 'Label the Diagram',
            'desc'  => 'Single text, label with frames',
            'icon'  => '🖼',
            'bloom' => 'Applying',
            'category' => 'HOTS',
        ],
        'problem-solving' => [
            'label' => 'Problem Solving / Computation',
            'desc'  => 'Numeric problem with shown solution steps',
            'icon'  => '∑',
            'bloom' => 'Applying',
            'category' => 'HOTS',
        ],
        'short-answer' => [
            'label' => 'Short Answer / Essay',
            'desc'  => 'Longform text answer',
            'icon'  => 'T',
            'bloom' => 'Analyzing',
            'category' => 'HOTS',
        ],
        'case-analysis' => [
            'label' => 'Case Analysis',
            'desc'  => 'Scenario with its own sub-questions of any type',
            'icon'  => '📄',
            'bloom' => null,
            'category' => null,
        ],
    ];

    // Panel comment #9: exact HOTS/LOTS ratio still pending client confirmation.
    // Common DepEd/CHED TOS convention used as the default until then.
    const LOTS_PERCENT = 60;
    const HOTS_PERCENT = 40;

    public static function bloomFor(string $type): ?string
    {
        return self::TYPES[$type]['bloom'] ?? null;
    }

    public static function categoryFor(string $type): ?string
    {
        return self::TYPES[$type]['category'] ?? null;
    }

    public static function isValidType(string $type): bool
    {
        return array_key_exists($type, self::TYPES);
    }
}
