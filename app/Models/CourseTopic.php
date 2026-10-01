<?php

namespace App\Models;

use App\Support\BloomLevels;
use Illuminate\Database\Eloquent\Model;

class CourseTopic extends Model
{
    protected $fillable = ['course_id', 'grading_period', 'topic', 'weeks', 'hours', 'notes', 'module_path', 'module_file_name', 'order', 'created_by'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // TOS Generator target: total hours auto-fetched from OBE data (Panel comment
    // #8 — no manual Total Hours input), item counts allocated per topic proportional
    // to its share of those hours, then broken down per Bloom's Taxonomy level
    // (Remembering through Creating, matching the official TOS format) and given
    // sequential item numbers — "I." for the five regular levels, "II." for Creating
    // only, matching the official sample's own two-sequence numbering. "No. of
    // Items/Points" is POINTS, not a raw item count — every level is 1 point/item
    // except Creating, which is a bigger standalone item worth 5 (verified against
    // the sample's own row and TOTAL arithmetic).
    public static function targetBreakdown(int $courseId, string $gradingPeriod, int $totalItems): array
    {
        $topics = self::where('course_id', $courseId)
            ->where('grading_period', $gradingPeriod)
            ->orderBy('order')
            ->get();

        $totalHours = $topics->sum('hours');

        if ($totalHours === 0 || $totalItems === 0) {
            return ['total_hours' => $totalHours, 'total_items' => $totalItems, 'total_points' => 0, 'topics' => []];
        }

        // Pass 1: hours -> target_items per topic (largest-remainder rounding).
        $topicTargets = self::distribute(
            $totalItems,
            $topics->mapWithKeys(fn($t) => [$t->id => $t->hours])->all()
        );

        // Pass 2: each topic's target_items -> per-Bloom's-level item counts
        // (largest-remainder rounding again, this time against LEVEL_WEIGHTS).
        $counterI = 1;
        $counterII = 1;
        $totalPoints = 0;

        $topicRows = $topics->map(function ($topic) use ($topicTargets, $totalHours, &$counterI, &$counterII, &$totalPoints) {
            $target = $topicTargets[$topic->id];
            $levelCounts = self::distribute($target, BloomLevels::LEVEL_WEIGHTS);

            $levels = [];
            $topicPoints = 0;

            foreach (BloomLevels::LEVELS as $level) {
                $count = $levelCounts[$level];
                $pointsPerItem = BloomLevels::POINTS_PER_ITEM[$level];
                $points = $count * $pointsPerItem;
                $topicPoints += $points;

                $range = null;
                if ($count > 0) {
                    if ($level === 'Creating') {
                        $start = $counterII;
                        $counterII += $count;
                        $end = $counterII - 1;
                        $range = 'II.' . ($count === 1 ? $start : "{$start}-{$end}");
                    } else {
                        $start = $counterI;
                        $counterI += $count;
                        $end = $counterI - 1;
                        $range = 'I.' . ($count === 1 ? $start : "{$start}-{$end}");
                    }
                }

                $levels[$level] = ['count' => $count, 'range' => $range, 'points' => $points, 'points_per_item' => $pointsPerItem];
            }

            $totalPoints += $topicPoints;

            return [
                'id'             => $topic->id,
                'topic'          => $topic->topic,
                'hours'          => $topic->hours,
                'weight_percent' => round($topic->hours / $totalHours * 100, 1),
                'target_items'   => $target,
                'levels'         => $levels,
                'topic_points'   => $topicPoints,
            ];
        })->values()->all();

        return [
            'total_hours'  => $totalHours,
            'total_items'  => $totalItems,
            'total_points' => $totalPoints,
            'topics'       => $topicRows,
        ];
    }

    // Resolves which Bloom's level a question at $position (1-indexed, counting only
    // this topic's own questions) falls under, per this topic's TOS breakdown row (one
    // entry of targetBreakdown()['topics']) — e.g. position 1-5 -> Remembering, 6-10 ->
    // Understanding, matching the TOS's own "I.1-5, I.6-10, ..." ranges. This is what
    // actually assigns a question's Bloom's Level now (never the client, never a fixed
    // per-type mapping — Panel comment #11, plus the instructor's requirement that exam
    // items stay aligned to both the TOS and Bloom's Taxonomy). Positions beyond the
    // topic's own target extend into the last level that had any items, so extra
    // questions still get classified instead of being left blank.
    public static function resolveBloomLevelForPosition(?array $topicBreakdownRow, int $position): ?string
    {
        if (!$topicBreakdownRow) {
            return null;
        }

        $cursor = 0;
        $lastLevel = null;
        foreach (BloomLevels::LEVELS as $level) {
            $count = $topicBreakdownRow['levels'][$level]['count'] ?? 0;
            if ($count > 0) {
                $lastLevel = $level;
            }
            if ($position <= $cursor + $count) {
                return $level;
            }
            $cursor += $count;
        }

        return $lastLevel;
    }

    // Largest-remainder distribution: splits $total across the given non-negative
    // integer weights so the parts sum to exactly $total instead of drifting from
    // plain rounding. Returns an array keyed the same as $weights.
    protected static function distribute(int $total, array $weights): array
    {
        $weightSum = array_sum($weights);
        if ($weightSum <= 0 || $total <= 0) {
            return array_map(fn() => 0, $weights);
        }

        $raw = [];
        foreach ($weights as $key => $weight) {
            $exact = $weight / $weightSum * $total;
            $raw[$key] = ['exact' => $exact, 'floor' => (int) floor($exact)];
        }

        $allocated = array_sum(array_column($raw, 'floor'));
        $remainder = $total - $allocated;

        // Largest fractional remainder gets the leftover +1's, one each.
        $order = array_keys($raw);
        usort($order, fn($a, $b) => ($raw[$b]['exact'] - $raw[$b]['floor']) <=> ($raw[$a]['exact'] - $raw[$a]['floor']));

        $result = [];
        foreach ($raw as $key => $r) {
            $bonus = (array_search($key, $order) < $remainder) ? 1 : 0;
            $result[$key] = $r['floor'] + $bonus;
        }

        return $result;
    }
}
