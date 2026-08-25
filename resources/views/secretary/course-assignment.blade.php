<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Assignment – CBMA System</title>
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

        .filters-row { display: flex; gap: 10px; margin-bottom: 18px; flex-wrap: wrap; }
        .filter-select { height: 36px; padding: 0 32px 0 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 12.5px; color: #333; background: #fff; outline: none; appearance: none; -webkit-appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%23666' d='M5 7L0 2h10z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; cursor: pointer; min-width: 160px; }
        .filter-select:focus { border-color: #0f2557; }

        .assign-panel { background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; overflow: hidden; }
        .assign-table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
        .assign-table thead tr { background: #fafafa; border-bottom: 1px solid #eee; }
        .assign-table th { padding: 12px 16px; text-align: left; font-size: 11.5px; font-weight: 700; color: #666; }
        .assign-table td { padding: 13px 16px; border-bottom: 1px solid #f5f5f5; color: #333; vertical-align: middle; }
        .assign-table tbody tr:last-child td { border-bottom: none; }
        .assign-table td.empty-row { text-align: center; color: #999; padding: 40px 16px; }

        .course-code { font-weight: 700; color: #0f2557; }
        .program-tag { display: inline-block; font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 10px; background: #f0f4ff; color: #0f2557; }

        .faculty-chips { display: flex; flex-wrap: wrap; gap: 6px; }
        .faculty-chip { display: inline-flex; align-items: center; gap: 6px; background: #eef2ff; color: #0f2557; font-size: 11px; font-weight: 600; padding: 4px 6px 4px 10px; border-radius: 14px; }
        .chip-remove { background: none; border: none; color: #6b7280; cursor: pointer; padding: 0; display: flex; }
        .chip-remove:hover { color: #ef4444; }
        .chip-remove svg { width: 11px; height: 11px; }
        .no-faculty { color: #bbb; font-size: 11.5px; font-style: italic; }

        .btn-assign { display: inline-flex; align-items: center; gap: 4px; background: #0f2557; color: #fff; border: none; font-size: 11px; font-weight: 600; padding: 6px 12px; border-radius: 5px; cursor: pointer; white-space: nowrap; }
        .btn-assign:hover { background: #1a3a7a; }

        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 999; align-items: center; justify-content: center; }
        .modal-overlay.open { display: flex; }
        .modal { background: #fff; border-radius: 10px; padding: 24px 26px; width: 420px; max-width: 95vw; box-shadow: 0 8px 32px rgba(0,0,0,0.25); }
        .modal-title { font-size: 15px; font-weight: 700; color: #1a1a2e; margin-bottom: 4px; }
        .modal-sub { font-size: 11.5px; color: #888; margin-bottom: 16px; }
        .modal-field { margin-bottom: 13px; }
        .modal-field label { display: block; font-size: 11.5px; font-weight: 700; color: #333; margin-bottom: 4px; }
        .modal-field select { width: 100%; padding: 8px 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 12.5px; color: #333; outline: none; appearance: none; -webkit-appearance: none; background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%23666' d='M5 7L0 2h10z'/%3E%3C/svg%3E") no-repeat right 10px center; cursor: pointer; }
        .modal-field select:focus { border-color: #0f2557; }
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
            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif

            <div class="page-title">Course Assignment</div>
            <div class="page-sub">I-assign ang faculty sa mga courses per school year at semester</div>

            <div class="filters-row">
                <form method="GET" style="display:flex; gap:10px;">
                    <select name="program" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Programs</option>
                        <option value="FMAD" {{ $program == 'FMAD' ? 'selected' : '' }}>FMAD</option>
                        <option value="OFD" {{ $program == 'OFD' ? 'selected' : '' }}>OFD</option>
                        <option value="BAD" {{ $program == 'BAD' ? 'selected' : '' }}>BAD</option>
                    </select>
                    <select name="school_year" class="filter-select" onchange="this.form.submit()">
                        @foreach([$schoolYear, ($schoolYear != '2025-2026' ? '2025-2026' : '2026-2027')] as $sy)
                            <option value="{{ $sy }}" {{ $schoolYear == $sy ? 'selected' : '' }}>{{ $sy }}</option>
                        @endforeach
                    </select>
                    <select name="semester" class="filter-select" onchange="this.form.submit()">
                        <option value="First Semester" {{ $semester == 'First Semester' ? 'selected' : '' }}>First Semester</option>
                        <option value="Second Semester" {{ $semester == 'Second Semester' ? 'selected' : '' }}>Second Semester</option>
                        <option value="Summer" {{ $semester == 'Summer' ? 'selected' : '' }}>Summer</option>
                    </select>
                </form>
            </div>

            <div class="assign-panel">
                <table class="assign-table">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Program</th>
                            <th>Assigned Faculty</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                            @php $courseAssignments = $assignments->get($course->id, collect()); @endphp
                            <tr>
                                <td><span class="course-code">{{ $course->code }}</span></td>
                                <td>{{ $course->title }}</td>
                                <td><span class="program-tag">{{ $course->program }}</span></td>
                                <td>
                                    <div class="faculty-chips">
                                        @forelse($courseAssignments as $a)
                                            <span class="faculty-chip">
                                                {{ $a->faculty->name }}
                                                <form method="POST" action="{{ url('/secretary/course-assignment/' . $a->id) }}" onsubmit="return confirm('Remove this assignment?')" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="chip-remove" title="Remove">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                                    </button>
                                                </form>
                                            </span>
                                        @empty
                                            <span class="no-faculty">Walang naka-assign</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td>
                                    <button class="btn-assign" onclick='openAssignModal({{ $course->id }}, "{{ addslashes($course->code) }}")'>+ Assign</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="empty-row">Walang courses na nakita.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="assign-overlay">
        <div class="modal">
            <form method="POST" action="{{ url('/secretary/course-assignment') }}">
                @csrf
                <input type="hidden" name="course_id" id="f-course-id">
                <input type="hidden" name="school_year" value="{{ $schoolYear }}">
                <input type="hidden" name="semester" value="{{ $semester }}">

                <div class="modal-title">Assign Faculty</div>
                <div class="modal-sub" id="modal-course-label">—</div>

                <div class="modal-field">
                    <label>Faculty <span style="color:#ef4444">*</span></label>
                    <select name="faculty_id" required>
                        <option value="">Select faculty...</option>
                        @foreach($facultyList as $f)
                            <option value="{{ $f->id }}">{{ $f->name }} ({{ $f->program }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeAssignModal()">Cancel</button>
                    <button type="submit" class="btn-save">Assign</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAssignModal(courseId, courseCode) {
            document.getElementById('f-course-id').value = courseId;
            document.getElementById('modal-course-label').textContent = 'Course: ' + courseCode + ' — ' + '{{ $schoolYear }} · {{ $semester }}';
            document.getElementById('assign-overlay').classList.add('open');
        }
        function closeAssignModal() { document.getElementById('assign-overlay').classList.remove('open'); }
        document.getElementById('assign-overlay').addEventListener('click', function(e) { if (e.target === this) closeAssignModal(); });
    </script>

</body>
</html>