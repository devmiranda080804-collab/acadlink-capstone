<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        .page-header { margin-bottom: 24px; }
        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; }
        .page-sub { font-size: 12px; color: #888; margin-top: 3px; }

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
        .events-empty { text-align: center; padding: 30px; color: #bbb; font-size: 12.5px; }

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
            <div class="page-header">
                <div class="page-title">Calendar of Activities</div>
                <div class="page-sub">Set by Secretary/Dean/Admin</div>
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

    <script>
        const activities = @json($activities);
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
            const sorted = [...activities].sort((a,b) => new Date(a.date) - new Date(b.date));
            if (sorted.length === 0) {
                list.innerHTML = '<div class="events-empty">Wala pang activities na naka-schedule.</div>';
                return;
            }
            list.innerHTML = '';
            sorted.forEach(a => {
                const dateObj = new Date(a.date + 'T00:00:00');
                const item = document.createElement('div');
                item.className = 'event-item';
                item.innerHTML = `
                    <div class="event-date">
                        <div class="month">${monthShort[dateObj.getMonth()]}</div>
                        <div class="day">${dateObj.getDate()}</div>
                    </div>
                    <div class="event-info">
                        <div class="event-name">${escapeHtml(a.title)}</div>
                        <div class="event-meta">${a.location ? escapeHtml(a.location) : 'No location'}${a.description ? ' · ' + escapeHtml(a.description) : ''}</div>
                        <span class="event-tag ${a.category}">${escapeHtml(a.categoryLabel)}</span>
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

        renderCalendar();
        renderEvents();
    </script>

</body>
</html>