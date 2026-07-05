<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Template Distribution – CBMA System</title>
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
        .alert-success { background: #dcfce7; color: #166534; padding: 12px 16px; margin-bottom: 16px; border: 1px solid #bbf7d0; border-radius: 8px; font-size: 13px; }

        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 3px; }
        .page-sub { font-size: 11.5px; color: #888; margin-bottom: 20px; }

        .program-tabs { display: flex; gap: 4px; margin-bottom: 18px; }
        .program-tab { padding: 7px 16px; font-size: 12px; color: #666; background: #fff; border: 1px solid #e0e0e0; border-radius: 6px; cursor: pointer; text-decoration: none; }
        .program-tab:hover { border-color: #0f2557; color: #0f2557; }
        .program-tab.active { background: #0f2557; color: #fff; border-color: #0f2557; font-weight: 600; }

        .dist-panel { background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; overflow: hidden; }
        .dist-table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
        .dist-table thead tr { background: #fafafa; border-bottom: 1px solid #eee; }
        .dist-table th { padding: 12px 16px; text-align: left; font-size: 11.5px; font-weight: 700; color: #666; }
        .dist-table td { padding: 13px 16px; border-bottom: 1px solid #f5f5f5; color: #333; vertical-align: middle; }
        .dist-table tbody tr:last-child td { border-bottom: none; }
        .dist-table tbody tr:hover { background: #fafbff; }
        .dist-table td.empty-row { text-align: center; color: #999; padding: 40px 16px; }

        .tmpl-name { display: flex; align-items: center; gap: 8px; }
        .tmpl-name .ficon { font-size: 15px; }
        .type-label { color: #0f2557; font-weight: 600; }
        .program-tag { display: inline-block; font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 10px; background: #f0f4ff; color: #0f2557; }
        .version-tag { font-size: 11.5px; color: #666; }

        .dist-status { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 600; }
        .dist-status.yes { color: #16a34a; }
        .dist-status.no { color: #999; }
        .dist-dot { width: 7px; height: 7px; border-radius: 50%; background: currentColor; }

        .action-buttons { display: flex; gap: 6px; }
        .btn-distribute { display: inline-flex; align-items: center; gap: 5px; background: #0f2557; color: #fff; border: none; font-size: 11px; font-weight: 600; padding: 6px 14px; border-radius: 5px; cursor: pointer; }
        .btn-distribute:hover { background: #1a3a7a; }
        .btn-recall { display: inline-flex; align-items: center; gap: 5px; background: #fff; color: #b45309; border: 1px solid #fcd34d; font-size: 11px; font-weight: 600; padding: 6px 14px; border-radius: 5px; cursor: pointer; }
        .btn-recall:hover { background: #fffbeb; }
        .btn-view { display: inline-flex; align-items: center; gap: 4px; background: #fff; color: #444; border: 1px solid #d0d0d0; font-size: 11px; font-weight: 600; padding: 6px 12px; border-radius: 5px; text-decoration: none; }
        .btn-view:hover { background: #f5f5f5; }
        .action-buttons svg { width: 12px; height: 12px; }

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

            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif

            <div class="page-title">Template Distribution</div>
            <div class="page-sub">Distribute official approved templates to faculty and staff</div>

            {{-- Program filter tabs --}}
            <div class="program-tabs">
                <a class="program-tab {{ !request('program') ? 'active' : '' }}" href="{{ url('/secretary/template-distribution') }}">All Programs</a>
                <a class="program-tab {{ request('program') == 'FMAD' ? 'active' : '' }}" href="{{ url('/secretary/template-distribution?program=FMAD') }}">FMAD</a>
                <a class="program-tab {{ request('program') == 'OFD' ? 'active' : '' }}" href="{{ url('/secretary/template-distribution?program=OFD') }}">OFD</a>
                <a class="program-tab {{ request('program') == 'BAD' ? 'active' : '' }}" href="{{ url('/secretary/template-distribution?program=BAD') }}">BAD</a>
            </div>

            <div class="dist-panel">
                <table class="dist-table">
                    <thead>
                        <tr>
                            <th>Template Name</th>
                            <th>Type</th>
                            <th>Program</th>
                            <th>Submitted By</th>
                            <th>Approved</th>
                            <th>Distribution</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $template)
                            <tr>
                                <td>
                                    <div class="tmpl-name">
                                        <span class="ficon">@if($template->file_type == 'pdf') 📄 @else 📝 @endif</span>
                                        {{ $template->title }}
                                    </div>
                                </td>
                                <td><span class="type-label">{{ ucwords(str_replace('_', ' ', $template->type)) }}</span></td>
                                <td><span class="program-tag">{{ $template->program }}</span></td>
                                <td>{{ $template->faculty->name }}</td>
                                <td>{{ $template->updated_at->format('Y-m-d') }}</td>
                                <td>
                                    @if($template->distributed_at)
                                        <span class="dist-status yes"><span class="dist-dot"></span>Distributed</span>
                                    @else
                                        <span class="dist-status no"><span class="dist-dot"></span>Not yet</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a class="btn-view" href="{{ Storage::url($template->file_path) }}" target="_blank">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                            View
                                        </a>
                                        @if($template->distributed_at)
                                            <form method="POST" action="{{ url('/secretary/template-distribution/' . $template->id . '/undistribute') }}" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn-recall">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                                                    Recall
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ url('/secretary/template-distribution/' . $template->id . '/distribute') }}" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn-distribute">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                                    Distribute
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="empty-row">Wala pang approved templates para i-distribute.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</body>
</html>