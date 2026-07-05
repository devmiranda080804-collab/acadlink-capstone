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
        .cal-nav-btn { background: none; border: 1px solid #e0e0e0; border-radius: 6px; width: 28px; height: 28px; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #555; transition: background 0.15s; }
        .cal-nav-btn:hover { background: #f0f4ff; color: #0f2557; }
        .cal-nav-btn svg { width: 14px; height: 14px; }
        .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; }
        .cal-day-header { text-align: center; font-size: 11px; font-weight: 700; color: #999; padding: 6px 0; }
        .cal-day { text-align: center; padding: 6px 4px; font-size: 12.5px; color: #333; border-radius: 6px; cursor: pointer; transition: background 0.12s; min-height: 34px; display: flex; align-items: center; justify-content: center; position: relative; }
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
        .event-tag.all { background: #f0f4ff; color: #0f2557; }
        .event-tag.faculty { background: #dcfce7; color: #166534; }
        .event-tag.exam { background: #fef3c7; color: #92400e; }

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
            <a href="{{ url('/secretary/dashboard') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
        </li>
        @endif
        @if($navPermissions['document-repository'] ?? true)
        <li class="{{ request()->is('secretary/document-repository*') ? 'active' : '' }}">
            <a href="{{ url('/secretary/document-repository') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                Document Repository
            </a>
        </li>
        @endif
        @if($navPermissions['template-distribution'] ?? true)
        <li class="{{ request()->is('secretary/template-distribution*') ? 'active' : '' }}">
            <a href="{{ url('/secretary/template-distribution') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
                Template Distribution
            </a>
        </li>
        @endif
        @if($navPermissions['course-filing'] ?? true)
        <li class="{{ request()->is('secretary/course-filing*') ? 'active' : '' }}">
            <a href="{{ url('/secretary/course-filing') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                Course Filing
            </a>
        </li>
        @endif
        @if($navPermissions['account-management'] ?? true)
        <li class="{{ request()->is('secretary/account-management*') ? 'active' : '' }}">
            <a href="{{ url('/secretary/account-management') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                Account Management
            </a>
        </li>
        @endif
        @if($navPermissions['announcements'] ?? true)
        <li class="{{ request()->is('secretary/announcements*') ? 'active' : '' }}">
            <a href="{{ url('/secretary/announcements') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                Announcements
            </a>
        </li>
        @endif
        @if($navPermissions['calendar'] ?? true)
        <li class="{{ request()->is('secretary/calendar*') ? 'active' : '' }}">
            <a href="{{ url('/secretary/calendar') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Calendar of Activities
            </a>
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
            <div class="page-header">
                <div class="page-title">Calendar of Activities</div>
                <div class="page-sub">Set by Secretary/Dean/Admin</div>
            </div>

            <div class="cal-layout">
                <div class="cal-panel">
                    <div class="cal-nav">
                        <button class="cal-nav-btn" onclick="prevMonth()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                        </button>
                        <span class="month-label" id="month-label"></span>
                        <button class="cal-nav-btn" onclick="nextMonth()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                        </button>
                    </div>
                    <div class="cal-grid" id="cal-grid">
                        <div class="cal-day-header">Sun</div>
                        <div class="cal-day-header">Mon</div>
                        <div class="cal-day-header">Tue</div>
                        <div class="cal-day-header">Wed</div>
                        <div class="cal-day-header">Thu</div>
                        <div class="cal-day-header">Fri</div>
                        <div class="cal-day-header">Sat</div>
                    </div>
                </div>

                <div class="events-panel">
                    <div class="events-title">Upcoming Activities</div>

                    <div class="event-item">
                        <div class="event-date"><div class="month">Jun</div><div class="day">5</div></div>
                        <div class="event-info">
                            <div class="event-name">Department Meeting – Business Admin</div>
                            <div class="event-meta">9:00 AM · Conference Room</div>
                            <span class="event-tag all">All Roles</span>
                        </div>
                    </div>
                    <div class="event-item">
                        <div class="event-date"><div class="month">Jun</div><div class="day">10</div></div>
                        <div class="event-info">
                            <div class="event-name">Midterm Examinations Begin</div>
                            <div class="event-meta">All Day · All Programs</div>
                            <span class="event-tag exam">Exam Period</span>
                        </div>
                    </div>
                    <div class="event-item">
                        <div class="event-date"><div class="month">Jun</div><div class="day">15</div></div>
                        <div class="event-info">
                            <div class="event-name">Faculty Development Workshop</div>
                            <div class="event-meta">8:00 AM – 5:00 PM · AVR</div>
                            <span class="event-tag faculty">Faculty Required</span>
                        </div>
                    </div>
                    <div class="event-item">
                        <div class="event-date"><div class="month">Jun</div><div class="day">20</div></div>
                        <div class="event-info">
                            <div class="event-name">Midterm Examinations End</div>
                            <div class="event-meta">All Day · All Programs</div>
                            <span class="event-tag exam">Exam Period</span>
                        </div>
                    </div>
                    <div class="event-item">
                        <div class="event-date"><div class="month">Jun</div><div class="day">30</div></div>
                        <div class="event-info">
                            <div class="event-name">Course Registration Deadline</div>
                            <div class="event-meta">5:00 PM · Online Portal</div>
                            <span class="event-tag all">All Roles</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var today = new Date();
        var currentYear = today.getFullYear();
        var currentMonth = today.getMonth();
        var eventDates = [5, 10, 15, 20, 30];
        var monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];

        function renderCalendar() {
            document.getElementById('month-label').textContent = monthNames[currentMonth] + ' ' + currentYear;
            var grid = document.getElementById('cal-grid');
            grid.querySelectorAll('.cal-day').forEach(function(c) { c.remove(); });

            var firstDay = new Date(currentYear, currentMonth, 1).getDay();
            var daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();

            for (var i = 0; i < firstDay; i++) {
                var empty = document.createElement('div');
                empty.className = 'cal-day empty';
                grid.appendChild(empty);
            }

            for (var d = 1; d <= daysInMonth; d++) {
                var cell = document.createElement('div');
                cell.className = 'cal-day';
                cell.textContent = d;

                var isToday = (d === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear());
                var hasEvent = eventDates.includes(d) && currentMonth === today.getMonth();

                if (isToday) cell.classList.add('today');
                if (hasEvent) {
                    cell.classList.add('has-event');
                    var dot = document.createElement('span');
                    dot.className = 'dot';
                    cell.appendChild(dot);
                }
                grid.appendChild(cell);
            }
        }

        function prevMonth() {
            currentMonth--;
            if (currentMonth < 0) { currentMonth = 11; currentYear--; }
            renderCalendar();
        }

        function nextMonth() {
            currentMonth++;
            if (currentMonth > 11) { currentMonth = 0; currentYear++; }
            renderCalendar();
        }

        renderCalendar();
    </script>

</body>
</html>