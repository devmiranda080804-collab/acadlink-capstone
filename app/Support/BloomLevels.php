<?php

namespace App\Support;

// Single source of truth for question types + their auto-assigned Bloom's Taxonomy
// level (Capstone panel comment #11: Bloom's Level must be auto-filled by predefined
// logic, manual input must be prevented). Used by the "Choose Question Type" modal,
// the server-side auto-classification in ExamGeneratorController, and the TOS
// Generator's LOTS/HOTS split.
class BloomLevels
{
    // Trimmed to this set for now, per the client's own reference list. 'bloom'/'category'
    // here are only a TYPE's *natural* default — actual Bloom's Level is no longer taken
    // 1:1 from type. It's resolved from the question's POSITION within its topic against
    // the TOS's own per-level breakdown (see CourseTopic::resolveBloomLevelForPosition()),
    // since the same format (e.g. Multiple Choice) can legitimately sit at different levels
    // depending on where it falls in a topic's TOS sequence. This field is now just a
    // sensible fallback for topics/exams that have no TOS target_items to align against.
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
        'short-answer' => [
            'label' => 'Short Answer / Essay',
            'desc'  => 'Longform text answer',
            'icon'  => 'T',
            'bloom' => 'Analyzing',
            'category' => 'HOTS',
        ],
        'problem-solving' => [
            'label' => 'Problem Solving / Computation',
            'desc'  => 'Numeric problem with shown solution steps',
            'icon'  => '∑',
            'bloom' => 'Applying',
            'category' => 'HOTS',
        ],
    ];

    // Panel comment #9: exact HOTS/LOTS ratio still pending client confirmation.
    // Common DepEd/CHED TOS convention used as the default until then.
    const LOTS_PERCENT = 60;
    const HOTS_PERCENT = 40;

    // Canonical Bloom's Taxonomy order, used by the TOS Generator to build the
    // official-format table (Remembering through Creating as separate columns).
    const LEVELS = ['Remembering', 'Understanding', 'Applying', 'Analyzing', 'Evaluating', 'Creating'];

    // Re-derives the LOTS/HOTS split above, subdivided evenly within each category
    // (LOTS 60% over Remembering+Understanding, HOTS 40% over the other four).
    const LEVEL_WEIGHTS = [
        'Remembering'   => 30,
        'Understanding' => 30,
        'Applying'      => 10,
        'Analyzing'     => 10,
        'Evaluating'    => 10,
        'Creating'      => 10,
    ];

    // Every level is worth exactly 1 point per item, so "No. of Items/Points" always
    // equals the Total Items the faculty configured — no separate points system to
    // reconcile.
    const POINTS_PER_ITEM = [
        'Remembering'   => 1,
        'Understanding' => 1,
        'Applying'      => 1,
        'Analyzing'     => 1,
        'Evaluating'    => 1,
        'Creating'      => 1,
    ];

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
