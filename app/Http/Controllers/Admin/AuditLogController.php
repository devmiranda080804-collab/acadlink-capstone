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

        $logs = AuditLog::with('user')
            ->when($action, fn($q) => $q->where('action', $action))
            ->latest('created_at')
            ->paginate(30);

        // Para sa filter dropdown — lahat ng natatanging action types
        $actionTypes = AuditLog::select('action')->distinct()->pluck('action');

        return view('admin.audit-logs', compact('logs', 'action', 'actionTypes'));
    }
}