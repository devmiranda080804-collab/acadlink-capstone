<?php

namespace App\Support;

use Illuminate\Validation\ValidationException;

// The tests (exam parts) an instructor chooses for a TOS — e.g. one 50-item Multiple
// Choice test, or Test I Multiple Choice + Test II Essay. Each test covers a run of
// consecutive Bloom's levels, starting where the previous test ends, so the TOS item
// numbering and the exam's per-topic Bloom's level order stay aligned.
class TestParts
{
    public const MAX = 6;

    public const ROMAN = ['I', 'II', 'III', 'IV', 'V', 'VI'];

    // Starting instructions for the exam section created from each test.
    public const INSTRUCTIONS = [
        'mc-single'       => 'Choose the letter of the best answer.',
        'true-false'      => 'Write TRUE if the statement is correct and FALSE if it is not.',
        'identification'  => 'Identify the term or concept being described.',
        'enumeration'     => 'List what is being asked.',
        'short-answer'    => 'Answer the following questions briefly but completely.',
        'problem-solving' => 'Solve the following problems. Show your complete solution.',
    ];

    public static function defaults(): array
    {
        return [['type' => 'mc-single', 'from' => BloomLevels::LEVELS[0]]];
    }

    // [{type, from}] -> [{number, roman, type, label, from, levels}]. Throws a
    // ValidationException with a plain-language message when the setup doesn't work.
    public static function normalize(?array $parts): array
    {
        $parts = array_values($parts ?: self::defaults());

        if (count($parts) > self::MAX) {
            self::fail('An exam can have at most ' . self::MAX . ' tests.');
        }

        $starts = [];
        foreach ($parts as $i => $part) {
            $type = $part['type'] ?? null;
            if (!is_string($type) || !BloomLevels::isValidType($type)) {
                self::fail('Choose a question type for Test ' . self::ROMAN[$i] . '.');
            }

            $start = $i === 0 ? 0 : array_search($part['from'] ?? null, BloomLevels::LEVELS, true);
            if ($start === false) {
                self::fail('Choose the Bloom\'s level where Test ' . self::ROMAN[$i] . ' starts.');
            }
            if ($i > 0 && $start <= $starts[$i - 1]) {
                self::fail('Test ' . self::ROMAN[$i] . ' must start at a higher Bloom\'s level than Test ' . self::ROMAN[$i - 1] . '.');
            }
            $starts[$i] = $start;
        }

        $result = [];
        foreach ($parts as $i => $part) {
            $end = $starts[$i + 1] ?? count(BloomLevels::LEVELS);
            $levels = array_slice(BloomLevels::LEVELS, $starts[$i], $end - $starts[$i]);

            $result[] = [
                'number' => $i + 1,
                'roman'  => self::ROMAN[$i],
                'type'   => $part['type'],
                'label'  => BloomLevels::TYPES[$part['type']]['label'],
                'from'   => $levels[0],
                'levels' => $levels,
            ];
        }

        return $result;
    }

    // Level name -> index of the test that covers it.
    public static function levelMap(array $normalized): array
    {
        $map = [];
        foreach ($normalized as $i => $part) {
            foreach ($part['levels'] as $level) {
                $map[$level] = $i;
            }
        }

        return $map;
    }

    // Item numbers only need a "II." style prefix when there is more than one test.
    public static function prefix(array $normalized, int $index): string
    {
        return count($normalized) > 1 ? $normalized[$index]['roman'] . '.' : '';
    }

    public static function sectionTitle(array $part): string
    {
        return 'Test ' . $part['roman'] . ' – ' . $part['label'];
    }

    protected static function fail(string $message): void
    {
        throw ValidationException::withMessages(['parts' => $message]);
    }
}
