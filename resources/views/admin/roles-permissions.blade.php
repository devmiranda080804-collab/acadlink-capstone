<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles & Permissions – CBMA System</title>
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

        .alert-success { background: #dcfce7; color: #166534; padding: 12px 16px; margin-bottom: 18px; border: 1px solid #bbf7d0; border-radius: 8px; font-size: 13px; }

        .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 24px; }
        .page-header-left h1 { font-size: 20px; font-weight: 700; color: #1a1a2e; }
        .page-header-left p { font-size: 12.5px; color: #666; margin-top: 3px; }

        .btn-save { display: flex; align-items: center; gap: 6px; background: #0f2557; color: #fff; border: none; border-radius: 6px; font-size: 12.5px; font-weight: 600; padding: 9px 18px; cursor: pointer; transition: background 0.15s; }
        .btn-save:hover { background: #1a3a7a; }

        .layout { display: flex; gap: 20px; }

        /* Role tabs (vertical) */
        .role-sidebar { width: 180px; flex-shrink: 0; }
        .role-tab { display: block; padding: 10px 14px; font-size: 13px; color: #444; text-decoration: none; border-radius: 6px; margin-bottom: 4px; transition: background 0.15s; }
        .role-tab:hover { background: #e8ecf8; color: #0f2557; }
        .role-tab.active { background: #0f2557; color: #fff; font-weight: 600; }

        /* Permissions table */
        .perm-panel { flex: 1; background: #fff; border: 1px solid #e4e4e4; border-radius: 8px; overflow: hidden; }
        .perm-table { width: 100%; border-collapse: collapse; }
        .perm-table thead tr { background: #fafafa; border-bottom: 1px solid #eee; }
        .perm-table th { padding: 12px 20px; text-align: left; font-size: 12px; font-weight: 700; color: #666; }
        .perm-table td { padding: 14px 20px; border-bottom: 1px solid #f5f5f5; font-size: 13px; color: #333; }
        .perm-table tbody tr:last-child td { border-bottom: none; }
        .perm-table tbody tr:hover { background: #fafbff; }

        /* Custom checkbox */
        .perm-check { width: 18px; height: 18px; accent-color: #1d4ed8; cursor: pointer; }

        svg { display: inline-block; vertical-align: middle; }

        .perm-check:disabled { opacity: 0.6; cursor: not-allowed; }
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
    <li class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
        <a href="{{ url('/admin/dashboard') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
    </li>
    @endif
    @if($navPermissions['account-management'] ?? true)
    <li class="{{ request()->is('admin/account-management*') ? 'active' : '' }}">
        <a href="{{ url('/admin/account-management') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
            Account Management
        </a>
    </li>
    @endif
    @if($navPermissions['roles-permissions'] ?? true)
    <li class="{{ request()->is('admin/roles-permissions*') ? 'active' : '' }}">
        <a href="{{ url('/admin/roles-permissions') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            Roles & Permissions
        </a>
    </li>
    @endif
    @if($navPermissions['template-approvals'] ?? true)
    <li class="{{ request()->is('admin/template-approvals*') ? 'active' : '' }}">
        <a href="{{ url('/admin/template-approvals') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
            Template Approvals
        </a>
    </li>
    @endif
    @if($navPermissions['audit-logs'] ?? true)
    <li class="{{ request()->is('admin/audit-logs*') ? 'active' : '' }}">
        <a href="{{ url('/admin/audit-logs') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            Audit Logs
        </a>
    </li>
    @endif
    @if($navPermissions['announcements'] ?? true)
    <li class="{{ request()->is('admin/announcements*') ? 'active' : '' }}">
        <a href="{{ url('/admin/announcements') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
            Announcements
        </a>
    </li>
    @endif
    @if($navPermissions['calendar'] ?? true)
    <li class="{{ request()->is('admin/calendar*') ? 'active' : '' }}">
        <a href="{{ url('/admin/calendar') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Calendar of Activities
        </a>
    </li>
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
                <div class="page-header-left">
                    <h1>Roles & Permissions</h1>
                    <p>Viewing access for {{ ucwords(str_replace('_', ' ', $selectedRole)) }}</p>
                </div>
                <button type="submit" form="permissions-form" class="btn-save">
                    <svg style="width:14px;height:14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Save Changes
                </button>
            </div>

            <div class="layout">

                {{-- Role tabs --}}
                <div class="role-sidebar">
                    @foreach(['program_head' => 'Program Head', 'secretary' => 'Secretary', 'faculty' => 'Faculty'] as $roleKey => $roleLabel)
                        <a href="{{ url('/admin/roles-permissions/' . $roleKey) }}"
                        class="role-tab {{ $selectedRole === $roleKey ? 'active' : '' }}">
                            {{ $roleLabel }}
                        </a>
                    @endforeach
                </div>

                {{-- Permissions table --}}
                <div class="perm-panel">
                    <form id="permissions-form" method="POST" action="{{ url('/admin/roles-permissions/' . $selectedRole) }}">
                        @csrf
                        <table class="perm-table">
                            <thead>
                                <tr>
                                    <th>Module</th>
                                    <th>{{ ucwords(str_replace('_', ' ', $selectedRole)) }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($modules as $slug => $label)
                                    <tr>
                                        <td>{{ $label }}</td>
                                        <td>
                                            @if($slug === 'dashboard')
                                                {{-- Dashboard ay laging naka-check at hindi ma-uncheck --}}
                                                <input type="checkbox" class="perm-check" checked disabled title="Dashboard is always enabled">
                                                <input type="hidden" name="permissions[dashboard]" value="1">
                                            @else
                                                <input
                                                    type="checkbox"
                                                    class="perm-check"
                                                    name="permissions[{{ $slug }}]"
                                                    {{ ($permissions[$slug] ?? true) ? 'checked' : '' }}>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </form>
                </div>

            </div>
        </div>
    </div>

</body>
</html>