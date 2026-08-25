<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Calendar of Activities – CBMA System</title>
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
        .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 24px; }
        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; }
        .page-sub { font-size: 12px; color: #888; margin-top: 3px; }
        .btn-add-event { display: flex; align-items: center; gap: 6px; background: #0f2557; color: #fff; border: none; border-radius: 6px; font-size: 12.5px; font-weight: 600; padding: 9px 16px; cursor: pointer; transition: background 0.15s; }
        .btn-add-event:hover { background: #1a3a7a; }

        .cal-layout { display: flex; gap: 20px; align-items: flex-start; }
        .cal-panel { background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; padding: 20px; width: 360px; flex-shrink: 0; }
        .cal-nav { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; }
        .cal-nav .month-label { font-size: 14px; font-weight: 700; color: #1a1a2e; }
        .cal-nav-btn { background: none; border: 1px solid #e0e0e0; border-radius: 6px; width: 28px; height: 28px; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #555; }
        .cal-nav-btn:hover { background: #f0f4ff; color: #0f2557; }
        .cal-nav-btn svg { width: 14px; height: 14px; }
        .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; }
        .cal-day-header { text-align: center; font-size: 11px; font-weight: 700; color: #999; padding: 6px 0; }
        .cal-day { text-align: center; padding: 6px 4px; font-size: 12.5px; color: #333; border-radius: 6px; cursor: pointer; min-height: 34px; display: flex; align-items: center; justify-content: center; position: relative; }
        .cal-day:hover { background: #f0f4ff; }
        .cal-day.empty { cursor: default; }
        .cal-day.empty:hover { background: none; }
        .cal-day.today { background: #0f2557; color: #fff; font-weight: 700; }
        .cal-day.has-event { background: #dbeafe; color: #1d4ed8; font-weight: 600; }
        .cal-day.has-event.today { background: #0f2557; color: #fff; }
        .cal-day .dot { width: 4px; height: 4px; background: #1d4ed8; border-radius: 50%; position: absolute; bottom: 3px; left: 50%; transform: translateX(-50%); }
        .cal-day.today .dot { background: #fff; }

        .events-panel { flex: 1; background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; padding: 20px; }
        .events-title { font-size: 14px; font-weight: 700; color: #1a1a2e; margin-bottom: 16px; }
        .event-item { display: flex; align-items: flex-start; gap: 14px; padding: 12px 0; border-bottom: 1px solid #f5f5f5; }
        .event-item:last-child { border-bottom: none; }
        .event-date { background: #dbeafe; color: #1d4ed8; border-radius: 8px; padding: 6px 10px; text-align: center; flex-shrink: 0; min-width: 48px; }
        .event-date .month { font-size: 10px; font-weight: 700; text-transform: uppercase; }
        .event-date .day { font-size: 16px; font-weight: 700; line-height: 1.2; }
        .event-info { flex: 1; }
        .event-name { font-size: 13px; font-weight: 600; color: #1a1a2e; margin-bottom: 3px; }
        .event-meta { font-size: 11px; color: #999; }
        .event-tag { display: inline-block; font-size: 10px; font-weight: 600; padding: 2px 7px; border-radius: 10px; margin-top: 4px; }
        .event-tag.general { background: #f0f4ff; color: #0f2557; }
        .event-tag.faculty { background: #dcfce7; color: #166534; }
        .event-tag.exam { background: #fef3c7; color: #92400e; }
        .event-tag.holiday { background: #fee2e2; color: #991b1b; }
        .event-actions { display: flex; gap: 4px; margin-top: 6px; }
        .btn-mini { font-size: 10px; font-weight: 600; padding: 3px 8px; border-radius: 4px; cursor: pointer; border: 1px solid; background: #fff; }
        .btn-mini-edit { color: #0f2557; border-color: #cdd7ec; }
        .btn-mini-edit:hover { background: #f0f4ff; }
        .btn-mini-del { color: #ef4444; border-color: #fca5a5; }
        .btn-mini-del:hover { background: #fee2e2; }
        .events-empty { text-align: center; padding: 30px; color: #bbb; font-size: 12.5px; }

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
            <li class="{{ request()->is('admin/dashboard') ? 'active' : '' }}"><a href="{{ url('/admin/dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>Dashboard</a></li>
            @endif
            @if($navPermissions['account-management'] ?? true)
            <li class="{{ request()->is('admin/account-management*') ? 'active' : '' }}"><a href="{{ url('/admin/account-management') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>Account Management</a></li>
            @endif
            @if($navPermissions['roles-permissions'] ?? true)
            <li class="{{ request()->is('admin/roles-permissions*') ? 'active' : '' }}"><a href="{{ url('/admin/roles-permissions') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>Roles & Permissions</a></li>
            @endif
            @if($navPermissions['template-approvals'] ?? true)
            <li class="{{ request()->is('admin/template-approvals*') ? 'active' : '' }}"><a href="{{ url('/admin/template-approvals') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>Template Approvals</a></li>
            @endif
            @if($navPermissions['course-assignment'] ?? true)
            <li class="{{ request()->is('admin/course-assignment*') ? 'active' : '' }}"><a href="{{ url('/admin/course-assignment') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>Course Assignment</a></li>
            @endif
            @if($navPermissions['audit-logs'] ?? true)
            <li class="{{ request()->is('admin/audit-logs*') ? 'active' : '' }}"><a href="{{ url('/admin/audit-logs') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>Audit Logs</a></li>
            @endif
            @if($navPermissions['announcements'] ?? true)
            <li class="{{ request()->is('admin/announcements*') ? 'active' : '' }}"><a href="{{ url('/admin/announcements') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>Announcements</a></li>
            @endif
            @if($navPermissions['calendar'] ?? true)
            <li class="{{ request()->is('admin/calendar*') ? 'active' : '' }}"><a href="{{ url('/admin/calendar') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Calendar of Activities</a></li>
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
            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif

            <div class="page-header">
                <div>
                    <div class="page-title">Calendar of Activities</div>
                    <div class="page-sub">Set by Secretary/Dean/Admin</div>
                </div>
                <button class="btn-add-event" onclick="openAddModal()">
                    <svg style="width:13px;height:13px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Add Activity
                </button>
            </div>

            <div class="cal-layout">
                <div class="cal-panel">
                    <div class="cal-nav">
                        <button class="cal-nav-btn" onclick="prevMonth()"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></button>
                        <span class="month-label" id="month-label"></span>
                        <button class="cal-nav-btn" onclick="nextMonth()"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></button>
                    </div>
                    <div class="cal-grid" id="cal-grid">
                        <div class="cal-day-header">Sun</div><div class="cal-day-header">Mon</div><div class="cal-day-header">Tue</div><div class="cal-day-header">Wed</div><div class="cal-day-header">Thu</div><div class="cal-day-header">Fri</div><div class="cal-day-header">Sat</div>
                    </div>
                </div>

                <div class="events-panel">
                    <div class="events-title">Upcoming Activities</div>
                    <div id="events-list"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add/Edit Modal --}}
    <div class="modal-overlay" id="event-overlay">
        <div class="modal">
            <form id="event-form" method="POST" action="{{ url('/admin/calendar') }}">
                @csrf
                <div class="modal-title" id="modal-title">Add Activity</div>

                <div class="modal-field">
                    <label>Title <span style="color:#ef4444">*</span></label>
                    <input type="text" name="title" id="f-title" placeholder="e.g. Midterm Examinations" required>
                </div>

                <div class="modal-row">
                    <div class="modal-field">
                        <label>Date <span style="color:#ef4444">*</span></label>
                        <input type="date" name="activity_date" id="f-date" required>
                    </div>
                    <div class="modal-field">
                        <label>Category</label>
                        <select name="category" id="f-category">
                            <option value="general">All Roles</option>
                            <option value="exam">Exam Period</option>
                            <option value="faculty">Faculty Required</option>
                            <option value="holiday">Holiday</option>
                        </select>
                    </div>
                </div>

                <div class="modal-field">
                    <label>Location</label>
                    <input type="text" name="location" id="f-location" placeholder="e.g. Conference Room">
                </div>

                <div class="modal-field">
                    <label>Description</label>
                    <textarea name="description" id="f-description" placeholder="Optional details..."></textarea>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-save">Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Hidden delete form --}}
    <form id="delete-form" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        const activities = @json($activities);
        const canManage = true; // Admin
        const BASE = '{{ url('/admin/calendar') }}';

        let today = new Date();
        let currentYear = today.getFullYear();
        let currentMonth = today.getMonth();
        const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        const monthShort = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

        function renderCalendar() {
            document.getElementById('month-label').textContent = monthNames[currentMonth] + ' ' + currentYear;
            const grid = document.getElementById('cal-grid');
            grid.querySelectorAll('.cal-day').forEach(c => c.remove());

            const firstDay = new Date(currentYear, currentMonth, 1).getDay();
            const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();

            for (let i = 0; i < firstDay; i++) {
                const empty = document.createElement('div');
                empty.className = 'cal-day empty';
                grid.appendChild(empty);
            }

            for (let d = 1; d <= daysInMonth; d++) {
                const cell = document.createElement('div');
                cell.className = 'cal-day';
                cell.textContent = d;
                const dateStr = currentYear + '-' + String(currentMonth+1).padStart(2,'0') + '-' + String(d).padStart(2,'0');
                const isToday = (d === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear());
                const hasEvent = activities.some(a => a.date === dateStr);
                if (isToday) cell.classList.add('today');
                if (hasEvent) {
                    cell.classList.add('has-event');
                    const dot = document.createElement('span');
                    dot.className = 'dot';
                    cell.appendChild(dot);
                }
                grid.appendChild(cell);
            }
        }

        function renderEvents() {
            const list = document.getElementById('events-list');
            // Sort by date, upcoming muna
            const sorted = [...activities].sort((a,b) => new Date(a.date) - new Date(b.date));

            if (sorted.length === 0) {
                list.innerHTML = '<div class="events-empty">Wala pang activities. Click "Add Activity" para magdagdag.</div>';
                return;
            }

            list.innerHTML = '';
            sorted.forEach(a => {
                const dateObj = new Date(a.date + 'T00:00:00');
                const item = document.createElement('div');
                item.className = 'event-item';
                let actions = '';
                if (canManage) {
                    actions = `<div class="event-actions">
                        <button class="btn-mini btn-mini-edit" onclick='editActivity(${JSON.stringify(a)})'>Edit</button>
                        <button class="btn-mini btn-mini-del" onclick="deleteActivity(${a.id})">Delete</button>
                    </div>`;
                }
                item.innerHTML = `
                    <div class="event-date">
                        <div class="month">${monthShort[dateObj.getMonth()]}</div>
                        <div class="day">${dateObj.getDate()}</div>
                    </div>
                    <div class="event-info">
                        <div class="event-name">${escapeHtml(a.title)}</div>
                        <div class="event-meta">${a.location ? escapeHtml(a.location) : 'No location'}${a.description ? ' · ' + escapeHtml(a.description) : ''}</div>
                        <span class="event-tag ${a.category}">${escapeHtml(a.categoryLabel)}</span>
                        ${actions}
                    </div>`;
                list.appendChild(item);
            });
        }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str == null ? '' : str;
            return div.innerHTML;
        }

        function prevMonth() { currentMonth--; if (currentMonth < 0) { currentMonth = 11; currentYear--; } renderCalendar(); }
        function nextMonth() { currentMonth++; if (currentMonth > 11) { currentMonth = 0; currentYear++; } renderCalendar(); }

        function openAddModal() {
            document.getElementById('modal-title').textContent = 'Add Activity';
            document.getElementById('event-form').action = BASE;
            const m = document.getElementById('edit-method'); if (m) m.remove();
            document.getElementById('f-title').value = '';
            document.getElementById('f-date').value = '';
            document.getElementById('f-location').value = '';
            document.getElementById('f-description').value = '';
            document.getElementById('f-category').selectedIndex = 0;
            document.getElementById('event-overlay').classList.add('open');
        }

        function editActivity(a) {
            document.getElementById('modal-title').textContent = 'Edit Activity';
            document.getElementById('event-form').action = BASE + '/' + a.id;
            let m = document.getElementById('edit-method');
            if (!m) {
                m = document.createElement('input');
                m.type = 'hidden'; m.name = '_method'; m.value = 'PUT'; m.id = 'edit-method';
                document.getElementById('event-form').appendChild(m);
            }
            document.getElementById('f-title').value = a.title;
            document.getElementById('f-date').value = a.date;
            document.getElementById('f-location').value = a.location || '';
            document.getElementById('f-description').value = a.description || '';
            document.getElementById('f-category').value = a.category;
            document.getElementById('event-overlay').classList.add('open');
        }

        function deleteActivity(id) {
            if (!confirm('Delete this activity?')) return;
            const form = document.getElementById('delete-form');
            form.action = BASE + '/' + id;
            form.submit();
        }

        function closeModal() { document.getElementById('event-overlay').classList.remove('open'); }
        document.getElementById('event-overlay').addEventListener('click', function(e) { if (e.target === this) closeModal(); });

        renderCalendar();
        renderEvents();
    </script>

</body>
</html>