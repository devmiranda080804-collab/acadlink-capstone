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
        .alert-success { background: #dcfce7; color: #166534; padding: 12px 16px; margin-bottom: 16px; border: 1px solid #bbf7d0; border-radius: 8px; font-size: 13px; }

        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 3px; }
        .page-sub { font-size: 11.5px; color: #888; margin-bottom: 20px; }

        .review-layout { display: grid; grid-template-columns: 1fr 320px; gap: 20px; align-items: start; }

        /* Template list */
        .review-card { background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; padding: 16px 18px; margin-bottom: 14px; cursor: pointer; transition: box-shadow 0.15s, border-color 0.15s; }
        .review-card:hover { box-shadow: 0 3px 12px rgba(0,0,0,0.07); }
        .review-card.selected { border-color: #0f2557; box-shadow: 0 0 0 1px #0f2557; }
        .review-card-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 8px; }
        .review-card-title { font-size: 13px; font-weight: 700; color: #1a1a2e; display: flex; align-items: center; gap: 6px; }
        .review-card-course { font-size: 12.5px; color: #0f2557; font-weight: 600; margin: 6px 0 3px; }
        .review-card-type { font-size: 11px; color: #888; }
        .review-card-meta { font-size: 10.5px; color: #aaa; margin-top: 6px; }

        .status-badge { font-size: 10px; font-weight: 700; padding: 4px 10px; border-radius: 12px; white-space: nowrap; }
        .status-pending_review   { background: #dbeafe; color: #1e40af; }
        .status-needs_revision   { background: #fee2e2; color: #991b1b; }
        .status-pending_approval { background: #fef9c3; color: #92400e; }
        .status-approved         { background: #d1fae5; color: #065f46; }
        .status-rejected         { background: #f3f4f6; color: #6b7280; }

        .empty-state { text-align: center; padding: 50px 20px; color: #bbb; font-size: 13px; background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; }

        /* Preview panel */
        .preview-panel { background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; padding: 20px; position: sticky; top: 0; }
        .preview-title { font-size: 14px; font-weight: 700; color: #1a1a2e; margin-bottom: 16px; }
        .preview-field { margin-bottom: 14px; }
        .preview-field .plabel { font-size: 10.5px; font-weight: 700; color: #999; text-transform: uppercase; margin-bottom: 3px; }
        .preview-field .pvalue { font-size: 12.5px; color: #333; }
        .preview-empty { text-align: center; color: #bbb; font-size: 12px; padding: 30px 10px; }

        .btn-view-file { display: inline-flex; align-items: center; gap: 5px; background: #fff; border: 1px solid #d0d0d0; color: #333; font-size: 11.5px; font-weight: 600; padding: 7px 14px; border-radius: 5px; text-decoration: none; margin-bottom: 16px; }
        .btn-view-file:hover { background: #f5f5f5; }
        .btn-view-file svg { width: 13px; height: 13px; }

        .review-actions { display: flex; flex-direction: column; gap: 8px; margin-top: 8px; }
        .btn-approve { background: #16a34a; color: #fff; border: none; font-size: 12.5px; font-weight: 600; padding: 10px; border-radius: 6px; cursor: pointer; width: 100%; }
        .btn-approve:hover { background: #15803d; }
        .btn-revision { background: #fff; color: #b45309; border: 1px solid #fcd34d; font-size: 12.5px; font-weight: 600; padding: 10px; border-radius: 6px; cursor: pointer; width: 100%; }
        .btn-revision:hover { background: #fffbeb; }

        .already-reviewed { font-size: 11.5px; color: #888; background: #f9fafb; border: 1px solid #eee; border-radius: 6px; padding: 10px; line-height: 1.5; }

        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 999; align-items: center; justify-content: center; }
        .modal-overlay.open { display: flex; }
        .modal { background: #fff; border-radius: 10px; padding: 24px 26px; width: 440px; max-width: 95vw; box-shadow: 0 8px 32px rgba(0,0,0,0.25); }
        .modal-title { font-size: 15px; font-weight: 700; color: #1a1a2e; margin-bottom: 8px; }
        .modal-sub { font-size: 11.5px; color: #888; margin-bottom: 16px; }
        .modal-field label { display: block; font-size: 11.5px; font-weight: 700; color: #333; margin-bottom: 4px; }
        .modal-field textarea { width: 100%; min-height: 90px; padding: 8px 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 12.5px; outline: none; font-family: Arial, sans-serif; resize: vertical; }
        .modal-field textarea:focus { border-color: #0f2557; }
        .modal-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 18px; }
        .btn-cancel { background: #fff; border: 1px solid #ccc; color: #444; font-size: 12.5px; font-weight: 600; padding: 8px 18px; border-radius: 5px; cursor: pointer; }
        .btn-cancel:hover { background: #f5f5f5; }
        .btn-submit-revision { background: #b45309; color: #fff; border: none; font-size: 12.5px; font-weight: 600; padding: 8px 20px; border-radius: 5px; cursor: pointer; }
        .btn-submit-revision:hover { background: #92400e; }

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
            <li class="{{ request()->is('program-head/dashboard') ? 'active' : '' }}"><a href="{{ url('/program-head/dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>Dashboard</a></li>
            @endif
            @if($navPermissions['template-review'] ?? true)
            <li class="{{ request()->is('program-head/template-review*') ? 'active' : '' }}"><a href="{{ url('/program-head/template-review') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>Template Distribution</a></li>
            @endif
            @if($navPermissions['course-oversight'] ?? true)
            <li class="{{ request()->is('program-head/course-oversight*') ? 'active' : '' }}"><a href="{{ url('/program-head/course-oversight') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>Course Oversight</a></li>
            @endif
            @if($navPermissions['program-assignment'] ?? true)
            <li class="{{ request()->is('program-head/program-assignment*') ? 'active' : '' }}"><a href="{{ url('/program-head/program-assignment') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>Program Assignment</a></li>
            @endif
            @if($navPermissions['submissions'] ?? true)
            <li class="{{ request()->is('program-head/submissions*') ? 'active' : '' }}"><a href="{{ url('/program-head/submissions') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>Submissions and Deadline</a></li>
            @endif
            @if($navPermissions['account-management'] ?? true)
            <li class="{{ request()->is('program-head/account-management*') ? 'active' : '' }}"><a href="{{ url('/program-head/account-management') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>Account Management</a></li>
            @endif
            @if($navPermissions['announcements'] ?? true)
            <li class="{{ request()->is('program-head/announcements*') ? 'active' : '' }}"><a href="{{ url('/program-head/announcements') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>Announcements
                @if(($unreadAnnouncementsCount ?? 0) > 0)
                    <span class="nav-badge">{{ $unreadAnnouncementsCount }}</span>
                @endif
            </a></li>
            @endif
            @if($navPermissions['calendar'] ?? true)
            <li class="{{ request()->is('program-head/calendar*') ? 'active' : '' }}"><a href="{{ url('/program-head/calendar') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Calendar of Activities</a></li>
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
                <span class="role-badge">Program Head</span>
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
            <div class="page-sub">Templates the Secretary has forwarded to you. Distribute each one so every faculty member in <strong>{{ $myProgram }}</strong> can access it.</div>

            <div>
                @forelse($templates as $template)
                    @php $row = $template->programRow($myProgram); @endphp
                    <div class="review-card">
                        <div class="review-card-top">
                            <div class="review-card-title">
                                <svg style="width:15px;height:15px;color:#0f2557;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                {{ $template->title }}
                            </div>
                            @if($row && $row->distributed_at)
                                <span class="status-badge status-approved">Distributed</span>
                            @else
                                <span class="status-badge status-pending_approval">Not yet distributed</span>
                            @endif
                        </div>
                        <div class="review-card-type">{{ str_replace('_', ' ', $template->type) }}</div>
                        <div class="review-card-meta">Uploaded by {{ $template->creator->name }} • {{ $template->created_at->format('Y-m-d') }}</div>

                        <div style="margin-top:12px; display:flex; gap:8px;">
                            <a class="btn-view-file" style="margin-bottom:0;" href="{{ Storage::url($template->file_path) }}" target="_blank">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                View Document
                            </a>
                            @if(!$row || !$row->distributed_at)
                                <form method="POST" action="{{ url('/program-head/template-review/' . $template->id . '/distribute') }}">
                                    @csrf
                                    <button type="submit" class="btn-approve" style="width:auto; padding:9px 16px;">Distribute to My Faculty</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="empty-state">No templates have been forwarded to your program yet.</div>
                @endforelse
            </div>
        </div>
    </div>

</body>
</html>