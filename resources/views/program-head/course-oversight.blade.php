<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Oversight – CBMA System</title>
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

        /* ── Stat cards row ── */
        .stat-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: #fff;
            border: 1px solid #e8e8e8;
            border-radius: 8px;
            padding: 14px 16px;
        }

        .stat-label { font-size: 11px; color: #666; margin-bottom: 8px; }

        .stat-value {
            font-size: 22px; font-weight: 700;
        }

        .stat-value.blue   { color: #2563eb; }
        .stat-value.teal   { color: #0891b2; }
        .stat-value.orange { color: #f59e0b; }
        .stat-value.dark   { color: #1a1a2e; }

        /* ── Course table ── */
        .course-panel {
            background: #fff;
            border: 1px solid #e4e4e4;
            border-radius: 8px;
            overflow: hidden;
        }

        .course-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
        }

        .course-table thead tr {
            background: #fafafa;
            border-bottom: 1px solid #e8e8e8;
        }

        .course-table th {
            padding: 11px 16px;
            text-align: left;
            font-size: 11.5px; font-weight: 700; color: #555;
        }

        .course-table th:not(:nth-child(1)):not(:nth-child(2)):not(:nth-child(3)) {
            text-align: center;
        }

        .course-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #f5f5f5;
            color: #333; vertical-align: middle;
        }

        .course-table tbody tr:last-child td { border-bottom: none; }
        .course-table tbody tr:hover { background: #fafbff; }

        .course-table td:not(:nth-child(1)):not(:nth-child(2)):not(:nth-child(3)) {
            text-align: center;
        }

        .check { color: #555; font-size: 14px; }
        .cross { color: #ef4444; font-size: 14px; }

        .table-empty { text-align: center; padding: 36px; color: #bbb; font-size: 12.5px; }

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
            <li><a href="{{ url('/program-head/dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>Dashboard</a></li>
            <li><a href="{{ url('/program-head/template-review') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>Template Review</a></li>
            <li class="active"><a href="{{ url('/program-head/course-oversight') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>Course Oversight</a></li>
            <li><a href="{{ url('/program-head/account-management') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>Account Management</a></li>
            <li><a href="{{ url('/program-head/announcements') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>Announcements</a></li>
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

            <div class="page-title">Course Oversight</div>
            <div class="page-sub">Monitor master folder completeness for supervised courses</div>

            {{-- Stat cards --}}
            <div class="stat-cards">
                <div class="stat-card">
                    <div class="stat-label">Total Courses</div>
                    <div class="stat-value dark">—</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Fully Complete</div>
                    <div class="stat-value blue">—</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">In Progress</div>
                    <div class="stat-value teal">—</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Avg Completeness</div>
                    <div class="stat-value orange">—</div>
                </div>
            </div>

            {{-- Course table --}}
            <div class="course-panel">
                <table class="course-table">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Instructor</th>
                            <th>Syllabus</th>
                            <th>TOS</th>
                            <th>Exam Bank</th>
                            <th>Materials</th>
                        </tr>
                    </thead>
                    <tbody id="course-tbody">
                        <tr>
                            <td colspan="7" class="table-empty">No courses found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script>
        // Frontend-only course data — empty by default
        var courses = [];

        function renderTable() {
            var tbody = document.getElementById('course-tbody');

            if (courses.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="table-empty">No courses found.</td></tr>';
                return;
            }

            tbody.innerHTML = '';
            courses.forEach(function(c) {
                var tr = document.createElement('tr');
                tr.innerHTML =
                    '<td>' + c.code + '</td>' +
                    '<td>' + c.name + '</td>' +
                    '<td>' + c.instructor + '</td>' +
                    '<td>' + (c.syllabus  ? '<span class="check">✓</span>' : '<span class="cross">✕</span>') + '</td>' +
                    '<td>' + (c.tos       ? '<span class="check">✓</span>' : '<span class="cross">✕</span>') + '</td>' +
                    '<td>' + (c.examBank  ? '<span class="check">✓</span>' : '<span class="cross">✕</span>') + '</td>' +
                    '<td>' + (c.materials ? '<span class="check">✓</span>' : '<span class="cross">✕</span>') + '</td>';
                tbody.appendChild(tr);
            });

            // Update stat cards
            var total    = courses.length;
            var full     = courses.filter(function(c) { return c.syllabus && c.tos && c.examBank && c.materials; }).length;
            var progress = total - full;
            var pct      = total ? Math.round((full / total) * 100) + '%' : '—';

            document.querySelectorAll('.stat-value')[0].textContent = total;
            document.querySelectorAll('.stat-value')[1].textContent = full;
            document.querySelectorAll('.stat-value')[2].textContent = progress;
            document.querySelectorAll('.stat-value')[3].textContent = pct;
        }

        function handleLogout() {
            window.location.href = '{{ url("/login") }}';
        }

        renderTable();
    </script>

</body>
</html>