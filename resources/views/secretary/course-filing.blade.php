<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Filing – CBMA System</title>
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
        .page-sub { font-size: 11.5px; color: #888; margin-bottom: 20px; }

        /* ── Course Filing table ── */
        .filing-panel {
            background: #fff;
            border: 1px solid #e4e4e4;
            border-radius: 8px;
            overflow: hidden;
        }

        .filing-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
        }

        .filing-table thead tr { background: #fafafa; border-bottom: 1px solid #e8e8e8; }
        .filing-table th { padding: 12px 20px; text-align: left; font-size: 12px; font-weight: 700; color: #555; }
        .filing-table th:not(:nth-child(1)):not(:nth-child(2)) { text-align: center; }
        .filing-table td { padding: 13px 20px; border-bottom: 1px solid #f5f5f5; color: #333; vertical-align: middle; }
        .filing-table td:not(:nth-child(1)):not(:nth-child(2)) { text-align: center; }
        .filing-table tbody tr:last-child td { border-bottom: none; }
        .filing-table tbody tr:hover { background: #fafbff; }

        /* Status badges */
        .badge-present {
            display: inline-flex; align-items: center; gap: 3px;
            background: #d1fae5; color: #065f46;
            font-size: 10.5px; font-weight: 700;
            padding: 3px 10px; border-radius: 12px;
        }

        .badge-missing {
            display: inline-flex; align-items: center; gap: 3px;
            background: #fee2e2; color: #991b1b;
            font-size: 10.5px; font-weight: 700;
            padding: 3px 10px; border-radius: 12px;
        }

        .table-empty { text-align: center; padding: 40px; color: #bbb; font-size: 12.5px; }

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
            <li><a href="{{ url('/secretary/dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>Dashboard</a></li>
            <li><a href="{{ url('/secretary/document-repository') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>Document Repository</a></li>
            <li><a href="{{ url('/secretary/template-distribution') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>Template Distribution</a></li>
            <li class="active"><a href="{{ url('/secretary/course-filing') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>Course Filing</a></li>
            <li><a href="{{ url('/secretary/account-management') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>Account Management</a></li>
            <li><a href="{{ url('/secretary/announcements') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>Announcements</a></li>
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
                <span class="role-badge">Secretary</span>
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

            <div class="page-title">Course Filing</div>
            <div class="page-sub">Manage course master folder documents</div>

            <div class="filing-panel">
                <table class="filing-table">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Syllabus</th>
                            <th>TOS</th>
                            <th>Exam Bank</th>
                            <th>Materials</th>
                        </tr>
                    </thead>
                    <tbody id="filing-tbody">
                        <tr><td colspan="6" class="table-empty">No course filing records found.</td></tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script>
        // Frontend-only course data — empty by default
        var courses = [];

        function badge(hasFile, version) {
            if (hasFile) {
                return '<span class="badge-present">✓ ' + (version || 'v1.0') + '</span>';
            }
            return '<span class="badge-missing">✕ Missing</span>';
        }

        function renderTable() {
            var tbody = document.getElementById('filing-tbody');

            if (courses.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="table-empty">No course filing records found.</td></tr>';
                return;
            }

            tbody.innerHTML = '';
            courses.forEach(function(c) {
                var tr = document.createElement('tr');
                tr.innerHTML =
                    '<td>' + c.code + '</td>' +
                    '<td>' + c.name + '</td>' +
                    '<td>' + badge(c.syllabus, c.syllabusVer) + '</td>' +
                    '<td>' + badge(c.tos,      c.tosVer)      + '</td>' +
                    '<td>' + badge(c.examBank, c.examVer)     + '</td>' +
                    '<td>' + badge(c.materials,c.matVer)      + '</td>';
                tbody.appendChild(tr);
            });
        }

        function handleLogout() {
            window.location.href = '{{ url("/login") }}';
        }

        renderTable();
    </script>

</body>
</html>