<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard – CBMA System</title>
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
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            overflow-y: auto;
        }

        .sidebar-logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 22px 16px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-logo img {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            object-fit: contain;
            background: #1b3d7a;
        }

        .sidebar-logo .brand {
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            margin-top: 8px;
        }

        .sidebar-logo .brand-sub {
            color: #a0b4d6;
            font-size: 10px;
            margin-top: 2px;
        }

        .nav-list { list-style: none; padding: 10px 0; flex: 1; }

        .nav-list li a {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 11px 20px;
            color: #c8d6ec;
            text-decoration: none;
            font-size: 13px;
            transition: background 0.15s, color 0.15s;
        }

        .nav-list li a:hover { background-color: rgba(255,255,255,0.08); color: #fff; }

        .nav-list li.active a {
            background-color: rgba(255,255,255,0.08);
            color: #ffffff;
            border-left: 3px solid #ffffff;
        }

        .nav-list li a svg { width: 18px; height: 18px; flex-shrink: 0; opacity: 0.85; }

        .sidebar-logout {
            padding: 12px 0;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-logout a {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 11px 20px;
            color: #c8d6ec;
            text-decoration: none;
            font-size: 13px;
            transition: background 0.15s;
        }

        .sidebar-logout a:hover { background-color: rgba(255,255,255,0.08); color: #fff; }

        /* ═══════════════════ MAIN ═══════════════════ */
        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background-color: #f5f6fa;
        }

        .topnav {
            background: #fff;
            border-bottom: 1px solid #e0e0e0;
            padding: 0 24px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .topnav .label { font-size: 12px; color: #666; }

        .topnav-right { display: flex; align-items: center; gap: 12px; }

        .role-badge {
            background-color: #0f2557;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            padding: 5px 16px;
            border-radius: 20px;
        }

        .user-info { display: flex; align-items: center; gap: 8px; }
        .user-text { text-align: right; }
        .user-name { font-size: 12px; font-weight: 600; color: #222; }
        .user-email { font-size: 10px; color: #888; }

        .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background-color: #0f2557;
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ═══════════════════ CONTENT ═══════════════════ */
        .content {
            flex: 1;
            overflow-y: auto;
            padding: 28px 28px 24px;
        }

        .page-title { font-size: 22px; font-weight: 700; color: #1a1a2e; margin-bottom: 4px; }
        .page-welcome { font-size: 13px; color: #777; margin-bottom: 24px; }

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
            display: flex;
            flex-direction: column;
            min-height: 100px;
            position: relative;
            transition: box-shadow 0.15s;
        }

        .stat-card.clickable { cursor: pointer; }
        .stat-card.clickable:hover { box-shadow: 0 2px 12px rgba(0,0,0,0.1); }

        .stat-label { font-size: 12px; color: #666; margin-bottom: 10px; }
        .stat-value { font-size: 26px; font-weight: 700; color: #1a1a2e; }
        .stat-icon { position: absolute; top: 18px; right: 18px; font-size: 22px; opacity: 0.75; }

        /* Bottom panels */
        .bottom-panels { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

        .panel {
            background: #fff;
            border-radius: 8px;
            border: 1px solid #e8e8e8;
            padding: 18px 20px;
        }

        .panel-header {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 14px;
        }

        .panel-header .ph-icon { color: #e6a817; }

        .empty-state {
            text-align: center;
            padding: 28px 10px;
            color: #bbb;
            font-size: 12px;
        }

        .empty-state .empty-icon { font-size: 30px; margin-bottom: 8px; opacity: 0.4; }

        .btn-view-all {
            display: block;
            width: 100%;
            padding: 9px;
            background-color: #0f2557;
            color: #fff;
            font-size: 12.5px;
            font-weight: 600;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            margin-top: 14px;
            text-decoration: none;
            transition: background 0.15s;
        }

        .btn-view-all:hover { background-color: #1a3a7a; }

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
            <li class="active">
                <a href="{{ url('/faculty/dashboard') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="{{ url('/faculty/my-template') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    My Template
                </a>
            </li>
            <li>
                <a href="{{ url('/faculty/exam-generator') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 7h8M8 12h8M8 17h4"/></svg>
                    Exam Generator
                </a>
            </li>
            <li>
                <a href="{{ url('/faculty/shared-library') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                    Shared Library
                </a>
            </li>
            <li>
                <a href="{{ url('/faculty/course-coordination') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20"/></svg>
                    Course Coordination
                </a>
            </li>
            <li><a href="{{ url('/faculty/analytics') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>Analytics</a></li>
            <li><a href="{{ url('/faculty/user-manuals') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>User manuals</a></li>
            <li><a href="{{ url('/faculty/calendar') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Calendar Of Activities</a></li>
            <li><a href="{{ url('/faculty/announcements') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>Announcements</a></li>
            <li><a href="{{ url('/faculty/submissions') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Submissions and Deadline</a></li>
            <li><a href="{{ url('/faculty/cms') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 010 14.14M4.93 4.93a10 10 0 000 14.14"/></svg>CMS</a></li>
        </ul>

        <div class="sidebar-logout">
            <a href="#" onclick="handleLogout()">
                <svg style="width:14px;height:14px;flex-shrink:0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Log out
            </a>
        </div>
    </aside>

    {{-- ════════════ MAIN ════════════ --}}
    <div class="main">

        <div class="topnav">
            <span class="label">Current role</span>
            <div class="topnav-right">
                <span class="role-badge">Faculty</span>
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

            <div class="page-title">Faculty Dashboard</div>
            <div class="page-welcome">Welcome back,</div>

            {{-- Stat Cards --}}
            <div class="stat-cards">
                <div class="stat-card">
                    <span class="stat-label">Active Courses</span>
                    <span class="stat-value">—</span>
                    <span class="stat-icon">📖</span>
                </div>
                <div class="stat-card clickable" onclick="window.location.href='{{ url('/faculty/exam-generator') }}'">
                    <span class="stat-label">Exams Created</span>
                    <span class="stat-value">—</span>
                    <span class="stat-icon">📄</span>
                </div>
                <div class="stat-card clickable" onclick="window.location.href='{{ url('/faculty/my-template') }}'">
                    <span class="stat-label">Templates Approved</span>
                    <span class="stat-value">—</span>
                    <span class="stat-icon">📋</span>
                </div>
            </div>

            {{-- Bottom Panels --}}
            <div class="bottom-panels">
                <div class="panel">
                    <div class="panel-header">
                        <span class="ph-icon">🕐</span>
                        Upcoming Deadlines
                    </div>
                    <div class="empty-state">
                        <div class="empty-icon">📭</div>
                        No upcoming deadlines.
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-header">
                        <span class="ph-icon">🔔</span>
                        Recent Activity
                    </div>
                    <div class="empty-state">
                        <div class="empty-icon">📭</div>
                        No recent activity.
                    </div>
                    <a href="#" class="btn-view-all">View All Activity</a>
                </div>
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