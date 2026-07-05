<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program Head Dashboard – CBMA System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* ═══════════════════ SIDEBAR ═══════════════════ */
        .sidebar {
            width: 210px;
            background-color: #0f2557;
            display: flex; flex-direction: column;
            flex-shrink: 0; overflow-y: auto;
        }

        .sidebar-logo {
            display: flex; flex-direction: column; align-items: center;
            padding: 22px 16px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-logo img { width: 72px; height: 72px; border-radius: 50%; object-fit: contain; background: #1b3d7a; }
        .sidebar-logo .brand { color: #fff; font-size: 15px; font-weight: 700; margin-top: 8px; }
        .sidebar-logo .brand-sub { color: #a0b4d6; font-size: 10px; margin-top: 2px; }

        .nav-list { list-style: none; padding: 10px 0; flex: 1; }

        .nav-list li a {
            display: flex; align-items: center; gap: 11px;
            padding: 11px 20px;
            color: #c8d6ec; text-decoration: none; font-size: 13px;
            transition: background 0.15s, color 0.15s;
        }

        .nav-list li a:hover { background-color: rgba(255,255,255,0.08); color: #fff; }
        .nav-list li.active a { background-color: rgba(255,255,255,0.08); color: #fff; border-left: 3px solid #fff; }
        .nav-list li a svg { width: 18px; height: 18px; flex-shrink: 0; opacity: 0.85; }

        .sidebar-logout { padding: 12px 0; border-top: 1px solid rgba(255,255,255,0.1); }
        .sidebar-logout a {
            display: flex; align-items: center; gap: 11px;
            padding: 11px 20px; color: #c8d6ec; text-decoration: none; font-size: 13px;
            transition: background 0.15s;
        }
        .sidebar-logout a:hover { background-color: rgba(255,255,255,0.08); color: #fff; }

        /* ═══════════════════ MAIN ═══════════════════ */
        .main { flex: 1; display: flex; flex-direction: column; overflow: hidden; background-color: #f5f6fa; }

        .topnav {
            background: #fff; border-bottom: 1px solid #e0e0e0;
            padding: 0 24px; height: 52px;
            display: flex; align-items: center; justify-content: space-between;
            flex-shrink: 0;
        }

        .topnav .label { font-size: 12px; color: #666; }
        .topnav-right { display: flex; align-items: center; gap: 12px; }
        .role-badge { background-color: #0f2557; color: #fff; font-size: 12px; font-weight: 600; padding: 5px 16px; border-radius: 20px; }
        .user-info { display: flex; align-items: center; gap: 8px; }
        .user-text { text-align: right; }
        .user-name { font-size: 12px; font-weight: 600; color: #222; }
        .user-email { font-size: 10px; color: #888; }
        .user-avatar { width: 34px; height: 34px; border-radius: 50%; background-color: #0f2557; color: #fff; font-size: 12px; font-weight: 700; display: flex; align-items: center; justify-content: center; }

        /* ═══════════════════ CONTENT ═══════════════════ */
        .content { flex: 1; overflow-y: auto; padding: 28px 28px 24px; }

        .page-title { font-size: 22px; font-weight: 700; color: #1a1a2e; margin-bottom: 4px; }
        .page-sub { font-size: 13px; color: #777; margin-bottom: 24px; }

        /* Stat cards */
        .stat-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 22px;
        }

        .stat-card {
            background: #fff;
            border-radius: 8px;
            padding: 18px 20px;
            border: 1px solid #e8e8e8;
            position: relative;
            min-height: 100px;
        }

        .stat-label { font-size: 12px; color: #666; margin-bottom: 10px; }
        .stat-value { font-size: 26px; font-weight: 700; color: #1a1a2e; }
        .stat-icon { position: absolute; top: 18px; right: 18px; font-size: 22px; opacity: 0.75; }

        /* Recent Submissions panel */
        .submissions-panel {
            background: #fff;
            border: 1px solid #e8e8e8;
            border-radius: 8px;
            padding: 20px 24px;
        }

        .submissions-header {
            display: flex; align-items: center; gap: 8px;
            font-size: 14px; font-weight: 700; color: #1a1a2e;
            margin-bottom: 16px;
        }

        .submissions-header .trend-icon { color: #e6a817; font-size: 16px; }

        .submission-item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f5f5f5;
        }

        .submission-item:last-child { border-bottom: none; }

        .submission-left { display: flex; align-items: flex-start; gap: 10px; }

        .sub-dot { width: 8px; height: 8px; border-radius: 50%; background: #e6a817; flex-shrink: 0; margin-top: 5px; }

        .sub-title { font-size: 13px; font-weight: 600; color: #1a1a2e; margin-bottom: 3px; }
        .sub-meta  { font-size: 11px; color: #aaa; }

        .btn-review {
            background: #f5f5f5;
            border: 1px solid #ddd;
            color: #555;
            font-size: 11.5px;
            font-weight: 600;
            padding: 5px 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.15s;
            flex-shrink: 0;
        }
        .btn-review:hover { background: #e8e8e8; }

        .submissions-empty {
            text-align: center; padding: 28px 10px;
            color: #bbb; font-size: 12.5px;
        }

        svg { display: inline-block; vertical-align: middle; }
    </style>
</head>
<body>

    {{-- ════════════ SIDEBAR ════════════ --}}
    <aside class="sidebar">
    <div class="sidebar-logo">
        <img src="{{ asset('images/cbma-logo.png') }}" alt="CBMA Logo">
        <span class="brand">CBMA</span>
        <span class="brand-sub">Academic Coordination</span>
    </div>

    <ul class="nav-list">
        @if($navPermissions['dashboard'] ?? true)
        <li class="{{ request()->is('program-head/dashboard') ? 'active' : '' }}">
            <a href="{{ url('/program-head/dashboard') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
        </li>
        @endif
        @if($navPermissions['template-review'] ?? true)
        <li class="{{ request()->is('program-head/template-review*') ? 'active' : '' }}">
            <a href="{{ url('/program-head/template-review') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                Template Review
            </a>
        </li>
        @endif
        @if($navPermissions['course-oversight'] ?? true)
        <li class="{{ request()->is('program-head/course-oversight*') ? 'active' : '' }}">
            <a href="{{ url('/program-head/course-oversight') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                Course Oversight
            </a>
        </li>
        @endif
        @if($navPermissions['account-management'] ?? true)
        <li class="{{ request()->is('program-head/account-management*') ? 'active' : '' }}">
            <a href="{{ url('/program-head/account-management') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                Account Management
            </a>
        </li>
        @endif
        @if($navPermissions['announcements'] ?? true)
        <li class="{{ request()->is('program-head/announcements*') ? 'active' : '' }}">
            <a href="{{ url('/program-head/announcements') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                Announcements
            </a>
        </li>
        @endif
        @if($navPermissions['calendar'] ?? true)
        <li class="{{ request()->is('program-head/calendar*') ? 'active' : '' }}">
            <a href="{{ url('/program-head/calendar') }}">
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

    {{-- ════════════ MAIN ════════════ --}}
    <div class="main">

        <div class="topnav">
            <span class="label">Current role</span>
            <div class="topnav-right">
                <span class="role-badge">Program head</span>
                <div class="user-info">
                    <div class="user-text">
                        <div class="user-name">—</div>
                        <div class="user-email">—</div>
                    </div>
                    <div class="user-avatar">—</div>
                </div>
            </div>
        </div>

        <div class="content">

            <div class="page-title">Program Head Dashboard</div>
            <div class="page-sub">Overview of templates and course supervision</div>

            {{-- Stat Cards --}}
            <div class="stat-cards">
                <div class="stat-card">
                    <div class="stat-label">Templates Pending Review</div>
                    <div class="stat-value">—</div>
                    <div class="stat-icon">📄</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Courses Supervised</div>
                    <div class="stat-value">—</div>
                    <div class="stat-icon">📖</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Flagged Inconsistencies</div>
                    <div class="stat-value">—</div>
                    <div class="stat-icon">⚠️</div>
                </div>
            </div>

            {{-- Recent Submissions --}}
            <div class="submissions-panel">
                <div class="submissions-header">
                    <span class="trend-icon">📈</span>
                    Recent Submissions
                </div>
                <div class="submissions-empty">No recent submissions.</div>
            </div>

        </div>
    </div>

    <script>
        function handleLogout() {
            window.location.href = '{{ url("/login") }}';
        }
    </script>

</body>
</html>