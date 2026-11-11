<?php

namespace App\Support;

use Carbon\CarbonInterface;

// Dates and times as people in the college read them. The app stores and works in UTC;
// screens show Philippine time.
class DisplayTime
{
    public const TIMEZONE = 'Asia/Manila';

    // e.g. "Nov 11, 2026, 8:45 AM"
    public static function format(?CarbonInterface $time): string
    {
        return $time ? $time->copy()->timezone(self::TIMEZONE)->format('M j, Y, g:i A') : '—';
    }
}
