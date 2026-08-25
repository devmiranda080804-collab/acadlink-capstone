<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs – CBMA System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background-color: #f0f0f0; display: flex; height: 100vh; overflow: hidden; }
        .sidebar { width: 210px; background-color: #0f2557; display: flex; flex-direction: column; flex-shrink: 0; overflow-y: auto; }
        .sidebar-logo { display: flex; flex-direction: column; align-items: center; padding: 22px 16px 16px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-logo img { width: 72px; height: 72px; border-radius: 50%; object-fit: contain; background: #1b3d7a; }
        .sidebar-logo .brand { color: #fff; font-size: 15px; font-weight: 700; margin-top: 8px; }
        .sidebar-logo .brand-sub { color: #a0b4d6; font-size: 10px; margin-top: 2px; }
        .nav-list { list-style: none; padding: 10px 0; flex: 1; }
        .nav-list li a { display: flex; align-items: center; gap: 11px; padding: 11px 20px; color: #c8d6ec; text-decoration: none; font-size: 13px; transition: background 0.15s, color 0.15s; }
        .nav-list li a:hover { background-color: rgba(255,255,255,0.08); color: #fff; }
        .nav-list li.active a { background-color: rgba(255,255,255,0.08); color: #fff; border-left: 3px solid #fff; }
        .nav-list li a svg { width: 18px; height: 18px; flex-shrink: 0; opacity: 0.85; }
        .sidebar-logout { padding: 12px 0; border-top: 1px solid rgba(255,255,255,0.1); }
        .sidebar-logout a { display: flex; align-items: center; gap: 11px; padding: 11px 20px; color: #c8d6ec; text-decoration: none; font-size: 13px; transition: background 0.15s; }
        .sidebar-logout a:hover { background-color: rgba(255,255,255,0.08); color: #fff; }

        .main { flex: 1; display: flex; flex-direction: column; overflow: hidden; background-color: #f5f6fa; }
        .topnav { background: #fff; border-bottom: 1px solid #e0e0e0; padding: 0 24px; height: 52px; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
        .topnav .label { font-size: 12px; color: #666; }
        .topnav-right { display: flex; align-items: center; gap: 12px; }
        .role-badge { background-color: #0f2557; color: #fff; font-size: 12px; font-weight: 600; padding: 5px 16px; border-radius: 20px; }
        .user-info { display: flex; align-items: center; gap: 8px; }
        .user-text { text-align: right; }
        .user-name { font-size: 12px; font-weight: 600; color: #222; }
        .user-email { font-size: 10px; color: #888; }
        .user-avatar { width: 34px; height: 34px; border-radius: 50%; background-color: #0f2557; color: #fff; font-size: 12px; font-weight: 700; display: flex; align-items: center; justify-content: center; }

        .content { flex: 1; overflow-y: auto; padding: 24px 28px 28px; }
        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 3px; }
        .page-sub { font-size: 11.5px; color: #888; margin-bottom: 20px; }

        .filters-row { display: flex; gap: 10px; margin-bottom: 18px; }
        .filter-select { height: 36px; padding: 0 32px 0 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 12.5px; color: #333; background: #fff; outline: none; appearance: none; -webkit-appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%23666' d='M5 7L0 2h10z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; cursor: pointer; min-width: 220px; }
        .filter-select:focus { border-color: #0f2557; }

        .log-panel { background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; overflow: hidden; }
        .log-table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
        .log-table thead tr { background: #fafafa; border-bottom: 1px solid #eee; }
        .log-table th { padding: 12px 16px; text-align: left; font-size: 11.5px; font-weight: 700; color: #666; }
        .log-table td { padding: 13px 16px; border-bottom: 1px solid #f5f5f5; color: #333; vertical-align: top; }
        .log-table tbody tr:last-child td { border-bottom: none; }
        .log-table tbody tr:hover { background: #fafbff; }
        .log-table td.empty-row { text-align: center; color: #999; padding: 40px 16px; }

        .action-badge { display: inline-block; font-size: 10.5px; font-weight: 700; padding: 4px 11px; border-radius: 12px; white-space: nowrap; }
        .action-created { background: #dbeafe; color: #1e40af; }
        .action-archived { background: #fee2e2; color: #991b1b; }
        .action-restored { background: #d1fae5; color: #065f46; }
        .action-approved { background: #d1fae5; color: #065f46; }
        .action-rejected { background: #fee2e2; color: #991b1b; }
        .action-revision { background: #fef3c7; color: #92400e; }
        .action-distributed { background: #ede9fe; color: #6d28d9; }
        .action-permissions { background: #f0f4ff; color: #0f2557; }
        .action-default { background: #f3f4f6; color: #4b5563; }

        .log-desc { color: #555; line-height: 1.5; max-width: 480px; }
        .log-user { font-weight: 600; color: #1a1a2e; }
        .log-date { color: #999; font-size: 11.5px; white-space: nowrap; }

        .pagination-row { display: flex; justify-content: center; padding: 16px; }

        svg { display: inline-block; vertical-align: middle; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="{{ asset('images/cbma-logo.png') }}" alt="CBMA Logo">
            <span class="brand">CBMA</span>
            <span class="brand-sub">Academic Coordination</span>
        </div>
        <ul class="nav-list">
            @if($navPermissions['dashboard'] ?? true)
            <li class="{{ request()->is('admin/dashboard') ? 'active' : '' }}"><a href="{{ url('/admin/dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>Dashboard</a></li>
            @endif
            @if($navPermissions['account-management'] ?? true)
            <li class="{{ request()->is('admin/account-management*') ? 'active' : '' }}"><a href="{{ url('/admin/account-management') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>Account Management</a></li>
            @endif
            @if($navPermissions['roles-permissions'] ?? true)
            <li class="{{ request()->is('admin/roles-permissions*') ? 'active' : '' }}"><a href="{{ url('/admin/roles-permissions') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>Roles & Permissions</a></li>
            @endif
            @if($navPermissions['template-approvals'] ?? true)
            <li class="{{ request()->is('admin/template-approvals*') ? 'active' : '' }}"><a href="{{ url('/admin/template-approvals') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>Template Approvals</a></li>
            @endif
            @if($navPermissions['course-assignment'] ?? true)
            <li class="{{ request()->is('admin/course-assignment*') ? 'active' : '' }}"><a href="{{ url('/admin/course-assignment') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>Course Assignment</a></li>
            @endif
            @if($navPermissions['audit-logs'] ?? true)
            <li class="{{ request()->is('admin/audit-logs*') ? 'active' : '' }}"><a href="{{ url('/admin/audit-logs') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>Audit Logs</a></li>
            @endif
            @if($navPermissions['announcements'] ?? true)
            <li class="{{ request()->is('admin/announcements*') ? 'active' : '' }}"><a href="{{ url('/admin/announcements') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>Announcements</a></li>
            @endif
            @if($navPermissions['calendar'] ?? true)
            <li class="{{ request()->is('admin/calendar*') ? 'active' : '' }}"><a href="{{ url('/admin/calendar') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Calendar of Activities</a></li>
            @endif
        </ul>
        <div class="sidebar-logout">
            <a href="#" onclick="document.getElementById('logout-form').submit()">
                <svg style="width:14px;height:14px;flex-shrink:0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Log out
            </a>
            <form id="logout-form" method="POST" action="{{ url('/logout') }}" style="display:none;">@csrf</form>
        </div>
    </aside>

    <div class="main">
        <div class="topnav">
            <span class="label">Current role</span>
            <div class="topnav-right">
                <span class="role-badge">Admin/Dean</span>
                <div class="user-info">
                    <div class="user-text">
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <div class="user-email">{{ auth()->user()->email }}</div>
                    </div>
                    <div class="user-avatar">{{ auth()->user()->initials }}</div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="page-title">Audit Logs</div>
            <div class="page-sub">Talaan ng mahahalagang aksyon sa system</div>

            <div class="filters-row">
                <form method="GET">
                    <select name="action" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Actions</option>
                        @foreach($actionTypes as $type)
                            <option value="{{ $type }}" {{ $action == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="log-panel">
                <table class="log-table">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            @php
                                $badgeClass = match(true) {
                                    str_contains($log->action, 'Created') => 'action-created',
                                    str_contains($log->action, 'Archived') => 'action-archived',
                                    str_contains($log->action, 'Restored') => 'action-restored',
                                    str_contains($log->action, 'Approved') => 'action-approved',
                                    str_contains($log->action, 'Rejected') => 'action-rejected',
                                    str_contains($log->action, 'Revision') => 'action-revision',
                                    str_contains($log->action, 'Distributed') => 'action-distributed',
                                    str_contains($log->action, 'Permissions') => 'action-permissions',
                                    default => 'action-default',
                                };
                            @endphp
                            <tr>
                                <td class="log-date">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                                <td class="log-user">{{ $log->user->name ?? 'System' }}</td>
                                <td><span class="action-badge {{ $badgeClass }}">{{ $log->action }}</span></td>
                                <td class="log-desc">{{ $log->description }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="empty-row">Wala pang naitalang aksyon.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @if($logs->hasPages())
                    <div class="pagination-row">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

</body>
</html>