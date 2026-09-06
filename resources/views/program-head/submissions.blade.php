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

        .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 22px; }
        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 3px; }
        .page-sub { font-size: 11.5px; color: #888; }
        .btn-add { display: flex; align-items: center; gap: 6px; background: #0f2557; color: #fff; border: none; border-radius: 6px; font-size: 12.5px; font-weight: 600; padding: 9px 18px; cursor: pointer; white-space: nowrap; }
        .btn-add:hover { background: #1a3a7a; }

        .req-card { background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; padding: 18px 20px; margin-bottom: 14px; }
        .req-card.overdue { border-left: 4px solid #ef4444; }
        .req-card.soon { border-left: 4px solid #f59e0b; }
        .req-card.ok { border-left: 4px solid #10b981; }
        .req-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 8px; }
        .req-title { font-size: 14px; font-weight: 700; color: #1a1a2e; margin-bottom: 3px; }
        .req-type { font-size: 11px; color: #888; }
        .req-desc { font-size: 12px; color: #666; margin: 8px 0; line-height: 1.5; }
        .req-deadline-badge { font-size: 11px; font-weight: 700; padding: 5px 12px; border-radius: 14px; white-space: nowrap; }
        .badge-overdue { background: #fee2e2; color: #991b1b; }
        .badge-soon { background: #fef3c7; color: #92400e; }
        .badge-ok { background: #d1fae5; color: #065f46; }

        .req-progress { display: flex; align-items: center; gap: 10px; margin: 12px 0; }
        .progress-track { flex: 1; height: 8px; background: #eee; border-radius: 4px; overflow: hidden; }
        .progress-fill { height: 100%; background: #0f2557; border-radius: 4px; }
        .progress-text { font-size: 11.5px; color: #666; font-weight: 600; white-space: nowrap; }

        .req-actions { display: flex; gap: 8px; margin-top: 10px; }
        .btn-mini { font-size: 11px; font-weight: 600; padding: 5px 12px; border-radius: 5px; cursor: pointer; border: 1px solid; background: #fff; }
        .btn-mini-toggle { color: #0f2557; border-color: #cdd7ec; }
        .btn-mini-toggle:hover { background: #f0f4ff; }
        .btn-mini-edit { color: #0f2557; border-color: #cdd7ec; }
        .btn-mini-edit:hover { background: #f0f4ff; }
        .btn-mini-del { color: #ef4444; border-color: #fca5a5; }
        .btn-mini-del:hover { background: #fee2e2; }

        .submitters-list { display: none; margin-top: 12px; border-top: 1px solid #f0f0f0; padding-top: 12px; }
        .submitters-list.open { display: block; }
        .submitter-row { display: flex; align-items: center; justify-content: space-between; padding: 7px 0; font-size: 12px; }
        .submitter-name { color: #333; font-weight: 600; }
        .submitter-status { font-size: 10px; font-weight: 700; padding: 3px 9px; border-radius: 10px; }
        .status-submitted { background: #d1fae5; color: #065f46; }
        .status-late { background: #fee2e2; color: #991b1b; }
        .status-pending { background: #f3f4f6; color: #6b7280; }
        .submitter-link { font-size: 11px; color: #1d4ed8; text-decoration: none; font-weight: 600; }
        .submitter-link:hover { text-decoration: underline; }

        .empty-state { text-align: center; padding: 50px 20px; color: #bbb; font-size: 13px; background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; }

        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 999; align-items: center; justify-content: center; }
        .modal-overlay.open { display: flex; }
        .modal { background: #fff; border-radius: 10px; padding: 24px 26px; width: 460px; max-width: 95vw; box-shadow: 0 8px 32px rgba(0,0,0,0.25); }
        .modal-title { font-size: 15px; font-weight: 700; color: #1a1a2e; margin-bottom: 18px; }
        .modal-field { margin-bottom: 13px; }
        .modal-field label { display: block; font-size: 11.5px; font-weight: 700; color: #333; margin-bottom: 4px; }
        .modal-field input, .modal-field select, .modal-field textarea { width: 100%; padding: 8px 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 12.5px; color: #333; outline: none; font-family: Arial, sans-serif; }
        .modal-field input:focus, .modal-field select:focus, .modal-field textarea:focus { border-color: #0f2557; }
        .modal-field textarea { resize: vertical; min-height: 60px; }
        .modal-field select { appearance: none; -webkit-appearance: none; background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%23666' d='M5 7L0 2h10z'/%3E%3C/svg%3E") no-repeat right 10px center; cursor: pointer; }
        .modal-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .modal-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 18px; }
        .btn-cancel { background: #fff; border: 1px solid #ccc; color: #444; font-size: 12.5px; font-weight: 600; padding: 8px 18px; border-radius: 5px; cursor: pointer; }
        .btn-cancel:hover { background: #f5f5f5; }
        .btn-save { background: #0f2557; color: #fff; border: none; font-size: 12.5px; font-weight: 600; padding: 8px 20px; border-radius: 5px; cursor: pointer; }
        .btn-save:hover { background: #1a3a7a; }

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
            <li class="{{ request()->is('program-head/template-review*') ? 'active' : '' }}"><a href="{{ url('/program-head/template-review') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>Template Review</a></li>
            @endif
            @if($navPermissions['course-oversight'] ?? true)
            <li class="{{ request()->is('program-head/course-oversight*') ? 'active' : '' }}"><a href="{{ url('/program-head/course-oversight') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>Course Oversight</a></li>
            @endif
            @if($navPermissions['course-assignment'] ?? true)
            <li class="{{ request()->is('program-head/course-assignment*') ? 'active' : '' }}"><a href="{{ url('/program-head/course-assignment') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>Course Assignment</a></li>
            @endif
            @if($navPermissions['submissions'] ?? true)
            <li class="{{ request()->is('program-head/submissions*') ? 'active' : '' }}"><a href="{{ url('/program-head/submissions') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>Submissions and Deadline</a></li>
            @endif
            @if($navPermissions['account-management'] ?? true)
            <li class="{{ request()->is('program-head/account-management*') ? 'active' : '' }}"><a href="{{ url('/program-head/account-management') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>Account Management</a></li>
            @endif
            @if($navPermissions['announcements'] ?? true)
            <li class="{{ request()->is('program-head/announcements*') ? 'active' : '' }}"><a href="{{ url('/program-head/announcements') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>Announcements</a></li>
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

            <div class="page-header">
                <div>
                    <div class="page-title">Submissions and Deadline</div>
                    <div class="page-sub">Mga requirement para sa program: <strong>{{ $myProgram }}</strong> · naka-sort by pinakamalapit na deadline</div>
                </div>
                <button class="btn-add" onclick="openAddModal()">+ Add Requirement</button>
            </div>

            @forelse($requirements as $req)
                @php
                    $daysLeft = $req->days_left;
                    $submittedCount = $req->submissions->count();
                    $lateCount = $req->submissions->where('status', 'late')->count();
                    $pct = $facultyCount > 0 ? round(($submittedCount / $facultyCount) * 100) : 0;

                    if ($daysLeft < 0) { $cardClass = 'overdue'; $badgeClass = 'badge-overdue'; $badgeText = abs($daysLeft) . ' day(s) overdue'; }
                    elseif ($daysLeft <= 3) { $cardClass = 'soon'; $badgeClass = 'badge-soon'; $badgeText = $daysLeft == 0 ? 'Due today' : $daysLeft . ' day(s) left'; }
                    else { $cardClass = 'ok'; $badgeClass = 'badge-ok'; $badgeText = $daysLeft . ' day(s) left'; }
                @endphp
                <div class="req-card {{ $cardClass }}">
                    <div class="req-top">
                        <div>
                            <div class="req-title">{{ $req->title }}</div>
                            <div class="req-type">{{ ucwords(str_replace('_',' ',$req->type)) }} · Due {{ $req->deadline->format('M d, Y') }}</div>
                        </div>
                        <span class="req-deadline-badge {{ $badgeClass }}">{{ $badgeText }}</span>
                    </div>

                    @if($req->description)
                        <div class="req-desc">{{ $req->description }}</div>
                    @endif

                    <div class="req-progress">
                        <div class="progress-track"><div class="progress-fill" style="width: {{ $pct }}%;"></div></div>
                        <span class="progress-text">{{ $submittedCount }} / {{ $facultyCount }} submitted{{ $lateCount > 0 ? " ({$lateCount} late)" : '' }}</span>
                    </div>

                    <div class="req-actions">
                        <button class="btn-mini btn-mini-toggle" onclick="toggleSubmitters({{ $req->id }})">View Submitters</button>
                        <button class="btn-mini btn-mini-edit" onclick='editRequirement(@json($req))'>Edit</button>
                        <form method="POST" action="{{ url('/program-head/submissions/' . $req->id) }}" onsubmit="return confirm('Delete this requirement?')" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-mini btn-mini-del">Delete</button>
                        </form>
                    </div>

                    <div class="submitters-list" id="submitters-{{ $req->id }}">
                        @forelse($req->submissions as $sub)
                            <div class="submitter-row">
                                <span class="submitter-name">{{ $sub->faculty->name }}</span>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <a class="submitter-link" href="{{ Storage::url($sub->file_path) }}" target="_blank">View File</a>
                                    <span class="submitter-status status-{{ $sub->status }}">{{ ucfirst($sub->status) }}</span>
                                </div>
                            </div>
                        @empty
                            <div style="font-size:12px; color:#bbb;">Wala pang nakapagsumite.</div>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="empty-state">Wala pang submission requirements. Click "Add Requirement" para magsimula.</div>
            @endforelse
        </div>
    </div>

    <div class="modal-overlay" id="req-overlay">
        <div class="modal">
            <form id="req-form" method="POST" action="{{ url('/program-head/submissions') }}">
                @csrf
                <div class="modal-title" id="modal-title">Add Requirement</div>

                <div class="modal-field">
                    <label>Title <span style="color:#ef4444">*</span></label>
                    <input type="text" name="title" id="f-title" placeholder="e.g. Syllabus for AY 2026-2027" required>
                </div>

                <div class="modal-row">
                    <div class="modal-field">
                        <label>Type</label>
                        <select name="type" id="f-type">
                            <option value="syllabus">Syllabus</option>
                            <option value="lesson_plan">Lesson Plan</option>
                            <option value="tos">TOS</option>
                            <option value="exam_bank">Exam Bank</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="modal-field">
                        <label>Deadline <span style="color:#ef4444">*</span></label>
                        <input type="date" name="deadline" id="f-deadline" required>
                    </div>
                </div>

                <div class="modal-field">
                    <label>Description</label>
                    <textarea name="description" id="f-description" placeholder="Optional instructions..."></textarea>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-save">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const BASE = '{{ url('/program-head/submissions') }}';

        function toggleSubmitters(id) {
            document.getElementById('submitters-' + id).classList.toggle('open');
        }

        function openAddModal() {
            document.getElementById('modal-title').textContent = 'Add Requirement';
            document.getElementById('req-form').action = BASE;
            const m = document.getElementById('edit-method'); if (m) m.remove();
            document.getElementById('f-title').value = '';
            document.getElementById('f-deadline').value = '';
            document.getElementById('f-description').value = '';
            document.getElementById('f-type').selectedIndex = 0;
            document.getElementById('req-overlay').classList.add('open');
        }

        function editRequirement(req) {
            document.getElementById('modal-title').textContent = 'Edit Requirement';
            document.getElementById('req-form').action = BASE + '/' + req.id;
            let m = document.getElementById('edit-method');
            if (!m) {
                m = document.createElement('input');
                m.type = 'hidden'; m.name = '_method'; m.value = 'PUT'; m.id = 'edit-method';
                document.getElementById('req-form').appendChild(m);
            }
            document.getElementById('f-title').value = req.title;
            document.getElementById('f-deadline').value = req.deadline.split('T')[0];
            document.getElementById('f-description').value = req.description || '';
            document.getElementById('f-type').value = req.type;
            document.getElementById('req-overlay').classList.add('open');
        }

        function closeModal() { document.getElementById('req-overlay').classList.remove('open'); }
        document.getElementById('req-overlay').addEventListener('click', function(e) { if (e.target === this) closeModal(); });
    </script>

</body>
</html>