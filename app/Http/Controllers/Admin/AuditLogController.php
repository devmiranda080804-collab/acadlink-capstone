<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $action = $request->get('action');
        $search = $request->get('search');

        $logs = AuditLog::with('user')
            ->when($action, fn($q) => $q->where('action', $action))
            ->when($search, fn($q) => $q->where(function ($q2) use ($search) {
                $q2->where('description', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($q3) => $q3->where('name', 'like', "%{$search}%"));
            }))
            ->latest('created_at')
            ->paginate(30)
            ->withQueryString();

        // For the filter dropdown — all distinct action types
        $actionTypes = AuditLog::select('action')->distinct()->pluck('action');

        $totalCount = AuditLog::count();
        $todayCount = AuditLog::whereDate('created_at', today())->count();

        return view('admin.audit-logs', compact('logs', 'action', 'search', 'actionTypes', 'totalCount', 'todayCount'));
    }
}