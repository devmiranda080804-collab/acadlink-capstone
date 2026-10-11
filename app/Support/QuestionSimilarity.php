<?php

namespace App\Support;

// Duplicate-question checks for an exam (Panel: duplicate question validation / AI check).
// "exact" = the same wording once case, punctuation and spacing are ignored — never allowed.
// "similar" = nearly the same wording — a warning only, since two computations can
// legitimately differ by just their numbers. The Exam Builder runs the same rules in JS.
class QuestionSimilarity
{
    // Character-bigram (Dice) similarity at or above this is "similar".
    public const SIMILAR_THRESHOLD = 0.87;

    // Very short questions are only compared for exact matches.
    public const MIN_WORDS_FOR_SIMILAR = 4;

    public static function normalize(?string $text): string
    {
        $text = mb_strtolower(strip_tags((string) $text));
        $text = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $text);

        return trim(preg_replace('/\s+/', ' ', $text));
    }

    // 'exact', 'similar' or null.
    public static function compare(?string $a, ?string $b): ?string
    {
        $na = self::normalize($a);
        $nb = self::normalize($b);
        if ($na === '' || $nb === '') {
            return null;
        }
        if ($na === $nb) {
            return 'exact';
        }
        if (min(substr_count($na, ' '), substr_count($nb, ' ')) + 1 < self::MIN_WORDS_FOR_SIMILAR) {
            return null;
        }

        return self::dice($na, $nb) >= self::SIMILAR_THRESHOLD ? 'similar' : null;
    }

    public static function dice(string $a, string $b): float
    {
        $bigrams = function (string $s): array {
            $s = str_replace(' ', '', $s);
            $chars = mb_str_split($s);
            $out = [];
            for ($i = 0; $i < count($chars) - 1; $i++) {
                $pair = $chars[$i] . $chars[$i + 1];
                $out[$pair] = ($out[$pair] ?? 0) + 1;
            }

            return $out;
        };

        $x = $bigrams($a);
        $y = $bigrams($b);
        $total = array_sum($x) + array_sum($y);
        if ($total === 0) {
            return 0.0;
        }

        $shared = 0;
        foreach ($x as $pair => $n) {
            $shared += min($n, $y[$pair] ?? 0);
        }

        return 2 * $shared / $total;
    }

    // Every question of an exam with the item label the builder shows: numbers run
    // across the whole exam ("Item 7"), sub-questions get a letter ("Item 7b").
    // Works on the builder's payload arrays and on loaded models alike.
    public static function examItems(iterable $sections): array
    {
        $items = [];
        $number = 1;
        foreach ($sections as $section) {
            foreach (data_get($section, 'questions') ?? [] as $question) {
                $items[] = ['label' => "Item {$number}", 'text' => data_get($question, 'question_text')];
                foreach (data_get($question, 'children') ?? [] as $i => $child) {
                    $items[] = ['label' => "Item {$number}" . chr(97 + $i), 'text' => data_get($child, 'question_text')];
                }
                $number++;
            }
        }

        return $items;
    }

    // Plain-language messages for questions that are exactly the same.
    public static function exactDuplicateIssues(array $items): array
    {
        $groups = [];
        foreach ($items as $item) {
            $key = self::normalize($item['text']);
            if ($key !== '') {
                $groups[$key][] = $item['label'];
            }
        }

        $issues = [];
        foreach ($groups as $labels) {
            if (count($labels) > 1) {
                $last = array_pop($labels);
                $issues[] = implode(', ', $labels) . " and {$last} are the same question. Change or remove the extra " . (count($labels) > 1 ? 'copies' : 'copy') . '.';
            }
        }

        return $issues;
    }

    // The first existing item a text repeats (exactly or nearly), as [label, kind], or null.
    public static function findMatch(?string $text, array $items): ?array
    {
        foreach ($items as $item) {
            if ($kind = self::compare($text, $item['text'])) {
                return [$item['label'], $kind];
            }
        }

        return null;
    }
}
