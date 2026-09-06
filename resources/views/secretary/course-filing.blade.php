<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Filing – CBMA System</title>
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
        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 3px; }
        .page-sub { font-size: 11.5px; color: #888; margin-bottom: 20px; }

        .filters-row { display: flex; gap: 10px; margin-bottom: 18px; }
        .filter-select { height: 36px; padding: 0 32px 0 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 12.5px; color: #333; background: #fff; outline: none; appearance: none; -webkit-appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%23666' d='M5 7L0 2h10z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; cursor: pointer; min-width: 150px; }
        .filter-select:focus { border-color: #0f2557; }

        .filing-panel { background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; overflow: hidden; }
        .filing-table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
        .filing-table thead tr { background: #fafafa; border-bottom: 1px solid #eee; }
        .filing-table th { padding: 12px 16px; text-align: left; font-size: 11.5px; font-weight: 700; color: #666; }
        .filing-table th.center { text-align: center; }
        .filing-table td { padding: 13px 16px; border-bottom: 1px solid #f5f5f5; color: #333; vertical-align: middle; }
        .filing-table td.center { text-align: center; }
        .filing-table tbody tr:last-child td { border-bottom: none; }
        .filing-table tbody tr:hover { background: #fafbff; }
        .filing-table td.empty-row { text-align: center; color: #999; padding: 40px 16px; }

        .course-code { font-weight: 700; color: #0f2557; }
        .program-tag { display: inline-block; font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 10px; background: #f0f4ff; color: #0f2557; }

        .status-icon { display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; border-radius: 50%; }
        .status-yes { background: #d1fae5; color: #059669; }
        .status-no { background: #fef3c7; color: #d97706; }
        .status-icon svg { width: 14px; height: 14px; }

        .material-count { display: inline-block; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 12px; background: #ede9fe; color: #6d28d9; }
        .material-count.zero { background: #f3f4f6; color: #9ca3af; }

        .completion-bar { display: flex; align-items: center; gap: 8px; }
        .completion-track { width: 60px; height: 6px; background: #eee; border-radius: 3px; overflow: hidden; }
        .completion-fill { height: 100%; background: #059669; border-radius: 3px; }
        .completion-text { font-size: 10.5px; color: #888; font-weight: 600; }

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
            <li class="{{ request()->is('secretary/dashboard') ? 'active' : '' }}"><a href="{{ url('/secretary/dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>Dashboard</a></li>
            @endif
            @if($navPermissions['document-repository'] ?? true)
            <li class="{{ request()->is('secretary/document-repository*') ? 'active' : '' }}"><a href="{{ url('/secretary/document-repository') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>Document Repository</a></li>
            @endif
            @if($navPermissions['template-distribution'] ?? true)
            <li class="{{ request()->is('secretary/template-distribution*') ? 'active' : '' }}"><a href="{{ url('/secretary/template-distribution') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>Template Distribution</a></li>
            @endif
            @if($navPermissions['course-filing'] ?? true)
            <li class="{{ request()->is('secretary/course-filing*') ? 'active' : '' }}"><a href="{{ url('/secretary/course-filing') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>Course Filing</a></li>
            @endif
            @if($navPermissions['course-assignment'] ?? true)
            <li class="{{ request()->is('secretary/course-assignment*') ? 'active' : '' }}"><a href="{{ url('/secretary/course-assignment') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>Course Assignment</a></li>
            @endif
            @if($navPermissions['account-management'] ?? true)
            <li class="{{ request()->is('secretary/account-management*') ? 'active' : '' }}"><a href="{{ url('/secretary/account-management') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>Account Management</a></li>
            @endif
            @if($navPermissions['announcements'] ?? true)
            <li class="{{ request()->is('secretary/announcements*') ? 'active' : '' }}"><a href="{{ url('/secretary/announcements') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>Announcements</a></li>
            @endif
            @if($navPermissions['calendar'] ?? true)
            <li class="{{ request()->is('secretary/calendar*') ? 'active' : '' }}"><a href="{{ url('/secretary/calendar') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Calendar of Activities</a></li>
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
                <span class="role-badge">Secretary</span>
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
            <div class="page-title">Course Filing</div>
            <div class="page-sub">Monitor kung kompleto na ang mga dokumento (Syllabus, TOS, Exam Bank, Materials) ng bawat course</div>

            <div class="filters-row">
                <form method="GET">
                    <select name="program" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Programs</option>
                        <option value="BSA" {{ $program == 'BSA' ? 'selected' : '' }}>BSA</option>
                        <option value="BSMA" {{ $program == 'BSMA' ? 'selected' : '' }}>BSMA</option>
                        <option value="BSOA" {{ $program == 'BSOA' ? 'selected' : '' }}>BSOA</option>
                    </select>
                </form>
            </div>

            <div class="filing-panel">
                <table class="filing-table">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Program</th>
                            <th class="center">Syllabus</th>
                            <th class="center">TOS</th>
                            <th class="center">Exam Bank</th>
                            <th class="center">Materials</th>
                            <th class="center">Completion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($filing as $row)
                            @php
                                $done = ($row['has_syllabus'] ? 1 : 0) + ($row['has_tos'] ? 1 : 0) + ($row['has_exam_bank'] ? 1 : 0) + ($row['materials'] > 0 ? 1 : 0);
                                $pct = round(($done / 4) * 100);
                            @endphp
                            <tr>
                                <td><span class="course-code">{{ $row['code'] }}</span></td>
                                <td>{{ $row['title'] }}</td>
                                <td><span class="program-tag">{{ $row['program'] }}</span></td>
                                <td class="center">
                                    @if($row['has_syllabus'])
                                        <span class="status-icon status-yes"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
                                    @else
                                        <span class="status-icon status-no"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></span>
                                    @endif
                                </td>
                                <td class="center">
                                    @if($row['has_tos'])
                                        <span class="status-icon status-yes"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
                                    @else
                                        <span class="status-icon status-no"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></span>
                                    @endif
                                </td>
                                <td class="center">
                                    @if($row['has_exam_bank'])
                                        <span class="status-icon status-yes"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
                                    @else
                                        <span class="status-icon status-no"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></span>
                                    @endif
                                </td>
                                <td class="center">
                                    <span class="material-count {{ $row['materials'] == 0 ? 'zero' : '' }}">{{ $row['materials'] }}</span>
                                </td>
                                <td class="center">
                                    <div class="completion-bar" style="justify-content:center;">
                                        <div class="completion-track"><div class="completion-fill" style="width: {{ $pct }}%;"></div></div>
                                        <span class="completion-text">{{ $pct }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="empty-row">Walang courses na nakita.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>