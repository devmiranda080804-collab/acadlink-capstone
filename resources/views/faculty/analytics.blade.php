<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics – CBMA System</title>
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
            width: 72px; height: 72px;
            border-radius: 50%;
            object-fit: contain;
            background: #1b3d7a;
        }

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

        .nav-list li.active a {
            background-color: rgba(255,255,255,0.08);
            color: #fff;
            border-left: 3px solid #fff;
        }

        .nav-list li a svg { width: 18px; height: 18px; flex-shrink: 0; opacity: 0.85; }

        .sidebar-logout { padding: 12px 0; border-top: 1px solid rgba(255,255,255,0.1); }

        .sidebar-logout a {
            display: flex; align-items: center; gap: 11px;
            padding: 11px 20px;
            color: #c8d6ec; text-decoration: none; font-size: 13px;
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

        .role-badge {
            background-color: #0f2557; color: #fff;
            font-size: 12px; font-weight: 600;
            padding: 5px 16px; border-radius: 20px;
        }

        .user-info { display: flex; align-items: center; gap: 8px; }
        .user-text { text-align: right; }
        .user-name { font-size: 12px; font-weight: 600; color: #222; }
        .user-email { font-size: 10px; color: #888; }

        .user-avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background-color: #0f2557;
            color: #fff; font-size: 12px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
        }

        /* ═══════════════════ CONTENT ═══════════════════ */
        .content { flex: 1; overflow-y: auto; padding: 24px 28px 28px; }

        /* Page header */
        .page-header {
            display: flex; align-items: flex-start; justify-content: space-between;
            margin-bottom: 20px;
        }

        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 3px; }
        .page-sub { font-size: 11.5px; color: #888; }

        .btn-export {
            display: flex; align-items: center; gap: 6px;
            background: #0f2557; color: #fff;
            border: none; border-radius: 6px;
            font-size: 12.5px; font-weight: 600;
            padding: 9px 18px; cursor: pointer;
            transition: background 0.15s;
        }

        .btn-export:hover { background: #1a3a7a; }

        /* Semester box */
        .semester-box {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 14px 18px;
            margin-bottom: 18px;
            display: inline-block;
            width: 100%;
        }

        .semester-label { font-size: 11.5px; font-weight: 600; color: #444; margin-bottom: 8px; }

        .semester-select {
            height: 34px;
            padding: 0 32px 0 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 12.5px;
            color: #333;
            background: #fff;
            outline: none;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%23666' d='M5 7L0 2h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            cursor: pointer;
            min-width: 220px;
        }

        /* ── Stat cards ── */
        .stat-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-bottom: 18px;
        }

        .stat-card {
            background: #fff;
            border: 1px solid #e8e8e8;
            border-radius: 8px;
            padding: 16px 20px;
        }

        .stat-card-header {
            display: flex; align-items: center; gap: 7px;
            font-size: 12px; font-weight: 600;
            color: #c9963a;
            margin-bottom: 10px;
        }

        .stat-card-header svg { width: 15px; height: 15px; color: #c9963a; }

        .stat-value {
            font-size: 26px; font-weight: 700;
            color: #1a1a2e;
        }

        /* ── Charts row ── */
        .charts-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        .chart-panel {
            background: #fff;
            border: 1px solid #e8e8e8;
            border-radius: 8px;
            padding: 16px 18px;
        }

        .chart-title {
            font-size: 13px; font-weight: 700;
            color: #1a1a2e; margin-bottom: 14px;
        }

        .chart-wrap { width: 100%; }

        canvas { display: block; width: 100% !important; }

        /* Full-width chart */
        .chart-panel-full {
            background: #fff;
            border: 1px solid #e8e8e8;
            border-radius: 8px;
            padding: 16px 18px;
            margin-bottom: 16px;
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
            <li>
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
            <li class="active">
                <a href="{{ url('/faculty/analytics') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                    Analytics
                </a>
            </li>
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

            {{-- Page header --}}
            <div class="page-header">
                <div>
                    <div class="page-title">Analytics</div>
                    <div class="page-sub">Course performance and outcome analysis</div>
                </div>
                <button class="btn-export" type="button">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><circle cx="12" cy="12" r="10"/><polyline points="8 12 12 16 16 12"/><line x1="12" y1="8" x2="12" y2="16"/></svg>
                    Export Report
                </button>
            </div>

            {{-- Semester selector --}}
            <div class="semester-box">
                <div class="semester-label">Semester</div>
                <select class="semester-select" onchange="updateCharts()">
                    <option value="2025-2026-2" selected>2025-2026 &nbsp; Second Semester</option>
                    <option value="2025-2026-1">2025-2026 &nbsp; First Semester</option>
                    <option value="2024-2025-2">2024-2025 &nbsp; Second Semester</option>
                    <option value="2024-2025-1">2024-2025 &nbsp; First Semester</option>
                </select>
            </div>

            {{-- Stat cards --}}
            <div class="stat-cards">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                        Average Grade
                    </div>
                    <div class="stat-value" id="avg-grade">—</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                        CO Attainment
                    </div>
                    <div class="stat-value" id="co-attainment">—</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                        Pass Rate
                    </div>
                    <div class="stat-value" id="pass-rate">—</div>
                </div>
            </div>

            {{-- Grade Distribution + Course Outcome --}}
            <div class="charts-row">
                <div class="chart-panel">
                    <div class="chart-title">Grade Distribution</div>
                    <div class="chart-wrap"><canvas id="gradeChart" height="160"></canvas></div>
                </div>
                <div class="chart-panel">
                    <div class="chart-title">Course Outcome</div>
                    <div class="chart-wrap"><canvas id="outcomeChart" height="160"></canvas></div>
                </div>
            </div>

            {{-- Bloom's Taxonomy Coverage --}}
            <div class="chart-panel-full">
                <div class="chart-title">Bloom's Taxonomy Coverage</div>
                <div class="chart-wrap"><canvas id="bloomChart" height="100"></canvas></div>
            </div>

        </div>
    </div>

    {{-- Chart.js CDN --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>

    <script>
        var GOLD  = '#c9963a';
        var NAVY  = '#0f2557';
        var LIGHT = '#e8e8e8';

        // ── Chart instances ──
        var gradeChart, outcomeChart, bloomChart;

        function buildCharts() {
            // Grade Distribution (navy bars — A,B,C,D,E)
            var gradeCtx = document.getElementById('gradeChart').getContext('2d');
            gradeChart = new Chart(gradeCtx, {
                type: 'bar',
                data: {
                    labels: ['A', 'B', 'C', 'D', 'E'],
                    datasets: [{
                        data: [13, 18, 9, 5, 1],
                        backgroundColor: NAVY,
                        borderRadius: 3,
                        barPercentage: 0.55
                    }]
                },
                options: {
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, max: 20, ticks: { stepSize: 5, font: { size: 10 } }, grid: { color: '#f0f0f0' } },
                        x: { ticks: { font: { size: 11 } }, grid: { display: false } }
                    }
                }
            });

            // Course Outcome (gold bars — CAE1-4)
            var outcomeCtx = document.getElementById('outcomeChart').getContext('2d');
            outcomeChart = new Chart(outcomeCtx, {
                type: 'bar',
                data: {
                    labels: ['CAE1', 'CAE2', 'CAE3', 'CAE4'],
                    datasets: [{
                        data: [82, 75, 60, 78],
                        backgroundColor: GOLD,
                        borderRadius: 3,
                        barPercentage: 0.55
                    }]
                },
                options: {
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, max: 100, ticks: { stepSize: 25, font: { size: 10 } }, grid: { color: '#f0f0f0' } },
                        x: { ticks: { font: { size: 11 } }, grid: { display: false } }
                    }
                }
            });

            // Bloom's Taxonomy (navy bars)
            var bloomCtx = document.getElementById('bloomChart').getContext('2d');
            bloomChart = new Chart(bloomCtx, {
                type: 'bar',
                data: {
                    labels: ['Remember', 'Understand', 'Apply', 'Analyze', 'Evaluate', 'Create'],
                    datasets: [{
                        data: [16, 11, 19, 6, 4, 5],
                        backgroundColor: NAVY,
                        borderRadius: 3,
                        barPercentage: 0.55
                    }]
                },
                options: {
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, max: 20, ticks: { stepSize: 5, font: { size: 10 } }, grid: { color: '#f0f0f0' } },
                        x: { ticks: { font: { size: 11 } }, grid: { display: false } }
                    }
                }
            });
        }

        // ── Update stat values and charts on semester change ──
        function updateCharts() {
            // Frontend prototype — values stay the same regardless of selection
            document.getElementById('avg-grade').textContent    = '—';
            document.getElementById('co-attainment').textContent = '—';
            document.getElementById('pass-rate').textContent    = '—';
        }

        // ── Logout ──
        function handleLogout() {
            window.location.href = '{{ url("/login") }}';
        }

        // ── Init ──
        buildCharts();
    </script>

</body>
</html>