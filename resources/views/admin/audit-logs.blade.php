<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs – CBMA System</title>
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
        .content { flex: 1; overflow-y: auto; padding: 24px 28px 28px; }

        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 3px; }
        .page-sub { font-size: 11.5px; color: #888; margin-bottom: 18px; }

        /* ── Filter row ── */
        .filter-row {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 18px;
        }

        .search-wrap { position: relative; flex: 1; max-width: 280px; }
        .search-wrap svg { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: #aaa; }
        .search-wrap input {
            width: 100%; height: 36px; padding: 0 10px 0 32px;
            border: 1px solid #ccc; border-radius: 5px;
            font-size: 12px; color: #333; outline: none; background: #fff;
            transition: border-color 0.15s;
        }
        .search-wrap input:focus { border-color: #0f2557; }

        .filter-select {
            height: 36px; padding: 0 28px 0 12px;
            border: 1px solid #ccc; border-radius: 5px;
            font-size: 12px; color: #333; background: #fff;
            outline: none; appearance: none; -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%23666' d='M5 7L0 2h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 10px center;
            cursor: pointer; min-width: 130px;
        }

        .date-wrap {
            display: flex; align-items: center; gap: 6px;
            border: 1px solid #ccc; border-radius: 5px;
            padding: 0 10px; height: 36px; background: #fff;
            font-size: 12px; color: #888; cursor: pointer;
        }

        .date-wrap input[type="date"] {
            border: none; outline: none; font-size: 12px;
            color: #333; background: transparent; cursor: pointer;
        }

        /* ── Log list panel ── */
        .log-panel {
            background: #fff;
            border: 1px solid #e4e4e4;
            border-radius: 8px;
            padding: 8px 0;
            min-height: 300px;
        }

        .log-item {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 11px 20px;
            border-bottom: 1px solid #f5f5f5;
            transition: background 0.12s;
        }

        .log-item:last-child { border-bottom: none; }
        .log-item:hover { background: #fafbff; }

        .log-bullet {
            width: 8px; height: 8px; border-radius: 50%;
            background: #0f2557; flex-shrink: 0; margin-top: 5px;
        }

        .log-content {}

        .log-action {
            font-size: 12.5px; font-weight: 600; color: #2563eb;
            margin-bottom: 2px; cursor: pointer;
        }
        .log-action:hover { text-decoration: underline; }

        .log-meta { font-size: 11px; color: #888; }

        .log-empty {
            text-align: center; padding: 40px 20px;
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
            <li><a href="{{ url('/admin/dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>Dashboard</a></li>
            <li><a href="{{ url('/admin/account-management') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>Account Management</a></li>
            <li><a href="{{ url('/admin/roles-permissions') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>Roles & Permissions</a></li>
            <li><a href="{{ url('/admin/system-approvals') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>System Approvals</a></li>
            <li><a href="{{ url('/admin/edit-roles') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>Edit Roles</a></li>
            <li class="active"><a href="{{ url('/admin/audit-logs') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>Audit Logs</a></li>
            <li><a href="{{ url('/admin/announcements') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>Announcements</a></li>
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
                <span class="role-badge">Admin/Dean</span>
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

            <div class="page-title">Audit Logs</div>
            <div class="page-sub">Review and approve pending templates and formats</div>

            {{-- Filters --}}
            <div class="filter-row">
                <div class="search-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" id="search-input" placeholder="Search logs..." oninput="applyFilters()">
                </div>
                <select class="filter-select" id="filter-module" onchange="applyFilters()">
                    <option value="">All Modules</option>
                    <option value="Syllabus">Syllabus</option>
                    <option value="Announcement">Announcement</option>
                    <option value="Calendar">Calendar</option>
                    <option value="User">User</option>
                    <option value="Role">Role</option>
                    <option value="Question Bank">Question Bank</option>
                    <option value="Memorandum">Memorandum</option>
                </select>
                <div class="date-wrap">
                    <input type="date" id="filter-date" onchange="applyFilters()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;color:#888;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
            </div>

            {{-- Log list --}}
            <div class="log-panel" id="log-panel">
                <div class="log-empty">No audit logs available.</div>
            </div>

        </div>
    </div>

    <script>
        // Frontend-only audit log data — empty by default
        var logs = [];

        function applyFilters() {
            var search = document.getElementById('search-input').value.trim().toLowerCase();
            var module = document.getElementById('filter-module').value;
            var date   = document.getElementById('filter-date').value;

            var filtered = logs.filter(function(l) {
                var matchSearch = !search || l.action.toLowerCase().includes(search) || l.user.toLowerCase().includes(search);
                var matchModule = !module || l.module === module;
                var matchDate   = !date || l.date === date;
                return matchSearch && matchModule && matchDate;
            });

            renderLogs(filtered);
        }

        function renderLogs(data) {
            var panel = document.getElementById('log-panel');

            if (data.length === 0) {
                panel.innerHTML = '<div class="log-empty">No audit logs available.</div>';
                return;
            }

            panel.innerHTML = '';
            data.forEach(function(l) {
                var item = document.createElement('div');
                item.className = 'log-item';
                item.innerHTML =
                    '<div class="log-bullet"></div>' +
                    '<div class="log-content">' +
                        '<div class="log-action">' + l.action + '</div>' +
                        '<div class="log-meta">' + l.user + ' • ' + l.timeAgo + '</div>' +
                    '</div>';
                panel.appendChild(item);
            });
        }

        function handleLogout() {
            window.location.href = '{{ url("/login") }}';
        }

        // Init
        renderLogs(logs);
    </script>

</body>
</html>