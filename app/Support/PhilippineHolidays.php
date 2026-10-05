<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

// Philippine public holidays for the calendar, so nobody has to enter them by hand.
// Source is the free Nager.Date API (includes proclamation-based dates like Chinese
// New Year), cached per year. If the server can't reach it, a locally computed list
// of the fixed-date and Holy Week holidays is used instead.
class PhilippineHolidays
{
    public static function forYears(array $years): array
    {
        $holidays = [];
        foreach ($years as $year) {
            $holidays = array_merge($holidays, self::forYear($year));
        }

        return $holidays;
    }

    public static function forYear(int $year): array
    {
        $cacheKey = "ph-holidays-{$year}";
        if (($cached = Cache::get($cacheKey)) !== null) {
            return $cached;
        }

        $fromApi = self::fetch($year);
        if ($fromApi !== null) {
            Cache::put($cacheKey, $fromApi, now()->addDays(30));
            return $fromApi;
        }

        // Retry the API tomorrow instead of caching the fallback for a month.
        $fallback = self::computed($year);
        Cache::put($cacheKey, $fallback, now()->addDay());

        return $fallback;
    }

    protected static function fetch(int $year): ?array
    {
        try {
            $response = Http::timeout(5)->get("https://date.nager.at/api/v3/PublicHolidays/{$year}/PH");
            if (!$response->successful() || !is_array($response->json())) {
                return null;
            }

            return collect($response->json())
                ->filter(fn($h) => !empty($h['date']) && !empty($h['name']))
                ->map(fn($h) => ['date' => $h['date'], 'name' => $h['name']])
                ->values()
                ->all();
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected static function computed(int $year): array
    {
        $easter = Carbon::create($year, 3, 21)->addDays(easter_days($year));
        $lastMondayOfAugust = Carbon::create($year, 8, 31)->startOfDay();
        while (!$lastMondayOfAugust->isMonday()) {
            $lastMondayOfAugust->subDay();
        }

        $days = [
            "{$year}-01-01" => "New Year's Day",
            $easter->copy()->subDays(3)->toDateString() => 'Maundy Thursday',
            $easter->copy()->subDays(2)->toDateString() => 'Good Friday',
            $easter->copy()->subDay()->toDateString() => 'Black Saturday',
            "{$year}-04-09" => 'Day of Valor',
            "{$year}-05-01" => 'Labour Day',
            "{$year}-06-12" => 'Independence Day',
            "{$year}-08-21" => 'Ninoy Aquino Day',
            $lastMondayOfAugust->toDateString() => 'National Heroes Day',
            "{$year}-11-01" => "All Saints' Day",
            "{$year}-11-30" => 'Bonifacio Day',
            "{$year}-12-08" => 'Feast of the Immaculate Conception of Mary',
            "{$year}-12-24" => 'Christmas Eve',
            "{$year}-12-25" => 'Christmas Day',
            "{$year}-12-30" => 'Rizal Day',
            "{$year}-12-31" => 'Last Day of the Year',
        ];
        ksort($days);

        return collect($days)->map(fn($name, $date) => ['date' => $date, 'name' => $name])->values()->all();
    }
}
