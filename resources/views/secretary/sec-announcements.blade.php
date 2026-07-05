<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements – CBMA System</title>
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
        .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 20px; }
        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 3px; }
        .page-sub { font-size: 11.5px; color: #888; }
        .btn-post { display: flex; align-items: center; gap: 6px; background: #0f2557; color: #fff; border: none; border-radius: 6px; font-size: 12.5px; font-weight: 600; padding: 9px 18px; cursor: pointer; transition: background 0.15s; }
        .btn-post:hover { background: #1a3a7a; }
        .ann-panel { background: #fff; border: 1px solid #e4e4e4; border-radius: 8px; padding: 10px 0; min-height: 200px; }
        .ann-item { padding: 14px 20px; border-bottom: 1px solid #f0f0f0; }
        .ann-item:last-child { border-bottom: none; }
        .ann-item:hover { background: #fafbff; }
        .ann-item-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 6px; }
        .ann-title { font-size: 13px; font-weight: 700; color: #1a1a2e; margin-bottom: 2px; }
        .ann-meta { font-size: 10.5px; color: #aaa; }
        .ann-body { font-size: 12px; color: #555; line-height: 1.6; margin-top: 6px; }
        .badge { font-size: 10.5px; font-weight: 600; padding: 3px 12px; border-radius: 12px; }
        .badge-general { background: #dbeafe; color: #1e40af; }
        .badge-priority { background: #fef9c3; color: #92400e; }
        .badge-faculty { background: #d1fae5; color: #065f46; }
        .badge-urgent { background: #fee2e2; color: #991b1b; }
        .source-badge { font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 10px; background: #f0f4ff; color: #0f2557; margin-left: 6px; }
        .ann-actions { display: flex; align-items: center; gap: 6px; flex-shrink: 0; margin-left: 12px; }
        .btn-del { background: none; border: none; color: #ccc; cursor: pointer; padding: 3px 6px; border-radius: 4px; transition: color 0.15s, background 0.15s; }
        .btn-del:hover { color: #ef4444; background: #fee2e2; }
        .btn-del svg { width: 14px; height: 14px; }
        .ann-empty { text-align: center; padding: 50px 20px; color: #bbb; font-size: 12.5px; }
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 999; align-items: center; justify-content: center; }
        .modal-overlay.open { display: flex; }
        .modal { background: #fff; border-radius: 10px; padding: 24px 26px; width: 480px; max-width: 95vw; box-shadow: 0 8px 32px rgba(0,0,0,0.25); }
        .modal-title { font-size: 15px; font-weight: 700; color: #1a1a2e; margin-bottom: 18px; }
        .modal-field { margin-bottom: 13px; }
        .modal-field label { display: block; font-size: 11.5px; font-weight: 700; color: #333; margin-bottom: 4px; }
        .modal-field input, .modal-field select, .modal-field textarea { width: 100%; padding: 7px 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 12.5px; color: #333; outline: none; font-family: Arial, sans-serif; transition: border-color 0.15s; }
        .modal-field input:focus, .modal-field select:focus, .modal-field textarea:focus { border-color: #0f2557; }
        .modal-field textarea { resize: vertical; min-height: 100px; }
        .modal-field select { appearance: none; -webkit-appearance: none; background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%23666' d='M5 7L0 2h10z'/%3E%3C/svg%3E") no-repeat right 10px center; cursor: pointer; }
        .modal-error { display: none; background: #fee2e2; border: 1px solid #fca5a5; color: #b91c1c; font-size: 11px; padding: 6px 10px; border-radius: 4px; margin-bottom: 12px; }
        .modal-note { background: #eef2ff; border: 1px solid #c7d2fe; color: #3730a3; font-size: 11px; padding: 8px 10px; border-radius: 4px; margin-bottom: 12px; }
        .modal-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 18px; }
        .btn-cancel { background: #fff; border: 1px solid #ccc; color: #444; font-size: 12.5px; font-weight: 600; padding: 8px 18px; border-radius: 5px; cursor: pointer; }
        .btn-cancel:hover { background: #f5f5f5; }
        .btn-save-modal { background: #0f2557; color: #fff; border: none; font-size: 12.5px; font-weight: 600; padding: 8px 20px; border-radius: 5px; cursor: pointer; }
        .btn-save-modal:hover { background: #1a3a7a; }
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
            <li class="{{ request()->is('secretary/dashboard') ? 'active' : '' }}">
                <a href="{{ url('/secretary/dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>Dashboard</a>
            </li>
            @endif
            @if($navPermissions['template-review'] ?? true)
            <li class="{{ request()->is('secretary/template-review*') ? 'active' : '' }}">
                <a href="{{ url('/secretary/template-review') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>Template Review</a>
            </li>
            @endif
            @if($navPermissions['course-oversight'] ?? true)
            <li class="{{ request()->is('secretary/course-oversight*') ? 'active' : '' }}">
                <a href="{{ url('/secretary/course-oversight') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>Course Oversight</a>
            </li>
            @endif
            @if($navPermissions['account-management'] ?? true)
            <li class="{{ request()->is('secretary/account-management*') ? 'active' : '' }}">
                <a href="{{ url('/secretary/account-management') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>Account Management</a>
            </li>
            @endif
            @if($navPermissions['announcements'] ?? true)
            <li class="{{ request()->is('secretary/announcements*') ? 'active' : '' }}">
                <a href="{{ url('/secretary/announcements') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>Announcements</a>
            </li>
            @endif
            @if($navPermissions['calendar'] ?? true)
            <li class="{{ request()->is('secretary/calendar*') ? 'active' : '' }}">
                <a href="{{ url('/secretary/calendar') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Calendar of Activities</a>
            </li>
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

            <div class="page-header">
                <div>
                    <div class="page-title">Announcements</div>
                    <div class="page-sub">Ikaw ay naka-post para sa program: <strong>{{ auth()->user()->program }}</strong></div>
                </div>
                <button class="btn-post" type="button" onclick="openModal()">+ Post Announcement</button>
            </div>

            <div class="ann-panel">
                @forelse($announcements as $ann)
                    <div class="ann-item">
                        <div class="ann-item-header">
                            <div>
                                <div class="ann-title">{{ $ann->title }}</div>
                                <div class="ann-meta">
                                    Posted by <strong>{{ $ann->user->name }}</strong>
                                    <span class="source-badge">{{ ucfirst($ann->user->role === 'secretary' ? 'Secretary' : ucfirst($ann->user->role)) }}</span>
                                    · {{ $ann->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div class="ann-actions">
                                <span class="badge badge-{{ $ann->tag }}">{{ ucfirst($ann->tag) }}</span>
                                @if($ann->user_id === auth()->id())
                                    <form method="POST" action="{{ url('/secretary/announcements/' . $ann->id) }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-del" title="Delete">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        <div class="ann-body">{{ $ann->body }}</div>
                    </div>
                @empty
                    <div class="ann-empty">No announcements yet.</div>
                @endforelse
            </div>

        </div>
    </div>

    <div class="modal-overlay" id="modal-overlay">
        <div class="modal">
            <form method="POST" action="{{ url('/secretary/announcements') }}">
                @csrf
                <div class="modal-title">Post Announcement</div>
                <div class="modal-note">Ang post mo ay makikita ng lahat ng roles sa CBMA System. <strong>{{ auth()->user()->program }}</strong>.</div>

                @if($errors->any())
                    <div class="modal-error" style="display:block;">{{ $errors->first() }}</div>
                @endif

                <div class="modal-field">
                    <label>Title <span style="color:#ef4444">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="Announcement title" required>
                </div>

                <div class="modal-field">
                    <label>Tag</label>
                    <select name="tag">
                        <option value="general" {{ old('tag') == 'general' ? 'selected' : '' }}>General</option>
                        <option value="priority" {{ old('tag') == 'priority' ? 'selected' : '' }}>Priority</option>
                        <option value="faculty" {{ old('tag') == 'faculty' ? 'selected' : '' }}>Faculty only</option>
                        <option value="urgent" {{ old('tag') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>

                <div class="modal-field">
                    <label>Message <span style="color:#ef4444">*</span></label>
                    <textarea name="body" placeholder="Write your announcement here..." required>{{ old('body') }}</textarea>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-save-modal">Post</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() { document.getElementById('modal-overlay').classList.add('open'); }
        function closeModal() { document.getElementById('modal-overlay').classList.remove('open'); }
        document.getElementById('modal-overlay').addEventListener('click', function(e) { if (e.target === this) closeModal(); });
        @if($errors->any())
            document.addEventListener('DOMContentLoaded', function() { document.getElementById('modal-overlay').classList.add('open'); });
        @endif
    </script>
</body>
</html>