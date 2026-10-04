<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Templates – CBMA System</title>
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
        .alert-success { background: #dcfce7; color: #166534; padding: 12px 16px; margin-bottom: 16px; border: 1px solid #bbf7d0; border-radius: 8px; font-size: 13px; }

        .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 20px; }
        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 3px; }
        .page-sub { font-size: 11.5px; color: #888; max-width: 520px; }

        .btn-create { display: flex; align-items: center; gap: 6px; background: #0f2557; color: #fff; border: none; border-radius: 6px; font-size: 12.5px; font-weight: 600; padding: 9px 18px; cursor: pointer; transition: background 0.15s; white-space: nowrap; }
        .btn-create:hover { background: #1a3a7a; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 16px; margin-bottom: 22px; }
        .stat-card { background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; padding: 18px 20px; display: flex; align-items: center; gap: 14px; }
        .stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .stat-icon svg { width: 22px; height: 22px; }
        .stat-icon.blue { background: #dbeafe; color: #1d4ed8; }
        .stat-icon.amber { background: #fef3c7; color: #b45309; }
        .stat-icon.green { background: #d1fae5; color: #059669; }
        .stat-info .stat-value { font-size: 24px; font-weight: 700; color: #1a1a2e; line-height: 1.1; }
        .stat-info .stat-label { font-size: 11.5px; color: #888; margin-top: 2px; }

        .toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 18px; flex-wrap: wrap; }
        .filter-tabs { display: flex; gap: 4px; }
        .filter-tab { padding: 7px 16px; font-size: 12px; color: #666; background: #fff; border: 1px solid #e0e0e0; border-radius: 6px; cursor: pointer; user-select: none; }
        .filter-tab:hover { border-color: #0f2557; color: #0f2557; }
        .filter-tab.active { background: #0f2557; color: #fff; border-color: #0f2557; font-weight: 600; }

        .search-box { position: relative; width: 240px; max-width: 100%; }
        .search-box svg { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: #999; pointer-events: none; }
        .search-box input { width: 100%; padding: 8px 10px 8px 30px; border: 1px solid #ddd; border-radius: 6px; font-size: 12px; outline: none; font-family: Arial, sans-serif; }
        .search-box input:focus { border-color: #0f2557; }

        {{-- Needs Attention: pinned, never buried under everything already handled --}}
        .attention-banner { background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 16px 18px; margin-bottom: 22px; }
        .attention-banner-title { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: #92400e; margin-bottom: 12px; }

        {{-- Folder (library-shelf) browsing --}}
        .folder-section { margin-bottom: 26px; }
        .folder-section-title { display: flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 700; color: #1a1a2e; margin-bottom: 12px; }
        .folder-section-count { font-size: 10.5px; font-weight: 600; color: #999; background: #f0f0f0; padding: 2px 8px; border-radius: 10px; }
        .folder-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); gap: 14px; }
        .folder-card { position: relative; display: flex; align-items: center; gap: 12px; background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; padding: 14px 16px; cursor: pointer; transition: box-shadow 0.15s, transform 0.15s, border-color 0.15s; text-align: left; }
        .folder-card:hover { box-shadow: 0 6px 18px rgba(15,37,87,0.1); transform: translateY(-2px); border-color: #c7d2e8; }
        .folder-card-icon { font-size: 30px; line-height: 1; flex-shrink: 0; }
        .folder-card-label { font-size: 12.5px; font-weight: 700; color: #1a1a2e; line-height: 1.3; }
        .folder-card-count { font-size: 10.5px; color: #999; margin-top: 3px; }
        .folder-card-pending-badge { position: absolute; top: -7px; right: -7px; background: #f59e0b; color: #fff; font-size: 10px; font-weight: 700; min-width: 18px; height: 18px; padding: 0 5px; border-radius: 999px; display: flex; align-items: center; justify-content: center; }

        .breadcrumb { display: flex; align-items: center; gap: 10px; margin-bottom: 18px; }
        .btn-back { display: inline-flex; align-items: center; gap: 5px; background: #fff; border: 1px solid #ddd; color: #444; font-size: 12px; font-weight: 600; padding: 7px 13px; border-radius: 6px; cursor: pointer; }
        .btn-back:hover { border-color: #0f2557; color: #0f2557; }
        .btn-back svg { width: 13px; height: 13px; }
        .breadcrumb-label { font-size: 13.5px; font-weight: 700; color: #1a1a2e; }

        .template-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px; }
        .tmpl-card { background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; padding: 16px 18px; display: flex; flex-direction: column; transition: box-shadow 0.15s; }
        .tmpl-card:hover { box-shadow: 0 3px 12px rgba(0,0,0,0.07); }
        .tmpl-card.forwarded { border-left: 4px solid #1e40af; }
        .tmpl-card.waiting { border-left: 4px solid #f59e0b; }
        .tmpl-card-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 8px; gap: 8px; }
        .tmpl-card-icon { font-size: 22px; line-height: 1; }
        .tmpl-card-title { font-size: 13.5px; font-weight: 700; color: #1a1a2e; margin-bottom: 4px; }
        .tmpl-card-type { font-size: 11px; color: #888; text-transform: capitalize; }
        .tmpl-card-meta { font-size: 10.5px; color: #aaa; margin: 8px 0 10px; }

        .program-tags { display: flex; gap: 4px; flex-wrap: wrap; margin-bottom: 14px; }
        .program-tag { display: inline-block; font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 10px; }
        .program-tag.distributed { background: #d1fae5; color: #065f46; }
        .program-tag.pending { background: #f3f4f6; color: #888; }

        .status-badge { font-size: 10px; font-weight: 700; padding: 4px 10px; border-radius: 12px; white-space: nowrap; }
        .status-waiting { background: #fef9c3; color: #92400e; }
        .status-forwarded { background: #dbeafe; color: #1e40af; }

        .empty-state { grid-column: 1 / -1; text-align: center; padding: 50px 20px; color: #bbb; font-size: 13px; background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; }

        .action-buttons { display: flex; gap: 6px; margin-top: auto; }
        .btn-del { display: inline-flex; align-items: center; justify-content: center; gap: 4px; background: #fff; color: #ef4444; border: 1px solid #fca5a5; font-size: 11.5px; font-weight: 600; padding: 8px 12px; border-radius: 5px; cursor: pointer; flex: 1; }
        .btn-del:hover { background: #fef2f2; }
        .btn-view { display: inline-flex; align-items: center; justify-content: center; gap: 4px; background: #fff; color: #444; border: 1px solid #d0d0d0; font-size: 11.5px; font-weight: 600; padding: 8px 12px; border-radius: 5px; text-decoration: none; flex: 1; }
        .btn-view:hover { background: #f5f5f5; }
        .btn-del svg, .btn-view svg { width: 12px; height: 12px; }

        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 999; align-items: center; justify-content: center; }
        .modal-overlay.open { display: flex; }
        .modal { background: #fff; border-radius: 10px; padding: 24px 26px; width: 440px; max-width: 95vw; box-shadow: 0 8px 32px rgba(0,0,0,0.25); }
        .modal-title { font-size: 15px; font-weight: 700; color: #1a1a2e; margin-bottom: 16px; }
        .modal-field { margin-bottom: 13px; }
        .modal-field label { display: block; font-size: 11.5px; font-weight: 700; color: #333; margin-bottom: 4px; }
        .modal-field input, .modal-field select { width: 100%; padding: 8px 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 12.5px; outline: none; font-family: Arial, sans-serif; }
        .modal-field select { appearance: none; -webkit-appearance: none; background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%23666' d='M5 7L0 2h10z'/%3E%3C/svg%3E") no-repeat right 10px center; cursor: pointer; }
        .modal-error { display: none; background: #fee2e2; border: 1px solid #fca5a5; color: #b91c1c; font-size: 11px; padding: 8px 10px; border-radius: 4px; margin-bottom: 12px; }
        .modal-hint { font-size: 10.5px; color: #999; margin-top: 4px; }
        .program-checklist { border: 1px solid #ccc; border-radius: 5px; padding: 8px 10px; max-height: 150px; overflow-y: auto; }
        .program-check-all { display: flex; align-items: center; gap: 7px; font-size: 12px; font-weight: 700; color: #0f2557; padding: 4px 2px 7px; margin-bottom: 6px; border-bottom: 1px solid #eee; cursor: pointer; }
        .program-check { display: flex; align-items: center; gap: 7px; font-size: 12px; color: #333; padding: 3px 2px; cursor: pointer; }
        .program-checklist input[type=checkbox] { width: auto; cursor: pointer; }
        .modal-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 18px; }
        .btn-cancel { background: #fff; border: 1px solid #ccc; color: #444; font-size: 12.5px; font-weight: 600; padding: 8px 18px; border-radius: 5px; cursor: pointer; }
        .btn-cancel:hover { background: #f5f5f5; }
        .btn-save { background: #0f2557; color: #fff; border: none; font-size: 12.5px; font-weight: 600; padding: 8px 20px; border-radius: 5px; cursor: pointer; }
        .btn-save:hover { background: #1a3a7a; }
        .btn-danger { background: #ef4444; color: #fff; border: none; font-size: 12.5px; font-weight: 600; padding: 8px 20px; border-radius: 5px; cursor: pointer; }
        .btn-danger:hover { background: #dc2626; }
        .delete-target { font-size: 13px; color: #555; margin-bottom: 4px; }
        .delete-target strong { color: #1a1a2e; }

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
            <li class="{{ request()->is('admin/template-approvals*') ? 'active' : '' }}"><a href="{{ url('/admin/template-approvals') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>Templates</a></li>
            @endif
            @if($navPermissions['program-assignment'] ?? true)
            <li class="{{ request()->is('admin/program-assignment*') ? 'active' : '' }}"><a href="{{ url('/admin/program-assignment') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>Program Assignment</a></li>
            @endif
            @if($navPermissions['audit-logs'] ?? true)
            <li class="{{ request()->is('admin/audit-logs*') ? 'active' : '' }}"><a href="{{ url('/admin/audit-logs') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>Audit Logs</a></li>
            @endif
            @if($navPermissions['announcements'] ?? true)
            <li class="{{ request()->is('admin/announcements*') ? 'active' : '' }}"><a href="{{ url('/admin/announcements') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>Announcements
                @if(($unreadAnnouncementsCount ?? 0) > 0)
                    <span class="nav-badge">{{ $unreadAnnouncementsCount }}</span>
                @endif
            </a></li>
            @endif
            @if($navPermissions['calendar'] ?? true)
            <li class="{{ request()->is('admin/calendar*') ? 'active' : '' }}"><a href="{{ url('/admin/calendar') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Calendar of Activities</a></li>
            @endif
            @if($navPermissions['analytics'] ?? true)
            <li class="{{ request()->is('admin/analytics*') ? 'active' : '' }}"><a href="{{ url('/admin/analytics') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>Analytics</a></li>
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

            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif

            <div class="page-header">
                <div>
                    <div class="page-title">Templates</div>
                    <div class="page-sub">Upload the official template formats here. Each one is relayed by the Secretary to every Program Head, who then distributes it to their own faculty.</div>
                </div>
                <button class="btn-create" type="button" onclick="openCreateModal()">+ New Template</button>
            </div>

            @php
                $waitingCount = $templates->filter(fn($t) => !$t->isForwarded())->count();
                $forwardedCount = $templates->count() - $waitingCount;
            @endphp

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
                    <div class="stat-info"><div class="stat-value">{{ $templates->count() }}</div><div class="stat-label">Total Templates</div></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon amber"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
                    <div class="stat-info"><div class="stat-value">{{ $waitingCount }}</div><div class="stat-label">Awaiting Secretary</div></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
                    <div class="stat-info"><div class="stat-value">{{ $forwardedCount }}</div><div class="stat-label">Forwarded</div></div>
                </div>
            </div>

            <div class="toolbar">
                <div class="search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" id="template-search" placeholder="Search all templates by title..." oninput="onSearchInput(this.value)">
                </div>
            </div>

            @php
                $waitingTemplates = $templates->filter(fn($t) => !$t->isForwarded());
                $typeFolders = $templates->groupBy('type')->map(function ($group, $type) {
                    return [
                        'key'     => 'type-' . $type,
                        'label'   => str_replace('_', ' ', ucfirst($type)) . ' Templates',
                        'icon'    => $type === 'syllabus' ? '📘' : ($type === 'course_guide' ? '📙' : '📝'),
                        'count'   => $group->count(),
                        'pending' => $group->filter(fn($t) => !$t->isForwarded())->count(),
                    ];
                })->sortBy('label')->values();
            @endphp

            {{-- Needs Attention: pinned so nothing awaiting forwarding gets buried once there are many Forwarded templates --}}
            @if($waitingTemplates->isNotEmpty())
                <div class="attention-banner">
                    <div class="attention-banner-title">
                        <svg style="width:16px;height:16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        Needs Attention — Awaiting Secretary ({{ $waitingTemplates->count() }})
                    </div>
                    <div class="template-grid">
                        @foreach($waitingTemplates as $template)
                            @include('admin.partials.template-card', ['template' => $template])
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- FOLDER HOME: templates arranged by type, like library shelves --}}
            <div id="folder-home">
                <div class="folder-section">
                    <div class="folder-section-title">
                        All Templates
                        <span class="folder-section-count">{{ $templates->count() }} file{{ $templates->count() == 1 ? '' : 's' }}</span>
                    </div>
                    @if($typeFolders->isEmpty())
                        <div class="empty-state">No templates uploaded yet.</div>
                    @else
                        <div class="folder-grid">
                            @foreach($typeFolders as $folder)
                                <button type="button" class="folder-card" onclick="openFolder('{{ $folder['key'] }}', @js($folder['label']))">
                                    @if($folder['pending'] > 0)
                                        <span class="folder-card-pending-badge" title="{{ $folder['pending'] }} awaiting Secretary">{{ $folder['pending'] }}</span>
                                    @endif
                                    <span class="folder-card-icon">{{ $folder['icon'] }}</span>
                                    <div>
                                        <div class="folder-card-label">{{ $folder['label'] }}</div>
                                        <div class="folder-card-count">{{ $folder['count'] }} file{{ $folder['count'] == 1 ? '' : 's' }}</div>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- FILE VIEW: shown after opening a folder, or while searching --}}
            <div id="file-view" style="display:none;">
                <div class="breadcrumb">
                    <button type="button" class="btn-back" onclick="backToFolders()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                        Back to Library
                    </button>
                    <span class="breadcrumb-label" id="file-view-title"></span>
                </div>

                <div class="template-grid" id="template-grid">
                    @foreach($templates as $template)
                        @include('admin.partials.template-card', ['template' => $template, 'folderKey' => 'type-' . $template->type])
                    @endforeach
                </div>
                <div class="empty-state" id="no-search-results" style="display:none;">No templates match.</div>
            </div>

        </div>
    </div>

    {{-- Upload Modal --}}
    <div class="modal-overlay" id="modal-overlay">
        <div class="modal">
            <form action="{{ url('/admin/template-approvals') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-title">New Template</div>
                <div class="modal-error" id="modal-error">{{ $errors->first() }}</div>

                <div class="modal-field">
                    <label>Title <span style="color:#ef4444">*</span></label>
                    <input type="text" name="title" placeholder="e.g. Official Syllabus Format 2026" value="{{ old('title') }}">
                </div>

                <div class="modal-field">
                    <label>Template Type <span style="color:#ef4444">*</span></label>
                    <select name="type">
                        <option value="syllabus">Syllabus</option>
                        <option value="course_guide">Course Guide</option>
                        <option value="module">Module</option>
                    </select>
                </div>

                <div class="modal-field">
                    <label>Send to Programs <span style="color:#ef4444">*</span></label>
                    <div class="program-checklist">
                        <label class="program-check-all">
                            <input type="checkbox" id="programs-select-all" onchange="toggleAllPrograms(this)">
                            <span>All Programs</span>
                        </label>
                        @foreach(\App\Support\Programs::options() as $code => $label)
                            <label class="program-check">
                                <input type="checkbox" name="programs[]" value="{{ $code }}" class="program-check-item" onchange="syncSelectAll()" {{ in_array($code, old('programs', [])) ? 'checked' : '' }}>
                                <span title="{{ $label }}">{{ $code }} — {{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="modal-field">
                    <label>File <span style="color:#ef4444">*</span></label>
                    <input type="file" name="file" accept=".pdf,.doc,.docx" required>
                    <div class="modal-hint">Secretary forwards it, Program Head distributes it, and faculty can view/download it.</div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-save">Create</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal-overlay" id="delete-overlay">
        <div class="modal" style="width:420px;">
            <div class="modal-title">Remove Template</div>
            <div class="delete-target">Remove <strong id="delete-target-title"></strong>? This cannot be undone.</div>
            <form id="delete-form" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
                    <button type="submit" class="btn-danger">Remove Template</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openDeleteModal(id, title) {
            document.getElementById('delete-target-title').textContent = title;
            document.getElementById('delete-form').action = '{{ url("/admin/template-approvals") }}/' + id;
            document.getElementById('delete-overlay').classList.add('open');
        }
        function closeDeleteModal() {
            document.getElementById('delete-overlay').classList.remove('open');
        }
        document.getElementById('delete-overlay').addEventListener('click', function(e) {
            if (e.target === this) closeDeleteModal();
        });

        var currentFolder = null; // null = no folder open (home or global search)
        var currentFolderLabel = '';

        function openFolder(key, label) {
            currentFolder = key;
            currentFolderLabel = label;
            document.getElementById('template-search').value = '';
            document.getElementById('folder-home').style.display = 'none';
            document.getElementById('file-view').style.display = '';
            document.getElementById('file-view-title').textContent = label;
            renderFileView();
        }

        function backToFolders() {
            currentFolder = null;
            currentFolderLabel = '';
            document.getElementById('template-search').value = '';
            document.getElementById('file-view').style.display = 'none';
            document.getElementById('folder-home').style.display = '';
        }

        function onSearchInput(query) {
            query = query.trim();
            if (query === '') {
                if (currentFolder) {
                    document.getElementById('file-view-title').textContent = currentFolderLabel;
                    renderFileView();
                } else {
                    document.getElementById('file-view').style.display = 'none';
                    document.getElementById('folder-home').style.display = '';
                }
                return;
            }

            document.getElementById('folder-home').style.display = 'none';
            document.getElementById('file-view').style.display = '';
            document.getElementById('file-view-title').textContent = 'Search results for "' + query + '"';
            renderFileView(query);
        }

        function renderFileView(searchQuery) {
            var cards = document.querySelectorAll('#template-grid .tmpl-card');
            var q = (searchQuery || '').toLowerCase();
            var visibleCount = 0;

            cards.forEach(function(card) {
                var matchesFolder = searchQuery ? true : (!currentFolder || card.dataset.folder === currentFolder);
                var matchesSearch = !q || card.dataset.title.includes(q);
                var visible = matchesFolder && matchesSearch;
                card.style.display = visible ? '' : 'none';
                if (visible) visibleCount++;
            });

            var noResults = document.getElementById('no-search-results');
            if (noResults) {
                noResults.style.display = (visibleCount === 0) ? 'block' : 'none';
            }
        }

        function toggleAllPrograms(selectAllCheckbox) {
            document.querySelectorAll('.program-check-item').forEach(function(cb) {
                cb.checked = selectAllCheckbox.checked;
            });
        }
        function syncSelectAll() {
            var items = document.querySelectorAll('.program-check-item');
            var allChecked = Array.from(items).every(function(cb) { return cb.checked; });
            document.getElementById('programs-select-all').checked = allChecked;
        }

        function openCreateModal() {
            document.getElementById('modal-overlay').classList.add('open');
        }
        function closeModal() {
            document.getElementById('modal-overlay').classList.remove('open');
        }
        document.getElementById('modal-overlay').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        @if($errors->any())
            document.addEventListener('DOMContentLoaded', function () {
                document.getElementById('modal-overlay').classList.add('open');
                document.getElementById('modal-error').style.display = 'block';
            });
        @endif
    </script>

</body>
</html>
