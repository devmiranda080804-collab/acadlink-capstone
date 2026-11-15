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
        .nav-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 16px; height: 16px; padding: 0 4px; margin-left: auto; background: #ef4444; color: #fff; font-size: 10px; font-weight: 700; border-radius: 999px; }
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

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 16px; margin-bottom: 22px; }
        .stat-card { background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; padding: 18px 20px; display: flex; align-items: center; gap: 14px; }
        .stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .stat-icon svg { width: 22px; height: 22px; }
        .stat-icon.blue { background: #dbeafe; color: #1d4ed8; }
        .stat-icon.green { background: #d1fae5; color: #059669; }
        .stat-info .stat-value { font-size: 24px; font-weight: 700; color: #1a1a2e; line-height: 1.1; }
        .stat-info .stat-label { font-size: 11.5px; color: #888; margin-top: 2px; }

        .filters-row { display: flex; gap: 10px; margin-bottom: 18px; flex-wrap: wrap; align-items: center; }
        .filter-select { height: 36px; padding: 0 32px 0 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 12.5px; color: #333; background: #fff; outline: none; appearance: none; -webkit-appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%23666' d='M5 7L0 2h10z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; cursor: pointer; min-width: 220px; }
        .filter-select:focus { border-color: #0f2557; }
        .filters-row form { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .search-box { position: relative; width: 260px; max-width: 100%; }
        .search-box svg { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: #999; pointer-events: none; }
        .search-box input { width: 100%; height: 36px; padding: 0 10px 0 30px; border: 1px solid #ccc; border-radius: 6px; font-size: 12.5px; outline: none; font-family: Arial, sans-serif; box-sizing: border-box; }
        .search-box input:focus { border-color: #0f2557; }
        .btn-filter { height: 36px; padding: 0 18px; background: #0f2557; color: #fff; border: none; border-radius: 6px; font-size: 12.5px; font-weight: 600; cursor: pointer; }
        .btn-filter:hover { background: #1a3a7a; }
        .btn-clear-filter { height: 36px; padding: 0 14px; background: #fff; color: #666; border: 1px solid #ccc; border-radius: 6px; font-size: 12.5px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; }
        .btn-clear-filter:hover { background: #f5f5f5; }

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

        .pagination-row { display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; border-top: 1px solid #f0f0f0; flex-wrap: wrap; gap: 10px; }
        .pagination-info { font-size: 11.5px; color: #888; }
        .pagination-controls { display: flex; align-items: center; gap: 4px; }
        .page-btn { padding: 6px 12px; font-size: 12px; font-weight: 600; color: #444; background: #fff; border: 1px solid #ddd; border-radius: 6px; text-decoration: none; cursor: pointer; }
        .page-btn:hover { border-color: #0f2557; color: #0f2557; }
        .page-btn.disabled { color: #ccc; border-color: #eee; cursor: default; pointer-events: none; }
        .page-num { display: inline-flex; align-items: center; justify-content: center; min-width: 30px; height: 30px; padding: 0 6px; font-size: 12px; font-weight: 600; color: #444; background: #fff; border: 1px solid #ddd; border-radius: 6px; text-decoration: none; }
        .page-num:hover { border-color: #0f2557; color: #0f2557; }
        .page-num.active { background: #0f2557; color: #fff; border-color: #0f2557; }

        svg { display: inline-block; vertical-align: middle; }
    </style>
</head>
<body>

    @include('partials.sidebar')

    <div class="main">
        <div class="topnav">
            <div class="topnav-right" style="margin-left:auto;">
                @include('partials.topnav-user')
            </div>
        </div>

        <div class="content">
            <style>
                .al { font-family: 'Plus Jakarta Sans', Arial, sans-serif; color: #14213d; }
                .al .al-ic { width: 18px; height: 18px; flex-shrink: 0; }
                .al-eyebrow { font-size: 12px; font-weight: 800; letter-spacing: 2px; color: #1b3d7a; text-transform: uppercase; }
                .al-title { font-size: 28px; font-weight: 800; letter-spacing: -.4px; margin: 4px 0 6px; }
                .al-sub { font-size: 14px; color: #475467; }
                .al-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; margin: 22px 0 16px; }
                .al-stat { display: flex; align-items: center; gap: 14px; background: #fff; border: 1px solid #e7eaf1; border-radius: 16px; padding: 16px 18px;
                    box-shadow: 0 1px 2px rgba(15, 37, 87, .04), 0 6px 18px rgba(15, 37, 87, .05); }
                .al-stat-icon { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
                .al-stat-icon .al-ic { width: 21px; height: 21px; }
                .al-stat-value { font-size: 24px; font-weight: 800; line-height: 1.1; }
                .al-stat-label { font-size: 12.5px; font-weight: 600; color: #667085; margin-top: 2px; }
                .t-navy { background: #eef2fa; color: #0f2557; } .t-green { background: #ecfdf3; color: #16a34a; } .t-amber { background: #fff7e6; color: #d97706; } .t-violet { background: #f4f0ff; color: #7c3aed; }

                .al-card { background: #fff; border: 1px solid #e7eaf1; border-radius: 16px; box-shadow: 0 1px 2px rgba(15, 37, 87, .04), 0 6px 18px rgba(15, 37, 87, .05); }
                .al-filters { display: grid; grid-template-columns: minmax(220px, 2.2fr) repeat(2, minmax(150px, 1fr)) repeat(2, minmax(140px, .9fr)) auto; gap: 12px; align-items: end; padding: 16px 18px; }
                .al-field label { display: block; font-size: 12.5px; font-weight: 700; color: #344054; margin-bottom: 6px; }
                .al-field input, .al-field select { width: 100%; height: 40px; padding: 0 12px; border: 1px solid #d5dbe8; border-radius: 10px; background: #fff;
                    font: 500 13.5px 'Plus Jakarta Sans', Arial, sans-serif; color: #14213d; outline: none; transition: border-color .15s ease, box-shadow .15s ease; }
                .al-field input::placeholder { color: #98a2b3; }
                .al-field input:focus, .al-field select:focus { border-color: #1b3d7a; box-shadow: 0 0 0 3px rgba(37, 99, 235, .14); }
                .al-search { position: relative; }
                .al-search .al-ic { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #98a2b3; width: 16px; height: 16px; }
                .al-search input { padding-left: 36px; }
                .al-buttons { display: flex; gap: 8px; }
                .al-buttons .btn-save { height: 40px; padding: 0 16px; display: inline-flex; align-items: center; gap: 6px; font-size: 13.5px; }
                .al-reset { height: 40px; padding: 0 14px; display: inline-flex; align-items: center; border: 1px solid #d5dbe8; border-radius: 10px; color: #344054; text-decoration: none; font-size: 13.5px; font-weight: 700; background: #fff; }
                .al-reset:hover { background: #f8f9fc; }
                .al-chips { display: flex; flex-wrap: wrap; gap: 6px; padding: 0 18px 14px; margin-top: -4px; }
                .al-chip { padding: 4px 10px; border-radius: 999px; background: #eef2fa; color: #0f2557; font-size: 12px; font-weight: 700; }

                .al-table-wrap { overflow-x: auto; border-top: 1px solid #eef0f5; }
                .al-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
                .al-table th { text-align: left; padding: 12px 18px; background: #f8f9fc; color: #475467; font-size: 11.5px; font-weight: 800; letter-spacing: .5px; text-transform: uppercase; white-space: nowrap; }
                .al-table td { padding: 13px 18px; border-top: 1px solid #f1f3f8; vertical-align: top; }
                .al-table tbody tr { transition: background-color .15s ease; }
                .al-table tbody tr:hover { background: #fafbfd; }
                .al-when strong { display: block; font-weight: 700; white-space: nowrap; }
                .al-when span { font-size: 12px; color: #667085; white-space: nowrap; }
                .al-user { display: flex; align-items: center; gap: 10px; min-width: 170px; }
                .al-avatar { width: 32px; height: 32px; border-radius: 50%; background: #0f2557; color: #fff; font-size: 11.5px; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
                .al-avatar.system { background: #eef2fa; color: #0f2557; }
                .al-user strong { display: block; font-weight: 700; }
                .al-user span { font-size: 12px; color: #667085; }
                .al-desc { color: #344054; line-height: 1.5; min-width: 260px; }
                .action-badge { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 11.5px; font-weight: 800; white-space: nowrap; }
                .action-created { background: #ecfdf3; color: #15803d; } .action-approved { background: #ecfdf3; color: #15803d; } .action-restored { background: #eff4ff; color: #1d4ed8; }
                .action-archived { background: #f2f4f7; color: #475467; } .action-rejected { background: #fef2f2; color: #b91c1c; } .action-revision { background: #fff7e6; color: #b45309; }
                .action-distributed { background: #f4f0ff; color: #6d28d9; } .action-permissions { background: #eef2fa; color: #0f2557; } .action-default { background: #f2f4f7; color: #344054; }
                .al-empty { padding: 44px 18px; text-align: center; color: #667085; }
                .al-empty .al-ic { width: 26px; height: 26px; color: #98a2b3; display: block; margin: 0 auto 8px; }

                .al-pages { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; padding: 14px 18px; border-top: 1px solid #f1f3f8; font-size: 13px; color: #667085; }
                .al-pager { display: flex; gap: 6px; flex-wrap: wrap; }
                .al-pager a, .al-pager span { min-width: 34px; height: 34px; padding: 0 10px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #e7eaf1;
                    border-radius: 8px; color: #344054; text-decoration: none; font-weight: 700; background: #fff; }
                .al-pager a:hover { background: #f8f9fc; }
                .al-pager .is-current { background: #0f2557; border-color: #0f2557; color: #fff; }
                .al-pager .is-disabled { color: #c0c6d4; }
                @media (max-width: 1100px) { .al-filters { grid-template-columns: repeat(2, minmax(0, 1fr)); } .al-filters .al-field.wide { grid-column: 1 / -1; } }
            </style>

            <div class="al">
                <div class="al-eyebrow">System activity</div>
                <div class="al-title">Audit Logs</div>
                <div class="al-sub">A record of important actions in the system — who did what, and when (Philippine time).</div>

                <div class="al-stats">
                    <div class="al-stat"><span class="al-stat-icon t-navy">{!! \App\Support\SidebarMenu::icon('scroll', 'al-ic') !!}</span><div><div class="al-stat-value">{{ number_format($totalCount) }}</div><div class="al-stat-label">Total actions logged</div></div></div>
                    <div class="al-stat"><span class="al-stat-icon t-green">{!! \App\Support\SidebarMenu::icon('clock', 'al-ic') !!}</span><div><div class="al-stat-value">{{ number_format($todayCount) }}</div><div class="al-stat-label">Logged today</div></div></div>
                    <div class="al-stat"><span class="al-stat-icon t-amber">{!! \App\Support\SidebarMenu::icon('calendar', 'al-ic') !!}</span><div><div class="al-stat-value">{{ number_format($weekCount) }}</div><div class="al-stat-label">This week</div></div></div>
                    <div class="al-stat"><span class="al-stat-icon t-violet">{!! \App\Support\SidebarMenu::icon('layers', 'al-ic') !!}</span><div><div class="al-stat-value">{{ number_format($logs->total()) }}</div><div class="al-stat-label">{{ $filtered ? 'Matching your filters' : 'Showing' }}</div></div></div>
                </div>

                <div class="al-card">
                    <form method="GET" class="al-filters" id="audit-filters">
                        <div class="al-field wide">
                            <label for="al-search">Search</label>
                            <div class="al-search">
                                <svg class="al-ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                                <input type="text" id="al-search" name="search" placeholder="Description, action or user" value="{{ $search }}">
                            </div>
                        </div>
                        <div class="al-field">
                            <label for="al-action">Action</label>
                            <select id="al-action" name="action" onchange="this.form.submit()">
                                <option value="">All actions</option>
                                @foreach($actionTypes as $type)
                                    <option value="{{ $type }}" @selected($action == $type)>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="al-field">
                            <label for="al-user-type">User type</label>
                            <select id="al-user-type" name="user_type" onchange="this.form.submit()">
                                <option value="">All users</option>
                                @foreach($userTypes as $key => $label)
                                    <option value="{{ $key }}" @selected($userType === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="al-field">
                            <label for="al-from">From</label>
                            <input type="date" id="al-from" name="from" value="{{ $from }}" max="{{ now('Asia/Manila')->toDateString() }}" onchange="this.form.submit()">
                        </div>
                        <div class="al-field">
                            <label for="al-to">To</label>
                            <input type="date" id="al-to" name="to" value="{{ $to }}" max="{{ now('Asia/Manila')->toDateString() }}" onchange="this.form.submit()">
                        </div>
                        <div class="al-buttons">
                            <button type="submit" class="btn-save">{!! \App\Support\SidebarMenu::icon('arrow', 'al-ic') !!} Apply</button>
                            @if($filtered)
                                <a href="{{ url('/admin/audit-logs') }}" class="al-reset">Reset</a>
                            @endif
                        </div>
                    </form>

                    @if($filtered)
                        <div class="al-chips">
                            @if($search !== '')<span class="al-chip">“{{ $search }}”</span>@endif
                            @if($action)<span class="al-chip">{{ $action }}</span>@endif
                            @if($userType)<span class="al-chip">{{ $userTypes[$userType] }}</span>@endif
                            @if($from || $to)<span class="al-chip">{{ $from ? \Illuminate\Support\Carbon::parse($from)->format('M d, Y') : 'Start' }} – {{ $to ? \Illuminate\Support\Carbon::parse($to)->format('M d, Y') : 'Today' }}</span>@endif
                        </div>
                    @endif

                    <div class="al-table-wrap">
                        <table class="al-table">
                            <thead>
                                <tr>
                                    <th>Date &amp; Time</th>
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
                                            str_contains($log->action, 'Distributed'), str_contains($log->action, 'Sent') => 'action-distributed',
                                            str_contains($log->action, 'Returned'), str_contains($log->action, 'Removed'), str_contains($log->action, 'Withdrawn') => 'action-rejected',
                                            str_contains($log->action, 'Submitted') => 'action-revision',
                                            str_contains($log->action, 'Permissions') => 'action-permissions',
                                            default => 'action-default',
                                        };
                                        $when = $log->created_at->copy()->timezone('Asia/Manila');
                                    @endphp
                                    <tr>
                                        <td class="al-when"><strong>{{ $when->format('M d, Y') }}</strong><span>{{ $when->format('h:i A') }}</span></td>
                                        <td>
                                            <div class="al-user">
                                                @if($log->user)
                                                    <span class="al-avatar">{{ $log->user->initials }}</span>
                                                    <div><strong>{{ $log->user->name }}</strong><span>{{ $userTypes[$log->user->role] ?? ucfirst($log->user->role) }}</span></div>
                                                @else
                                                    <span class="al-avatar system">{!! \App\Support\SidebarMenu::icon('settings', 'al-ic') !!}</span>
                                                    <div><strong>System</strong><span>Automatic</span></div>
                                                @endif
                                            </div>
                                        </td>
                                        <td><span class="action-badge {{ $badgeClass }}">{{ $log->action }}</span></td>
                                        <td class="al-desc">{{ $log->description }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="al-empty">{!! \App\Support\SidebarMenu::icon('scroll', 'al-ic') !!}{{ $filtered ? 'No actions match these filters.' : 'No actions have been logged yet.' }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($logs->hasPages())
                        <div class="al-pages">
                            <div>Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }} results</div>
                            <div class="al-pager">
                                @if($logs->onFirstPage())
                                    <span class="is-disabled">‹ Previous</span>
                                @else
                                    <a href="{{ $logs->previousPageUrl() }}">‹ Previous</a>
                                @endif
                                @foreach($logs->getUrlRange(max(1, $logs->currentPage() - 2), min($logs->lastPage(), $logs->currentPage() + 2)) as $page => $url)
                                    @if($page == $logs->currentPage())
                                        <span class="is-current">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}">{{ $page }}</a>
                                    @endif
                                @endforeach
                                @if($logs->hasMorePages())
                                    <a href="{{ $logs->nextPageUrl() }}">Next ›</a>
                                @else
                                    <span class="is-disabled">Next ›</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


    <div id="logout-confirm-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:9999;align-items:center;justify-content:center;">
        <div style="background:#fff;border-radius:10px;padding:24px 26px;width:360px;max-width:92vw;box-shadow:0 8px 32px rgba(0,0,0,0.25);font-family:Arial,sans-serif;">
            <div style="font-size:15px;font-weight:700;color:#1a1a2e;margin-bottom:8px;">Log out?</div>
            <div style="font-size:13px;color:#555;margin-bottom:20px;">Are you sure you want to log out of your account?</div>
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <button type="button" onclick="document.getElementById('logout-confirm-overlay').style.display='none';" style="background:#fff;border:1px solid #ccc;color:#444;font-size:12.5px;font-weight:600;padding:8px 18px;border-radius:5px;cursor:pointer;font-family:Arial,sans-serif;">Cancel</button>
            <button type="button" onclick="document.getElementById('logout-form').submit();" style="background:#ef4444;color:#fff;border:none;font-size:12.5px;font-weight:600;padding:8px 18px;border-radius:5px;cursor:pointer;font-family:Arial,sans-serif;">Log Out</button>
            </div>
        </div>
    </div>
    <script>
        function openLogoutModal() { document.getElementById('logout-confirm-overlay').style.display = 'flex'; }
        document.getElementById('logout-confirm-overlay').addEventListener('click', function(e) { if (e.target === this) this.style.display = 'none'; });
    </script>

    @include('partials.system-feedback')
</body>
</html>