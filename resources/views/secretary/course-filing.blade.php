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

        /* Summary */
        .summary-row { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; margin-bottom: 18px; }
        .summary-card { background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; padding: 14px 18px; display: flex; align-items: center; gap: 14px; }
        .summary-icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .summary-icon svg { width: 18px; height: 18px; }
        .summary-num { font-size: 20px; font-weight: 700; color: #1a1a2e; line-height: 1.1; }
        .summary-label { font-size: 11.5px; color: #777; margin-top: 2px; }

        /* The two filing columns */
        .filing-table th.col-split { background: #f3f6fd; color: #0f2557; }
        .filing-table td.col-split { background: #fbfcff; }
        .filing-table tbody tr:hover td.col-split { background: #f3f6fd; }
        .split-count { font-size: 12.5px; font-weight: 700; color: #1a1a2e; }
        .split-count span { font-weight: 400; color: #888; }
        .doc-dots { display: flex; gap: 4px; justify-content: center; margin-top: 5px; }
        .doc-dot { width: 9px; height: 9px; border-radius: 50%; background: #e5e7eb; }
        .doc-dot.filed { background: #93c5fd; }
        .doc-dot.validated { background: #10b981; }
        .doc-dot.returned { background: #ef4444; }
        .doc-dot.pending { background: #f59e0b; }
        .mini-badges { display: flex; gap: 4px; justify-content: center; flex-wrap: wrap; margin-top: 5px; }
        .mini-badge { font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 10px; white-space: nowrap; }
        .mini-badge.check { background: #fef3c7; color: #b45309; }
        .mini-badge.returned { background: #fee2e2; color: #b91c1c; }
        .btn-review { display: inline-flex; align-items: center; gap: 6px; background: #0f2557; color: #fff; border: none; font-size: 11.5px; font-weight: 600; padding: 7px 13px; border-radius: 6px; cursor: pointer; white-space: nowrap; position: relative; }
        .btn-review:hover { background: #1b3d7a; }
        .btn-review svg { width: 13px; height: 13px; }
        .btn-review .dot { position: absolute; top: -4px; right: -4px; min-width: 16px; height: 16px; padding: 0 4px; border-radius: 999px; background: #f59e0b; color: #fff; font-size: 10px; display: flex; align-items: center; justify-content: center; }

        /* Review modal */
        .cf-overlay { display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); z-index: 900; align-items: center; justify-content: center; padding: 20px; }
        .cf-overlay.open { display: flex; }
        .cf-modal { background: #fff; border-radius: 12px; width: 980px; max-width: 100%; max-height: 92vh; display: flex; flex-direction: column; box-shadow: 0 20px 50px rgba(0,0,0,0.25); overflow: hidden; }
        .cf-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; padding: 18px 22px; border-bottom: 1px solid #eee; }
        .cf-title { font-size: 16px; font-weight: 700; color: #1a1a2e; }
        .cf-sub { font-size: 12px; color: #777; margin-top: 3px; }
        .cf-close { background: none; border: none; font-size: 22px; line-height: 1; color: #999; cursor: pointer; padding: 2px 6px; border-radius: 6px; }
        .cf-close:hover { background: #f3f4f6; color: #333; }
        .cf-body { overflow-y: auto; padding: 4px 22px 18px; }
        .cf-table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
        .cf-table th { text-align: left; padding: 12px 12px 10px; font-size: 11px; font-weight: 700; color: #666; text-transform: uppercase; letter-spacing: .4px; border-bottom: 1px solid #eee; position: sticky; top: 0; background: #fff; }
        .cf-table th.col-split { color: #0f2557; }
        .cf-table td { padding: 12px; border-bottom: 1px solid #f3f3f3; vertical-align: top; }
        .cf-table tr:last-child td { border-bottom: none; }
        .cf-doc { font-weight: 700; color: #1a1a2e; white-space: nowrap; }
        .cf-file + .cf-file, .cf-check + .cf-check { margin-top: 10px; padding-top: 10px; border-top: 1px dashed #eee; }
        .cf-file, .cf-check { min-height: 46px; }
        .cf-file-name { font-weight: 600; color: #1a1a2e; word-break: break-word; }
        .cf-file-meta { font-size: 11px; color: #888; margin-top: 3px; }
        .cf-file-meta a { color: #1f4fbf; font-weight: 600; text-decoration: none; margin-left: 6px; }
        .cf-file-meta a:hover { text-decoration: underline; }
        .cf-none { color: #aaa; font-style: italic; }
        .cf-actions { display: flex; gap: 6px; flex-wrap: wrap; }
        .cf-actions form { margin: 0; }
        .btn-validate { background: #059669; color: #fff; border: none; font-size: 11.5px; font-weight: 600; padding: 7px 12px; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; }
        .btn-validate:hover { background: #047857; }
        .btn-return { background: #fff; color: #b91c1c; border: 1px solid #f3c4c4; font-size: 11.5px; font-weight: 600; padding: 7px 12px; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; }
        .btn-return:hover { background: #fee2e2; border-color: #dc2626; }
        .btn-validate svg, .btn-return svg { width: 12px; height: 12px; }
        .cf-status { display: inline-flex; align-items: center; gap: 5px; font-size: 11.5px; font-weight: 700; padding: 3px 10px; border-radius: 999px; }
        .cf-status svg { width: 12px; height: 12px; }
        .cf-status.validated { background: #d1fae5; color: #047857; }
        .cf-status.returned { background: #fee2e2; color: #b91c1c; }
        .cf-by { font-size: 11px; color: #888; margin-top: 4px; }
        .cf-reason { font-size: 12px; color: #7f1d1d; background: #fef2f2; border-left: 3px solid #ef4444; border-radius: 4px; padding: 6px 9px; margin-top: 6px; white-space: pre-wrap; word-break: break-word; }
        .btn-undo { background: none; border: none; color: #6b7280; font-size: 11px; font-weight: 600; text-decoration: underline; cursor: pointer; padding: 0; margin-left: 6px; }
        .btn-undo:hover { color: #111827; }
        .cf-foot { padding: 12px 22px; border-top: 1px solid #eee; background: #fafafa; font-size: 11.5px; color: #777; display: flex; gap: 14px; flex-wrap: wrap; align-items: center; }
        .cf-foot .legend { display: inline-flex; align-items: center; gap: 5px; }

        .return-modal { background: #fff; border-radius: 12px; width: 440px; max-width: 100%; padding: 22px 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.25); }
        .return-modal h3 { font-size: 15px; color: #1a1a2e; margin-bottom: 6px; }
        .return-modal p { font-size: 12.5px; color: #666; margin-bottom: 12px; line-height: 1.5; }
        .return-modal textarea { width: 100%; min-height: 96px; border: 1px solid #ccc; border-radius: 6px; padding: 9px 10px; font-family: inherit; font-size: 13px; resize: vertical; outline: none; }
        .return-modal textarea:focus { border-color: #0f2557; }
        .return-modal .row { display: flex; justify-content: flex-end; gap: 8px; margin-top: 14px; }
        .btn-cancel { background: #fff; border: 1px solid #ccc; color: #444; font-size: 12.5px; font-weight: 600; padding: 8px 16px; border-radius: 6px; cursor: pointer; }
        .btn-send-return { background: #dc2626; color: #fff; border: none; font-size: 12.5px; font-weight: 600; padding: 8px 16px; border-radius: 6px; cursor: pointer; }
        .btn-send-return:hover { background: #b91c1c; }
        .field-error { color: #b91c1c; font-size: 11.5px; margin-top: 6px; }
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
            @if($navPermissions['account-management'] ?? true)
            <li class="{{ request()->is('secretary/account-management*') ? 'active' : '' }}"><a href="{{ url('/secretary/account-management') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>Account Management</a></li>
            @endif
            @if($navPermissions['announcements'] ?? true)
            <li class="{{ request()->is('secretary/announcements*') ? 'active' : '' }}"><a href="{{ url('/secretary/announcements') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>Announcements
                @if(($unreadAnnouncementsCount ?? 0) > 0)
                    <span class="nav-badge">{{ $unreadAnnouncementsCount }}</span>
                @endif
            </a></li>
            @endif
            @if($navPermissions['calendar'] ?? true)
            <li class="{{ request()->is('secretary/calendar*') ? 'active' : '' }}"><a href="{{ url('/secretary/calendar') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Calendar of Activities</a></li>
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
            <div class="page-sub">Each course's files as filed by the Program Head (Submission), and whether you have checked and validated them (Checked and Validated)</div>

            <div class="summary-row">
                <div class="summary-card">
                    <div class="summary-icon" style="background:#eef2ff;color:#0f2557;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg></div>
                    <div><div class="summary-num">{{ $totals['courses'] }}</div><div class="summary-label">Courses</div></div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon" style="background:#fef3c7;color:#b45309;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
                    <div><div class="summary-num">{{ $totals['to_check'] }}</div><div class="summary-label">Files waiting for your check</div></div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon" style="background:#d1fae5;color:#047857;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
                    <div><div class="summary-num">{{ $totals['complete'] }}</div><div class="summary-label">Courses complete (all validated)</div></div>
                </div>
            </div>

            <div class="filters-row">
                <form method="GET">
                    <select name="program" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Programs</option>
                        @foreach(\App\Support\Programs::options() as $code => $label)
                            <option value="{{ $code }}" {{ $program == $code ? 'selected' : '' }}>{{ $code }} — {{ $label }}</option>
                        @endforeach
                    </select>
                </form>
                <select id="cf-show" class="filter-select" onchange="filterCourses(this.value)">
                    <option value="all">All courses</option>
                    <option value="check">Has files to check</option>
                    <option value="returned">Has returned files</option>
                    <option value="incomplete">Not yet complete</option>
                    <option value="complete">Complete</option>
                </select>
            </div>

            @php
                $docCount = count(\App\Models\CourseMaterial::FILING_DOCUMENTS);
            @endphp
            <div class="filing-panel">
                <table class="filing-table">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Program</th>
                            <th class="center col-split">Submission</th>
                            <th class="center col-split">Checked and Validated</th>
                            <th class="center">OBE Alignment</th>
                            <th class="center">Completion</th>
                            <th class="center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($filing as $row)
                            @php
                                $pct = $row['completion_pct'];
                                $returnedDocs = collect($row['documents'])->where('status', 'returned')->count();
                            @endphp
                            <tr class="cf-row"
                                data-check="{{ $row['to_check'] > 0 ? 1 : 0 }}"
                                data-returned="{{ $returnedDocs > 0 ? 1 : 0 }}"
                                data-complete="{{ $pct == 100 ? 1 : 0 }}">
                                <td><span class="course-code">{{ $row['code'] }}</span></td>
                                <td>{{ $row['title'] }}</td>
                                <td><span class="program-tag" title="{{ \App\Support\Programs::label($row['program']) }}">{{ $row['program'] }}</span></td>
                                <td class="center col-split">
                                    <div class="split-count">{{ $row['submitted'] }} <span>of {{ $docCount }} filed</span></div>
                                    <div class="doc-dots">
                                        @foreach($row['documents'] as $doc)
                                            <span class="doc-dot {{ $doc['status'] === 'missing' ? '' : 'filed' }}" title="{{ $doc['label'] }}: {{ $doc['status'] === 'missing' ? 'not filed yet' : 'filed' }}"></span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="center col-split">
                                    <div class="split-count">{{ $row['validated'] }} <span>of {{ $docCount }} validated</span></div>
                                    <div class="doc-dots">
                                        @foreach($row['documents'] as $doc)
                                            <span class="doc-dot {{ $doc['status'] === 'missing' ? '' : $doc['status'] }}" title="{{ $doc['label'] }}: {{ ['missing' => 'not filed yet', 'pending' => 'waiting for your check', 'validated' => 'checked and validated', 'returned' => 'returned'][$doc['status']] }}"></span>
                                        @endforeach
                                    </div>
                                    @if($row['to_check'] > 0 || $returnedDocs > 0)
                                        <div class="mini-badges">
                                            @if($row['to_check'] > 0)<span class="mini-badge check">{{ $row['to_check'] }} to check</span>@endif
                                            @if($returnedDocs > 0)<span class="mini-badge returned">{{ $returnedDocs }} returned</span>@endif
                                        </div>
                                    @endif
                                </td>
                                <td class="center">
                                    @if($row['has_obe_alignment'])
                                        <span class="status-icon status-yes" title="All Course Outcomes are mapped to Program Outcomes"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></span>
                                    @else
                                        <span class="status-icon status-no" title="Course Outcomes are missing or not yet mapped"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></span>
                                    @endif
                                </td>
                                <td class="center">
                                    <div class="completion-bar" style="justify-content:center;">
                                        <div class="completion-track"><div class="completion-fill" style="width: {{ $pct }}%;"></div></div>
                                        <span class="completion-text">{{ $pct }}%</span>
                                    </div>
                                </td>
                                <td class="center">
                                    <button type="button" class="btn-review" onclick="openReview({{ $row['id'] }})">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                                        Review
                                        @if($row['to_check'] > 0)<span class="dot">{{ $row['to_check'] }}</span>@endif
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="empty-row">No courses found.</td></tr>
                        @endforelse
                        <tr id="cf-no-match" style="display:none;"><td colspan="8" class="empty-row">No courses match this filter.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Review one course: Submission (left) and Checked and Validated (right) per document. --}}
    <div class="cf-overlay" id="cf-review-overlay" role="dialog" aria-modal="true" aria-labelledby="cf-review-title">
        <div class="cf-modal">
            <div class="cf-head">
                <div>
                    <div class="cf-title" id="cf-review-title"></div>
                    <div class="cf-sub" id="cf-review-sub"></div>
                </div>
                <button type="button" class="cf-close" onclick="closeReview()" aria-label="Close">&times;</button>
            </div>
            <div class="cf-body">
                <table class="cf-table">
                    <thead>
                        <tr>
                            <th style="width:16%;">Document</th>
                            <th class="col-split" style="width:42%;">Submission</th>
                            <th class="col-split" style="width:42%;">Checked and Validated</th>
                        </tr>
                    </thead>
                    <tbody id="cf-review-rows"></tbody>
                </table>
            </div>
            <div class="cf-foot">
                <span class="legend"><span class="doc-dot pending"></span> Waiting for your check</span>
                <span class="legend"><span class="doc-dot validated"></span> Checked and validated</span>
                <span class="legend"><span class="doc-dot returned"></span> Returned to the Program Head</span>
                <span style="margin-left:auto;">A returned file is replaced when the Program Head files a new one in Course Oversight.</span>
            </div>
        </div>
    </div>

    <div class="cf-overlay" id="cf-return-overlay" role="dialog" aria-modal="true" aria-labelledby="cf-return-title" style="z-index:950;">
        <form class="return-modal" method="POST" id="cf-return-form">
            @csrf
            <input type="hidden" name="action" value="return">
            <input type="hidden" name="material_id" id="cf-return-material" value="{{ old('material_id') }}">
            <h3 id="cf-return-title">Return file</h3>
            <p>Return <strong id="cf-return-file"></strong> to <strong id="cf-return-to"></strong>. Tell them what to fix so they can file a corrected copy.</p>
            <textarea name="reason" id="cf-return-reason" maxlength="500" placeholder="e.g. The syllabus is missing the grading system and the Week 10–18 topics." required>{{ old('reason') }}</textarea>
            @error('reason')<div class="field-error">{{ $message }}</div>@enderror
            <div class="row">
                <button type="button" class="btn-cancel" onclick="closeReturn()">Cancel</button>
                <button type="submit" class="btn-send-return">Return File</button>
            </div>
        </form>
    </div>

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

    <script>
        var CF_COURSES = @json($filing->keyBy('id'));
        var CF_CSRF = @json(csrf_token());
        var CF_REVIEW_URL = @json(url('/secretary/course-filing/materials')) ;

        function cfEsc(s) {
            return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
            });
        }

        var CF_ICON_CHECK = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>';
        var CF_ICON_RETURN = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 14 4 9 9 4"/><path d="M20 20v-7a4 4 0 00-4-4H4"/></svg>';

        function cfForm(fileId, action, inner, cls, confirm) {
            return '<form method="POST" action="' + CF_REVIEW_URL + '/' + fileId + '/review"'
                + (confirm ? ' data-confirm="' + cfEsc(confirm) + '" data-confirm-title="Undo check" data-confirm-button="Undo"' : '') + '>'
                + '<input type="hidden" name="_token" value="' + cfEsc(CF_CSRF) + '">'
                + '<input type="hidden" name="action" value="' + action + '">'
                + '<button type="submit" class="' + cls + '">' + inner + '</button></form>';
        }

        function cfCheckCell(f) {
            if (f.status === 'validated') {
                return '<span class="cf-status validated">' + CF_ICON_CHECK + 'Checked and Validated</span>'
                    + '<div class="cf-by">by ' + cfEsc(f.checker || 'Secretary') + ' · ' + cfEsc(f.checked_at)
                    + ' ' + cfForm(f.id, 'reset', 'Undo', 'btn-undo', 'Move this file back to "waiting for your check"?').replace('<form ', '<form style="display:inline;" ') + '</div>';
            }
            if (f.status === 'returned') {
                return '<span class="cf-status returned">' + CF_ICON_RETURN + 'Returned</span>'
                    + '<div class="cf-reason">' + cfEsc(f.return_reason) + '</div>'
                    + '<div class="cf-by">by ' + cfEsc(f.checker || 'Secretary') + ' · ' + cfEsc(f.checked_at)
                    + ' ' + cfForm(f.id, 'reset', 'Undo', 'btn-undo', 'Cancel the return and move this file back to "waiting for your check"?').replace('<form ', '<form style="display:inline;" ') + '</div>';
            }
            return '<div class="cf-actions">'
                + cfForm(f.id, 'validate', CF_ICON_CHECK + 'Validate', 'btn-validate')
                + '<button type="button" class="btn-return" onclick="openReturn(' + f.id + ')">' + CF_ICON_RETURN + 'Return</button>'
                + '</div>';
        }

        var cfOpenCourse = null;

        function openReview(courseId) {
            var c = CF_COURSES[courseId];
            if (!c) return;
            cfOpenCourse = courseId;
            document.getElementById('cf-review-title').textContent = c.code + ' — ' + c.title;
            document.getElementById('cf-review-sub').textContent = c.program + ' · ' + c.submitted + ' of ' + c.documents.length
                + ' documents filed · ' + c.validated + ' of ' + c.documents.length + ' checked and validated';

            var html = '';
            c.documents.forEach(function (d) {
                var sub = '', chk = '';
                if (!d.files.length) {
                    sub = '<span class="cf-none">Not filed yet</span>';
                    chk = '<span class="cf-none">—</span>';
                } else {
                    d.files.forEach(function (f) {
                        sub += '<div class="cf-file"><div class="cf-file-name">' + cfEsc(f.title) + '</div>'
                            + '<div class="cf-file-meta">' + cfEsc(f.file_type) + ' · ' + cfEsc(f.version) + ' · Filed by '
                            + cfEsc(f.uploader) + ' on ' + cfEsc(f.uploaded_at)
                            + '<a href="' + cfEsc(f.view_url) + '" target="_blank">View</a></div></div>';
                        chk += '<div class="cf-check">' + cfCheckCell(f) + '</div>';
                    });
                }
                html += '<tr><td class="cf-doc">' + cfEsc(d.label) + '</td><td>' + sub + '</td><td>' + chk + '</td></tr>';
            });
            document.getElementById('cf-review-rows').innerHTML = html;
            document.getElementById('cf-review-overlay').classList.add('open');
        }

        function closeReview() {
            document.getElementById('cf-review-overlay').classList.remove('open');
            cfOpenCourse = null;
        }

        function cfFindFile(fileId) {
            var c = CF_COURSES[cfOpenCourse];
            for (var i = 0; c && i < c.documents.length; i++) {
                for (var j = 0; j < c.documents[i].files.length; j++) {
                    if (c.documents[i].files[j].id === fileId) return c.documents[i].files[j];
                }
            }
            return null;
        }

        function openReturn(fileId, keepText) {
            var f = cfFindFile(fileId);
            if (!f) return;
            document.getElementById('cf-return-form').action = CF_REVIEW_URL + '/' + fileId + '/review';
            document.getElementById('cf-return-file').textContent = '"' + f.title + '"';
            document.getElementById('cf-return-to').textContent = f.uploader;
            document.getElementById('cf-return-material').value = fileId;
            if (!keepText) document.getElementById('cf-return-reason').value = '';
            document.getElementById('cf-return-overlay').classList.add('open');
            setTimeout(function () { document.getElementById('cf-return-reason').focus(); }, 30);
        }

        function closeReturn() {
            document.getElementById('cf-return-overlay').classList.remove('open');
        }

        function filterCourses(mode) {
            var shown = 0;
            document.querySelectorAll('.cf-row').forEach(function (row) {
                var ok = mode === 'all'
                    || (mode === 'check' && row.dataset.check === '1')
                    || (mode === 'returned' && row.dataset.returned === '1')
                    || (mode === 'complete' && row.dataset.complete === '1')
                    || (mode === 'incomplete' && row.dataset.complete === '0');
                row.style.display = ok ? '' : 'none';
                if (ok) shown++;
            });
            document.getElementById('cf-no-match').style.display = shown || !document.querySelector('.cf-row') ? 'none' : '';
        }

        ['cf-review-overlay', 'cf-return-overlay'].forEach(function (id) {
            document.getElementById(id).addEventListener('click', function (e) {
                if (e.target === this) (id === 'cf-return-overlay' ? closeReturn : closeReview)();
            });
        });
        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape') return;
            if (document.getElementById('cf-return-overlay').classList.contains('open')) closeReturn();
            else if (cfOpenCourse !== null) closeReview();
        });

        // After a Validate / Return / Undo, reopen the course that was being reviewed.
        @if(session('open_course'))
            openReview({{ (int) session('open_course') }});
        @elseif($errors->has('reason') && old('material_id'))
            (function () {
                var id = {{ (int) old('material_id') }};
                for (var cid in CF_COURSES) {
                    if (CF_COURSES[cid].documents.some(function (d) { return d.files.some(function (f) { return f.id === id; }); })) {
                        openReview(Number(cid));
                        openReturn(id, true);
                        break;
                    }
                }
            })();
        @endif
    </script>

    @include('partials.system-feedback')
</body>
</html>