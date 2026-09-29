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
    // to its share of those hours, then split LOTS/HOTS per topic (Panel comment #9
    // default ratio, pending exact client confirmation).
    public static function targetBreakdown(int $courseId, string $gradingPeriod, int $totalItems): array
    {
        $topics = self::where('course_id', $courseId)
            ->where('grading_period', $gradingPeriod)
            ->orderBy('order')
            ->get();

        $totalHours = $topics->sum('hours');

        if ($totalHours === 0 || $totalItems === 0) {
            return ['total_hours' => $totalHours, 'total_items' => $totalItems, 'topics' => []];
        }

        // Raw (fractional) share per topic, then largest-remainder rounding so the
        // topic targets sum to exactly $totalItems instead of drifting from plain round().
        $raw = $topics->map(function ($topic) use ($totalHours, $totalItems) {
            $exact = $topic->hours / $totalHours * $totalItems;

            return [
                'topic'  => $topic,
                'exact'  => $exact,
                'floor'  => (int) floor($exact),
            ];
        });

        $allocated = $raw->sum('floor');
        $remainder = $totalItems - $allocated;

        $ranked = $raw->sortByDesc(fn($r) => $r['exact'] - $r['floor'])->values();

        $targets = $raw->map(function ($r, $i) use ($ranked, $remainder) {
            $bonus = $ranked->search(fn($x) => $x['topic']->id === $r['topic']->id) < $remainder ? 1 : 0;

            return $r['floor'] + $bonus;
        });

        return [
            'total_hours' => $totalHours,
            'total_items' => $totalItems,
            'topics' => $raw->values()->map(function ($r, $i) use ($targets, $totalHours) {
                $target = $targets[$i];
                $lots = (int) round($target * BloomLevels::LOTS_PERCENT / 100);
                $hots = $target - $lots;

                return [
                    'id'             => $r['topic']->id,
                    'topic'          => $r['topic']->topic,
                    'hours'          => $r['topic']->hours,
                    'weight_percent' => round($r['topic']->hours / $totalHours * 100, 1),
                    'target_items'   => $target,
                    'lots_target'    => $lots,
                    'hots_target'    => $hots,
                ];
            })->all(),
        ];
    }
}
