<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submissions and Deadline – CBMA System</title>
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
        .nav-badge { margin-left: auto; background: #ef4444; color: #fff; font-size: 9.5px; font-weight: 700; padding: 1px 6px; border-radius: 10px; }
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
        .alert-success { background: #dcfce7; color: #166534; padding: 12px 16px; margin-bottom: 16px; border: 1px solid #bbf7d0; border-radius: 8px; font-size: 13px; }

        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 3px; }
        .page-sub { font-size: 11.5px; color: #888; margin-bottom: 20px; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 16px; margin-bottom: 22px; }
        .stat-card { background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; padding: 18px 20px; display: flex; align-items: center; gap: 14px; }
        .stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .stat-icon svg { width: 22px; height: 22px; }
        .stat-icon.blue { background: #dbeafe; color: #1d4ed8; }
        .stat-icon.red { background: #fee2e2; color: #dc2626; }
        .stat-icon.amber { background: #fef3c7; color: #b45309; }
        .stat-icon.green { background: #d1fae5; color: #059669; }
        .stat-info .stat-value { font-size: 24px; font-weight: 700; color: #1a1a2e; line-height: 1.1; }
        .stat-info .stat-label { font-size: 11.5px; color: #888; margin-top: 2px; }

        .filter-tabs { display: flex; gap: 4px; margin-bottom: 18px; }
        .filter-tab { padding: 7px 16px; font-size: 12px; color: #666; background: #fff; border: 1px solid #e0e0e0; border-radius: 6px; cursor: pointer; user-select: none; }
        .filter-tab:hover { border-color: #0f2557; color: #0f2557; }
        .filter-tab.active { background: #0f2557; color: #fff; border-color: #0f2557; font-weight: 600; }

        .req-card { background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; padding: 18px 20px; margin-bottom: 14px; display: flex; gap: 16px; }
        .req-card.overdue { border-left: 4px solid #ef4444; }
        .req-card.soon { border-left: 4px solid #f59e0b; }
        .req-card.ok { border-left: 4px solid #10b981; }
        .req-card.done { border-left: 4px solid #10b981; }
        .req-type-icon { width: 40px; height: 40px; border-radius: 9px; background: #eef2ff; color: #0f2557; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 18px; }
        .req-body { flex: 1; min-width: 0; }
        .req-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 8px; }
        .req-title { font-size: 14px; font-weight: 700; color: #1a1a2e; margin-bottom: 3px; }
        .req-type { font-size: 11px; color: #888; }
        .req-desc { font-size: 12px; color: #666; margin: 8px 0; line-height: 1.5; }
        .req-deadline-badge { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 700; padding: 5px 12px; border-radius: 14px; white-space: nowrap; }
        .req-deadline-badge svg { width: 11px; height: 11px; }
        .badge-overdue { background: #fee2e2; color: #991b1b; }
        .badge-soon { background: #fef3c7; color: #92400e; }
        .badge-ok { background: #d1fae5; color: #065f46; }

        .my-status-row { display: flex; align-items: center; justify-content: space-between; margin-top: 12px; padding-top: 12px; border-top: 1px solid #f0f0f0; flex-wrap: wrap; gap: 8px; }
        .my-status-info { font-size: 12px; color: #555; }
        .my-status-badge { font-size: 10.5px; font-weight: 700; padding: 3px 10px; border-radius: 12px; margin-left: 6px; }
        .status-submitted { background: #d1fae5; color: #065f46; }
        .status-late { background: #fee2e2; color: #991b1b; }
        .file-link { color: #1d4ed8; text-decoration: none; font-weight: 600; }
        .file-link:hover { text-decoration: underline; }

        .upload-form { display: flex; align-items: center; gap: 8px; margin-top: 12px; }
        .file-picker { display: flex; align-items: center; gap: 8px; flex: 1; min-width: 0; }
        .file-picker input[type=file] { display: none; }
        .btn-choose { display: flex; align-items: center; gap: 5px; background: #fff; color: #333; border: 1px solid #d0d0d0; font-size: 11.5px; font-weight: 600; padding: 7px 12px; border-radius: 5px; cursor: pointer; white-space: nowrap; }
        .btn-choose:hover { background: #f5f5f5; }
        .btn-choose svg { width: 12px; height: 12px; }
        .file-picker-name { font-size: 11.5px; color: #888; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .btn-submit { background: #0f2557; color: #fff; border: none; font-size: 12px; font-weight: 600; padding: 8px 16px; border-radius: 5px; cursor: pointer; white-space: nowrap; }
        .btn-submit:hover { background: #1a3a7a; }

        .empty-state { text-align: center; padding: 50px 20px; color: #bbb; font-size: 13px; background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; }

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
            <li class="{{ request()->is('faculty/dashboard') ? 'active' : '' }}"><a href="{{ url('/faculty/dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>Dashboard</a></li>
            @endif
            @if($navPermissions['my-template'] ?? true)
            <li class="{{ request()->is('faculty/my-template*') ? 'active' : '' }}"><a href="{{ url('/faculty/my-template') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>Templates</a></li>
            @endif
            @if($navPermissions['exam-generator'] ?? true)
            <li class="{{ request()->is('faculty/exam-generator*') ? 'active' : '' }}"><a href="{{ url('/faculty/exam-generator') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>Exam Generator</a></li>
            @endif
            @if($navPermissions['shared-library'] ?? true)
            <li class="{{ request()->is('faculty/shared-library*') ? 'active' : '' }}"><a href="{{ url('/faculty/shared-library') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>Shared Library</a></li>
            @endif
            @if($navPermissions['course-coordination'] ?? true)
            <li class="{{ request()->is('faculty/course-coordination*') ? 'active' : '' }}"><a href="{{ url('/faculty/course-coordination') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>Course Coordination</a></li>
            @endif
            @if($navPermissions['analytics'] ?? true)
            <li class="{{ request()->is('faculty/analytics*') ? 'active' : '' }}"><a href="{{ url('/faculty/analytics') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>Analytics</a></li>
            @endif
            @if($navPermissions['calendar'] ?? true)
            <li class="{{ request()->is('faculty/calendar*') ? 'active' : '' }}"><a href="{{ url('/faculty/calendar') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Calendar of Activities</a></li>
            @endif
            @if($navPermissions['announcements'] ?? true)
            <li class="{{ request()->is('faculty/announcements*') ? 'active' : '' }}"><a href="{{ url('/faculty/announcements') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>Announcements
                @if(($unreadAnnouncementsCount ?? 0) > 0)
                    <span class="nav-badge">{{ $unreadAnnouncementsCount }}</span>
                @endif
            </a></li>
            @endif
            @if($navPermissions['submissions'] ?? true)
            <li class="{{ request()->is('faculty/submissions*') ? 'active' : '' }}"><a href="{{ url('/faculty/submissions') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                Submissions and Deadline
                @if($urgentCount > 0)
                    <span class="nav-badge">{{ $urgentCount }}</span>
                @endif
            </a></li>
            @endif
            @if($navPermissions['cms'] ?? true)
            <li class="{{ request()->is('faculty/cms*') ? 'active' : '' }}"><a href="{{ url('/faculty/cms') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>CMS</a></li>
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
                <span class="role-badge">Faculty</span>
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

            <div class="page-title">Submissions and Deadline</div>
            <div class="page-sub">Requirements sorted by nearest deadline</div>

            @php
                $overdueTotal = $requirements->filter(fn($r) => !$r['submission'] && $r['days_left'] < 0)->count();
                $soonTotal = $requirements->filter(fn($r) => !$r['submission'] && $r['days_left'] >= 0 && $r['days_left'] <= 3)->count();
                $submittedTotal = $requirements->filter(fn($r) => $r['submission'])->count();
            @endphp

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg></div>
                    <div class="stat-info"><div class="stat-value">{{ $requirements->count() }}</div><div class="stat-label">Total Requirements</div></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon red"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
                    <div class="stat-info"><div class="stat-value">{{ $overdueTotal }}</div><div class="stat-label">Overdue</div></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon amber"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
                    <div class="stat-info"><div class="stat-value">{{ $soonTotal }}</div><div class="stat-label">Due Soon</div></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
                    <div class="stat-info"><div class="stat-value">{{ $submittedTotal }}</div><div class="stat-label">Submitted</div></div>
                </div>
            </div>

            <div class="filter-tabs">
                <span class="filter-tab active" onclick="filterReqs('all', this)">All</span>
                <span class="filter-tab" onclick="filterReqs('overdue', this)">Overdue</span>
                <span class="filter-tab" onclick="filterReqs('soon', this)">Due Soon</span>
                <span class="filter-tab" onclick="filterReqs('done', this)">Submitted</span>
            </div>

            <div id="req-list">
            @forelse($requirements as $req)
                @php
                    $daysLeft = $req['days_left'];
                    $sub = $req['submission'];

                    if ($sub) {
                        $cardClass = 'done';
                        $badgeClass = $sub->status === 'late' ? 'badge-overdue' : 'badge-ok';
                        $badgeText = $sub->status === 'late' ? 'Submitted Late' : 'Submitted';
                    } elseif ($daysLeft < 0) {
                        $cardClass = 'overdue'; $badgeClass = 'badge-overdue'; $badgeText = abs($daysLeft) . ' day(s) overdue';
                    } elseif ($daysLeft <= 3) {
                        $cardClass = 'soon'; $badgeClass = 'badge-soon'; $badgeText = $daysLeft == 0 ? 'Due today' : $daysLeft . ' day(s) left';
                    } else {
                        $cardClass = 'ok'; $badgeClass = 'badge-ok'; $badgeText = $daysLeft . ' day(s) left';
                    }

                    $typeIcon = match(true) {
                        str_contains(strtolower($req['type']), 'syllabus') => '📘',
                        str_contains(strtolower($req['type']), 'lesson') => '📗',
                        str_contains(strtolower($req['type']), 'tos') => '📊',
                        str_contains(strtolower($req['type']), 'exam') => '📝',
                        default => '📄',
                    };
                @endphp
                <div class="req-card {{ $cardClass }}" data-status="{{ $cardClass }}">
                    <div class="req-type-icon">{{ $typeIcon }}</div>
                    <div class="req-body">
                        <div class="req-top">
                            <div>
                                <div class="req-title">{{ $req['title'] }}</div>
                                <div class="req-type">{{ $req['type'] }} · Due {{ $req['deadline']->format('M d, Y') }}</div>
                            </div>
                            <span class="req-deadline-badge {{ $badgeClass }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                {{ $badgeText }}
                            </span>
                        </div>

                        @if($req['description'])
                            <div class="req-desc">{{ $req['description'] }}</div>
                        @endif

                        @if($sub)
                            <div class="my-status-row">
                                <div class="my-status-info">
                                    You submitted: <a class="file-link" href="{{ Storage::url($sub->file_path) }}" target="_blank">{{ $sub->file_name }}</a>
                                    <span class="my-status-badge status-{{ $sub->status }}">{{ ucfirst($sub->status) }}</span>
                                </div>
                            </div>
                            <form class="upload-form" method="POST" action="{{ url('/faculty/submissions/' . $req['id']) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="file-picker">
                                    <button type="button" class="btn-choose" onclick="this.nextElementSibling.nextElementSibling.click()">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                        Choose File
                                    </button>
                                    <span class="file-picker-name">No file chosen</span>
                                    <input type="file" name="file" accept=".pdf,.doc,.docx" required onchange="this.previousElementSibling.textContent = this.files[0] ? this.files[0].name : 'No file chosen'">
                                </div>
                                <button type="submit" class="btn-submit">Re-submit</button>
                            </form>
                        @else
                            <form class="upload-form" method="POST" action="{{ url('/faculty/submissions/' . $req['id']) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="file-picker">
                                    <button type="button" class="btn-choose" onclick="this.nextElementSibling.nextElementSibling.click()">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                        Choose File
                                    </button>
                                    <span class="file-picker-name">No file chosen</span>
                                    <input type="file" name="file" accept=".pdf,.doc,.docx" required onchange="this.previousElementSibling.textContent = this.files[0] ? this.files[0].name : 'No file chosen'">
                                </div>
                                <button type="submit" class="btn-submit">Submit</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-state">No submission requirements at this time.</div>
            @endforelse
            </div>
        </div>
    </div>

    <script>
        function filterReqs(status, el) {
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            el.classList.add('active');
            document.querySelectorAll('#req-list .req-card').forEach(card => {
                card.style.display = (status === 'all' || card.dataset.status === status) ? '' : 'none';
            });
        }
    </script>

</body>
</html>