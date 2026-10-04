<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics – CBMA System</title>
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

        .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 20px; }
        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 3px; }
        .page-sub { font-size: 11.5px; color: #888; }

        .filter-select { height: 36px; padding: 0 32px 0 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 12.5px; color: #333; background: #fff; outline: none; appearance: none; -webkit-appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%23666' d='M5 7L0 2h10z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; cursor: pointer; min-width: 220px; }
        .filter-select:focus { border-color: #0f2557; }

        .stat-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 18px; }
        .stat-card { background: #fff; border: 1px solid #e8e8e8; border-radius: 8px; padding: 16px 20px; }
        .stat-card-header { display: flex; align-items: center; gap: 7px; font-size: 12px; font-weight: 600; color: #c9963a; margin-bottom: 10px; }
        .stat-card-header svg { width: 15px; height: 15px; color: #c9963a; }
        .stat-value { font-size: 26px; font-weight: 700; color: #1a1a2e; }

        .chart-title { font-size: 13px; font-weight: 700; color: #1a1a2e; margin-bottom: 14px; }
        .chart-wrap { width: 100%; }
        canvas { display: block; width: 100% !important; }
        .chart-panel-full { background: #fff; border: 1px solid #e8e8e8; border-radius: 8px; padding: 16px 18px; margin-bottom: 16px; }

        .report-panel { background: #fff; border: 1px solid #e8e8e8; border-radius: 8px; padding: 16px 18px; margin-bottom: 16px; }
        .report-title { font-size: 13px; font-weight: 700; color: #1a1a2e; margin-bottom: 2px; }
        .report-sub { font-size: 11px; color: #888; margin-bottom: 14px; }
        .report-table { width: 100%; border-collapse: collapse; font-size: 12px; }
        .report-table thead tr { border-bottom: 1px solid #eee; }
        .report-table th { padding: 8px 10px; text-align: left; font-size: 10.5px; font-weight: 700; color: #666; text-transform: uppercase; }
        .report-table td { padding: 9px 10px; border-bottom: 1px solid #f5f5f5; color: #333; }
        .report-table tbody tr:last-child td { border-bottom: none; }
        .report-table .empty-row { text-align: center; color: #999; padding: 20px 10px; }
        .pill { display: inline-block; font-size: 10.5px; font-weight: 600; padding: 2px 9px; border-radius: 10px; }
        .pill-ok { background: #dcfce7; color: #166534; }
        .pill-warn { background: #fef3c7; color: #92400e; }
        .pill-bad { background: #fee2e2; color: #991b1b; }
        .overdue-tag { font-size: 10px; font-weight: 700; color: #991b1b; }

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
            <a href="#" onclick="openLogoutModal()">
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

            <div class="page-header">
                <div>
                    <div class="page-title">Analytics</div>
                    <div class="page-sub">Institution-wide instructional reports — {{ $activity['school_year'] }}, {{ $activity['semester'] }}</div>
                </div>
                <form method="GET" style="display:flex;gap:10px;">
                    <select name="program" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Programs</option>
                        @foreach($programs as $code)
                            <option value="{{ $code }}" {{ $selectedProgram == $code ? 'selected' : '' }}>{{ $code }} — {{ \App\Support\Programs::label($code) }}</option>
                        @endforeach
                    </select>
                    <select name="school_year" class="filter-select" style="min-width:140px;" onchange="this.form.submit()">
                        @foreach($schoolYears as $sy)
                            <option value="{{ $sy }}" {{ $schoolYear == $sy ? 'selected' : '' }}>{{ $sy }}</option>
                        @endforeach
                    </select>
                    <select name="semester" class="filter-select" style="min-width:160px;" onchange="this.form.submit()">
                        @foreach($semesters as $s)
                            <option value="{{ $s }}" {{ $semester == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="stat-cards">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                        Compliance Rate
                    </div>
                    <div class="stat-value">{{ $compliance['compliance_rate'] }}%</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        On-Time Rate
                    </div>
                    <div class="stat-value">{{ $compliance['on_time_rate'] }}%</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                        Exams Analyzed
                    </div>
                    <div class="stat-value">{{ $coverage['exams_analyzed'] }}</div>
                </div>
            </div>

            {{-- 1. Instructional Compliance Report --}}
            <div class="report-panel">
                <div class="report-title">Instructional Compliance Report</div>
                <div class="report-sub">Faculty submission status against active Submissions &amp; Deadline requirements, by type (all-time — not affected by the term filter above)</div>
                <table class="report-table">
                    <thead><tr><th>Requirement Type</th><th>Expected</th><th>Submitted</th><th>On Time</th></tr></thead>
                    <tbody>
                        @forelse($compliance['by_type'] as $type => $row)
                            <tr>
                                <td>{{ ucwords(str_replace('_', ' ', $type)) }}</td>
                                <td>{{ $row['expected'] }}</td>
                                <td>{{ $row['submitted'] }}</td>
                                <td>{{ $row['on_time'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="empty-row">No submission requirements posted yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="report-panel">
                <div class="report-title">Outstanding Submissions</div>
                <div class="report-sub">Faculty with no submission yet for a posted requirement</div>
                <table class="report-table">
                    <thead><tr><th>Faculty</th><th>Requirement</th><th>Type</th><th>Deadline</th></tr></thead>
                    <tbody>
                        @forelse($compliance['outstanding'] as $row)
                            <tr>
                                <td>{{ $row['faculty'] }}</td>
                                <td>{{ $row['requirement'] }}</td>
                                <td>{{ ucwords(str_replace('_', ' ', $row['type'])) }}</td>
                                <td>{{ $row['deadline'] }} @if($row['is_overdue'])<span class="overdue-tag">&nbsp;OVERDUE</span>@endif</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="empty-row">All requirements are fully submitted.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 2. Assessment Coverage Report --}}
            <div class="chart-panel-full">
                <div class="chart-title">Assessment Coverage Report — Bloom's Taxonomy (Actual vs. TOS Target)</div>
                <div class="report-sub">Item counts from finalized exams vs. each course's Table of Specifications target, {{ $coverage['exams_analyzed'] }} exam(s) analyzed (all-time — not affected by the term filter above)</div>
                <div class="chart-wrap"><canvas id="bloomChart" height="100"></canvas></div>
            </div>

            <div class="report-panel">
                <div class="report-title">Coverage by Course</div>
                <table class="report-table">
                    <thead><tr><th>Course</th><th>Actual Items</th><th>TOS Target Items</th></tr></thead>
                    <tbody>
                        @forelse($coverage['per_course'] as $course => $row)
                            <tr>
                                <td>{{ $course }}</td>
                                <td>{{ $row['actual'] }}</td>
                                <td>{{ $row['target'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="empty-row">No finalized exams yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 3. Faculty Activity Summary --}}
            <div class="report-panel">
                <div class="report-title">Faculty Activity Summary</div>
                <div class="report-sub">Recorded activity per faculty — {{ $activity['school_year'] }}, {{ $activity['semester'] }}</div>
                <table class="report-table">
                    <thead><tr><th>Faculty</th><th>Exams Created</th><th>Exams Finalized</th><th>Topics Filed</th><th>Content Modules</th><th>Shared Resources</th><th>Submissions Filed</th></tr></thead>
                    <tbody>
                        @forelse($activity['faculty'] as $row)
                            <tr>
                                <td>{{ $row['faculty'] }}</td>
                                <td>{{ $row['exams_created'] }}</td>
                                <td>{{ $row['exams_finalized'] }}</td>
                                <td>{{ $row['topics_filed'] }}</td>
                                <td>{{ $row['content_modules'] }}</td>
                                <td>{{ $row['shared_resources'] }}</td>
                                <td>{{ $row['submissions_filed'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="empty-row">No faculty found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 4. Course Coordination Status Report --}}
            <div class="report-panel">
                <div class="report-title">Course Coordination Status Report</div>
                <div class="report-sub">Multi-section courses — exam finalization alignment across co-faculty — {{ $coordination['school_year'] }}, {{ $coordination['semester'] }}</div>
                <table class="report-table">
                    <thead><tr><th>Course</th><th>Faculty</th><th>Prelim</th><th>Midterm</th><th>Final</th></tr></thead>
                    <tbody>
                        @forelse($coordination['courses'] as $row)
                            <tr>
                                <td>{{ $row['course'] }}</td>
                                <td>{{ implode(', ', $row['faculty']) }}</td>
                                @foreach(['Prelim','Midterm','Final'] as $period)
                                    @php($p = $row['periods'][$period])
                                    <td>
                                        <span class="pill {{ $p['finalized'] == $p['of'] ? 'pill-ok' : ($p['finalized'] == 0 ? 'pill-bad' : 'pill-warn') }}">
                                            {{ $p['finalized'] }} of {{ $p['of'] }}
                                        </span>
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr><td colspan="5" class="empty-row">No multi-section courses this term.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        var GOLD = '#c9963a';
        var NAVY = '#0f2557';

        var bloomLabels = @json(array_keys($coverage['by_level']));
        var bloomActual = @json(array_values(array_map(fn($r) => $r['actual'], $coverage['by_level'])));
        var bloomTarget = @json(array_values(array_map(fn($r) => $r['target'], $coverage['by_level'])));

        var bloomCtx = document.getElementById('bloomChart').getContext('2d');
        new Chart(bloomCtx, {
            type: 'bar',
            data: {
                labels: bloomLabels,
                datasets: [
                    { label: 'Actual', data: bloomActual, backgroundColor: NAVY, borderRadius: 3, barPercentage: 0.7 },
                    { label: 'TOS Target', data: bloomTarget, backgroundColor: GOLD, borderRadius: 3, barPercentage: 0.7 }
                ]
            },
            options: {
                plugins: { legend: { display: true, position: 'top', labels: { font: { size: 11 } } } },
                scales: {
                    y: { beginAtZero: true, ticks: { font: { size: 10 } }, grid: { color: '#f0f0f0' } },
                    x: { ticks: { font: { size: 11 } }, grid: { display: false } }
                }
            }
        });
    </script>


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

</body>
</html>
