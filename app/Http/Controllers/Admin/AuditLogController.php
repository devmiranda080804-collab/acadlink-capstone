<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AuditLogController extends Controller
{
    // User types in the filter. "system" means actions with no signed-in user.
    const USER_TYPES = [
        'admin'        => 'Admin / Dean',
        'program_head' => 'Program Head',
        'secretary'    => 'Secretary',
        'faculty'      => 'Faculty',
        'system'       => 'System',
    ];

    // Dates people pick are Philippine dates; the log is stored in UTC.
    const TIMEZONE = 'Asia/Manila';

    public function index(Request $request)
    {
        $action = $request->get('action');
        $search = trim((string) $request->get('search'));
        $userType = array_key_exists($request->get('user_type'), self::USER_TYPES) ? $request->get('user_type') : null;
        $from = $this->date($request->get('from'));
        $to = $this->date($request->get('to'));
        if ($from && $to && $from->gt($to)) {
            [$from, $to] = [$to, $from];
        }

        $logs = AuditLog::with('user')
            ->when($action, fn ($q) => $q->where('action', $action))
            ->when($search !== '', fn ($q) => $q->where(function ($q2) use ($search) {
                $q2->where('description', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($q3) => $q3->where('name', 'like', "%{$search}%"));
            }))
            ->when($userType === 'system', fn ($q) => $q->whereNull('user_id'))
            ->when($userType && $userType !== 'system', fn ($q) => $q->whereHas('user', fn ($u) => $u->where('role', $userType)))
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from->copy()->startOfDay()->utc()))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to->copy()->endOfDay()->utc()))
            ->latest('created_at')
            ->paginate(30)
            ->withQueryString();

        // For the filter dropdown — all distinct action types
        $actionTypes = AuditLog::select('action')->distinct()->orderBy('action')->pluck('action');

        $today = now(self::TIMEZONE)->startOfDay();
        $totalCount = AuditLog::count();
        $todayCount = AuditLog::where('created_at', '>=', $today->copy()->utc())->count();
        $weekCount = AuditLog::where('created_at', '>=', $today->copy()->startOfWeek()->utc())->count();
        $filtered = $action || $search !== '' || $userType || $from || $to;

        return view('admin.audit-logs', [
            'logs'        => $logs,
            'action'      => $action,
            'search'      => $search,
            'userType'    => $userType,
            'from'        => $from?->toDateString(),
            'to'          => $to?->toDateString(),
            'actionTypes' => $actionTypes,
            'userTypes'   => self::USER_TYPES,
            'totalCount'  => $totalCount,
            'todayCount'  => $todayCount,
            'weekCount'   => $weekCount,
            'filtered'    => $filtered,
        ]);
    }

    // A YYYY-MM-DD date from the filter, as a Philippine date; anything else is ignored.
    protected function date(?string $value): ?Carbon
    {
        if (!$value || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }
        try {
            return Carbon::createFromFormat('Y-m-d', $value, self::TIMEZONE)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }
}
