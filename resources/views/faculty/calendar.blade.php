<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar of Activities – CBMA System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* ═══════════════════ SIDEBAR ═══════════════════ */
        .sidebar {
            width: 210px;
            background-color: #0f2557;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            overflow-y: auto;
        }

        .sidebar-logo {
            display: flex; flex-direction: column; align-items: center;
            padding: 22px 16px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-logo img { width: 72px; height: 72px; border-radius: 50%; object-fit: contain; background: #1b3d7a; }
        .sidebar-logo .brand { color: #fff; font-size: 15px; font-weight: 700; margin-top: 8px; }
        .sidebar-logo .brand-sub { color: #a0b4d6; font-size: 10px; margin-top: 2px; }

        .nav-list { list-style: none; padding: 10px 0; flex: 1; }

        .nav-list li a {
            display: flex; align-items: center; gap: 11px;
            padding: 11px 20px;
            color: #c8d6ec; text-decoration: none; font-size: 13px;
            transition: background 0.15s, color 0.15s;
        }

        .nav-list li a:hover { background-color: rgba(255,255,255,0.08); color: #fff; }

        .nav-list li.active a {
            background-color: rgba(255,255,255,0.08);
            color: #fff; border-left: 3px solid #fff;
        }

        .nav-list li a svg { width: 18px; height: 18px; flex-shrink: 0; opacity: 0.85; }

        .sidebar-logout { padding: 12px 0; border-top: 1px solid rgba(255,255,255,0.1); }

        .sidebar-logout a {
            display: flex; align-items: center; gap: 11px;
            padding: 11px 20px;
            color: #c8d6ec; text-decoration: none; font-size: 13px;
            transition: background 0.15s;
        }

        .sidebar-logout a:hover { background-color: rgba(255,255,255,0.08); color: #fff; }

        /* ═══════════════════ MAIN ═══════════════════ */
        .main { flex: 1; display: flex; flex-direction: column; overflow: hidden; background-color: #f5f6fa; }

        .topnav {
            background: #fff; border-bottom: 1px solid #e0e0e0;
            padding: 0 24px; height: 52px;
            display: flex; align-items: center; justify-content: space-between;
            flex-shrink: 0;
        }

        .topnav .label { font-size: 12px; color: #666; }
        .topnav-right { display: flex; align-items: center; gap: 12px; }

        .role-badge { background-color: #0f2557; color: #fff; font-size: 12px; font-weight: 600; padding: 5px 16px; border-radius: 20px; }

        .user-info { display: flex; align-items: center; gap: 8px; }
        .user-text { text-align: right; }
        .user-name { font-size: 12px; font-weight: 600; color: #222; }
        .user-email { font-size: 10px; color: #888; }

        .user-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background-color: #0f2557; color: #fff;
            font-size: 12px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
        }

        /* ═══════════════════ CONTENT ═══════════════════ */
        .content { flex: 1; overflow-y: auto; padding: 24px 28px 28px; }

        .page-header {
            display: flex; align-items: flex-start; justify-content: space-between;
            margin-bottom: 22px;
        }

        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 3px; }
        .page-sub { font-size: 11.5px; color: #888; }

        .btn-add-activity {
            display: flex; align-items: center; gap: 6px;
            background: #0f2557; color: #fff;
            border: none; border-radius: 6px;
            font-size: 12.5px; font-weight: 600;
            padding: 9px 18px; cursor: pointer;
            transition: background 0.15s;
            white-space: nowrap;
        }

        .btn-add-activity:hover { background: #1a3a7a; }

        /* ── Two-column layout ── */
        .cal-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            align-items: start;
        }

        /* ── Calendar panel ── */
        .cal-panel {
            background: #fff;
            border: 1px solid #e4e4e4;
            border-radius: 8px;
            padding: 18px 20px;
        }

        .cal-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 16px;
        }

        .cal-nav-btn {
            background: none; border: none;
            color: #555; font-size: 16px;
            cursor: pointer; padding: 2px 8px;
            border-radius: 4px;
            transition: background 0.15s;
            line-height: 1;
        }

        .cal-nav-btn:hover { background: #f0f0f0; }

        .cal-month-label {
            font-size: 14px; font-weight: 700;
            color: #1a1a2e;
        }

        /* Day-of-week row */
        .cal-dow {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            margin-bottom: 6px;
        }

        .cal-dow span {
            text-align: center;
            font-size: 11px;
            font-weight: 600;
            color: #aaa;
            padding: 4px 0;
        }

        /* Days grid */
        .cal-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
        }

        .cal-day {
            aspect-ratio: 1;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px;
            color: #333;
            border-radius: 6px;
            cursor: pointer;
            border: 1px solid transparent;
            transition: background 0.12s, border-color 0.12s;
            user-select: none;
        }

        .cal-day:hover { background: #f0f4ff; border-color: #c0cfff; }

        .cal-day.empty { cursor: default; }
        .cal-day.empty:hover { background: none; border-color: transparent; }

        /* Today */
        .cal-day.today {
            background: #bfdbfe;
            color: #1e3a8a;
            font-weight: 700;
            border-color: #93c5fd;
        }

        /* Has event */
        .cal-day.has-event {
            background: #cce5ff;
            color: #1e40af;
            font-weight: 600;
            border-color: #93c5fd;
        }

        /* Selected */
        .cal-day.selected {
            background: #0f2557;
            color: #fff;
            font-weight: 700;
        }

        /* ── Upcoming Activities panel ── */
        .upcoming-panel {
            background: #fff;
            border: 1px solid #e4e4e4;
            border-radius: 8px;
            padding: 16px 18px;
        }

        .upcoming-title {
            font-size: 13px; font-weight: 700;
            color: #1a1a2e; margin-bottom: 14px;
        }

        .activity-item {
            display: flex; gap: 12px; align-items: flex-start;
            padding: 10px 0;
            border-bottom: 1px solid #f5f5f5;
        }

        .activity-item:last-child { border-bottom: none; }

        .activity-date-badge {
            display: flex; flex-direction: column; align-items: center;
            background: #bfdbfe;
            color: #1e3a8a;
            border-radius: 6px;
            padding: 4px 8px;
            min-width: 40px;
            flex-shrink: 0;
        }

        .activity-date-badge .month {
            font-size: 9px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .activity-date-badge .day {
            font-size: 14px; font-weight: 700; line-height: 1.2;
        }

        .activity-info {}

        .activity-name {
            font-size: 12.5px; font-weight: 600;
            color: #1a1a2e; margin-bottom: 2px;
        }

        .activity-tag {
            font-size: 10px;
            color: #aaa;
        }

        .upcoming-empty {
            text-align: center; padding: 30px 10px;
            color: #bbb; font-size: 12.5px;
        }

        svg { display: inline-block; vertical-align: middle; }

        /* ── Modal overlay ── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 999;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.open { display: flex; }

        .modal {
            background: #fff;
            border-radius: 10px;
            padding: 24px 26px;
            width: 420px;
            max-width: 95vw;
            box-shadow: 0 8px 32px rgba(0,0,0,0.25);
            position: relative;
        }

        .modal-title {
            font-size: 15px; font-weight: 700;
            color: #1a1a2e; margin-bottom: 18px;
        }

        .modal-field { margin-bottom: 13px; }

        .modal-field label {
            display: block;
            font-size: 11.5px; font-weight: 700;
            color: #333; margin-bottom: 4px;
        }

        .modal-field input,
        .modal-field select,
        .modal-field textarea {
            width: 100%;
            padding: 7px 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 12.5px;
            color: #333;
            outline: none;
            font-family: Arial, sans-serif;
            transition: border-color 0.15s;
        }

        .modal-field input:focus,
        .modal-field select:focus,
        .modal-field textarea:focus { border-color: #0f2557; }

        .modal-field textarea { resize: vertical; min-height: 70px; }

        .modal-field select {
            appearance: none; -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%23666' d='M5 7L0 2h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-color: #fff;
            cursor: pointer;
        }

        .modal-field input.invalid { border-color: #ef4444; }

        .modal-error {
            display: none;
            background: #fee2e2; border: 1px solid #fca5a5;
            color: #b91c1c; font-size: 11px;
            padding: 6px 10px; border-radius: 4px;
            margin-bottom: 12px;
        }

        .modal-actions {
            display: flex; gap: 8px; justify-content: flex-end;
            margin-top: 18px;
        }

        .btn-cancel {
            background: #fff; border: 1px solid #ccc;
            color: #444; font-size: 12.5px; font-weight: 600;
            padding: 8px 18px; border-radius: 5px;
            cursor: pointer; transition: background 0.15s;
        }

        .btn-cancel:hover { background: #f5f5f5; }

        .btn-save-modal {
            background: #0f2557; color: #fff;
            border: none; font-size: 12.5px; font-weight: 600;
            padding: 8px 20px; border-radius: 5px;
            cursor: pointer; transition: background 0.15s;
        }

        .btn-save-modal:hover { background: #1a3a7a; }
    </style>
</head>
<body>

    {{-- ════════════ SIDEBAR ════════════ --}}
    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="{{ asset('images/cbma-logo.png') }}" alt="CBMA Logo">
            <span class="brand">CBMA</span>
            <span class="brand-sub">Academic Coordination</span>
        </div>

        <ul class="nav-list">
            <li><a href="{{ url('/faculty/dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>Dashboard</a></li>
            <li><a href="{{ url('/faculty/my-template') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>My Template</a></li>
            <li><a href="{{ url('/faculty/exam-generator') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 7h8M8 12h8M8 17h4"/></svg>Exam Generator</a></li>
            <li><a href="{{ url('/faculty/shared-library') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>Shared Library</a></li>
            <li><a href="{{ url('/faculty/course-coordination') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20"/></svg>Course Coordination</a></li>
            <li><a href="{{ url('/faculty/analytics') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>Analytics</a></li>
            <li><a href="{{ url('/faculty/user-manuals') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>User manuals</a></li>
            <li class="active"><a href="{{ url('/faculty/calendar') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Calendar Of Activities</a></li>
            <li><a href="{{ url('/faculty/announcements') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>Announcements</a></li>
            <li><a href="{{ url('/faculty/submissions') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Submissions and Deadline</a></li>
            <li><a href="{{ url('/faculty/cms') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 010 14.14M4.93 4.93a10 10 0 000 14.14"/></svg>CMS</a></li>
        </ul>

        <div class="sidebar-logout">
            <a href="#" onclick="handleLogout()">
                <svg style="width:14px;height:14px;flex-shrink:0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Log out
            </a>
        </div>
    </aside>

    {{-- ════════════ MAIN ════════════ --}}
    <div class="main">

        <div class="topnav">
            <span class="label">Current role</span>
            <div class="topnav-right">
                <span class="role-badge">Faculty</span>
                <div class="user-info">
                    <div class="user-text">
                        <div class="user-name">—</div>
                        <div class="user-email">—</div>
                    </div>
                    <div class="user-avatar">—</div>
                </div>
            </div>
        </div>

        <div class="content">

            <div class="page-header">
                <div>
                    <div class="page-title">Calendar of Activities</div>
                    <div class="page-sub">Set by Secretary/Dean/Admin</div>
                </div>
                <button class="btn-add-activity" type="button" onclick="addActivity()">+ Add Activity</button>
            </div>

            <div class="cal-layout">

                {{-- Calendar --}}
                <div class="cal-panel">
                    <div class="cal-header">
                        <button class="cal-nav-btn" onclick="prevMonth()">&#8249;</button>
                        <span class="cal-month-label" id="cal-month-label">May 2026</span>
                        <button class="cal-nav-btn" onclick="nextMonth()">&#8250;</button>
                    </div>

                    <div class="cal-dow">
                        <span>Sun</span><span>Mon</span><span>Tue</span>
                        <span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
                    </div>

                    <div class="cal-grid" id="cal-grid"></div>
                </div>

                {{-- Upcoming Activities --}}
                <div class="upcoming-panel">
                    <div class="upcoming-title">Upcoming Activities</div>
                    <div id="upcoming-list">
                        <div class="upcoming-empty">No upcoming activities.</div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- ════════════ ADD ACTIVITY MODAL ════════════ --}}
    <div class="modal-overlay" id="modal-overlay">
        <div class="modal">
            <div class="modal-title">Add Activity</div>

            <div class="modal-error" id="modal-error"></div>

            <div class="modal-field">
                <label for="act-name">Activity Name <span style="color:#ef4444;">*</span></label>
                <input type="text" id="act-name" placeholder="e.g. Department Meeting">
            </div>

            <div class="modal-field">
                <label for="act-date">Date <span style="color:#ef4444;">*</span></label>
                <input type="date" id="act-date">
            </div>

            <div class="modal-field">
                <label for="act-tag">Tag</label>
                <select id="act-tag">
                    <option value="Set by Admin">Set by Admin</option>
                    <option value="Set by Faculty">Set by Faculty</option>
                    <option value="Set by HR Department">Set by HR Department</option>
                    <option value="Set by Dean">Set by Dean</option>
                    <option value="Set by Secretary">Set by Secretary</option>
                </select>
            </div>

            <div class="modal-field">
                <label for="act-desc">Description (optional)</label>
                <textarea id="act-desc" placeholder="Add a short description..."></textarea>
            </div>

            <div class="modal-actions">
                <button class="btn-cancel" type="button" onclick="closeModal()">Cancel</button>
                <button class="btn-save-modal" type="button" onclick="saveActivity()">Save Activity</button>
            </div>
        </div>
    </div>

    <script>
        var currentYear  = 2026;
        var currentMonth = 4; // 0-indexed: 4 = May

        // Activities stored in memory (frontend only)
        var activities = [];

        var MONTHS = ['January','February','March','April','May','June',
                      'July','August','September','October','November','December'];
        var SHORT_MONTHS = ['Jan','Feb','Mar','Apr','May','Jun',
                            'Jul','Aug','Sep','Oct','Nov','Dec'];

        function renderCalendar() {
            var label = document.getElementById('cal-month-label');
            label.textContent = MONTHS[currentMonth] + ' ' + currentYear;

            var grid = document.getElementById('cal-grid');
            grid.innerHTML = '';

            var firstDay    = new Date(currentYear, currentMonth, 1).getDay();
            var daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();

            var today = new Date();
            var isCurrentMonth = (today.getFullYear() === currentYear && today.getMonth() === currentMonth);

            var eventDays = new Set(
                activities
                    .filter(function(a) { return a.year === currentYear && a.month === currentMonth; })
                    .map(function(a) { return a.day; })
            );

            for (var i = 0; i < firstDay; i++) {
                var empty = document.createElement('div');
                empty.className = 'cal-day empty';
                grid.appendChild(empty);
            }

            for (var d = 1; d <= daysInMonth; d++) {
                var cell = document.createElement('div');
                cell.className = 'cal-day';
                cell.textContent = d;
                cell.dataset.day = d;

                if (isCurrentMonth && d === today.getDate()) cell.classList.add('today');
                if (eventDays.has(d)) cell.classList.add('has-event');

                cell.addEventListener('click', function() {
                    document.querySelectorAll('.cal-day.selected').forEach(function(el) {
                        el.classList.remove('selected');
                    });
                    this.classList.add('selected');
                    // Pre-fill date in modal with clicked date
                    var y = currentYear;
                    var m = String(currentMonth + 1).padStart(2, '0');
                    var dd = String(this.dataset.day).padStart(2, '0');
                    document.getElementById('act-date').value = y + '-' + m + '-' + dd;
                });

                grid.appendChild(cell);
            }
        }

        function renderUpcoming() {
            var list = document.getElementById('upcoming-list');

            if (activities.length === 0) {
                list.innerHTML = '<div class="upcoming-empty">No upcoming activities.</div>';
                return;
            }

            // Sort by date
            var sorted = activities.slice().sort(function(a, b) {
                return new Date(a.year, a.month, a.day) - new Date(b.year, b.month, b.day);
            });

            list.innerHTML = '';
            sorted.forEach(function(a) {
                var item = document.createElement('div');
                item.className = 'activity-item';
                item.innerHTML =
                    '<div class="activity-date-badge">' +
                        '<span class="month">' + SHORT_MONTHS[a.month] + '</span>' +
                        '<span class="day">' + a.day + '</span>' +
                    '</div>' +
                    '<div class="activity-info">' +
                        '<div class="activity-name">' + a.name + '</div>' +
                        '<div class="activity-tag">' + a.tag + '</div>' +
                    '</div>';
                list.appendChild(item);
            });
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

        // ── Modal ──
        function addActivity() {
            // Pre-fill today's date
            var today = new Date();
            var y = today.getFullYear();
            var m = String(today.getMonth() + 1).padStart(2, '0');
            var d = String(today.getDate()).padStart(2, '0');
            document.getElementById('act-date').value = y + '-' + m + '-' + d;
            document.getElementById('act-name').value = '';
            document.getElementById('act-desc').value = '';
            document.getElementById('act-tag').selectedIndex = 0;
            document.getElementById('act-name').classList.remove('invalid');
            document.getElementById('act-date').classList.remove('invalid');
            document.getElementById('modal-error').style.display = 'none';
            document.getElementById('modal-overlay').classList.add('open');
        }

        function closeModal() {
            document.getElementById('modal-overlay').classList.remove('open');
        }

        // Close modal on overlay click
        document.getElementById('modal-overlay').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        function saveActivity() {
            var nameInput = document.getElementById('act-name');
            var dateInput = document.getElementById('act-date');
            var tagInput  = document.getElementById('act-tag');
            var errorDiv  = document.getElementById('modal-error');

            nameInput.classList.remove('invalid');
            dateInput.classList.remove('invalid');
            errorDiv.style.display = 'none';

            var name = nameInput.value.trim();
            var date = dateInput.value;
            var tag  = tagInput.value;

            if (!name && !date) {
                errorDiv.textContent = 'Please enter an activity name and date.';
                errorDiv.style.display = 'block';
                nameInput.classList.add('invalid');
                dateInput.classList.add('invalid');
                return;
            }

            if (!name) {
                errorDiv.textContent = 'Please enter an activity name.';
                errorDiv.style.display = 'block';
                nameInput.classList.add('invalid');
                return;
            }

            if (!date) {
                errorDiv.textContent = 'Please select a date.';
                errorDiv.style.display = 'block';
                dateInput.classList.add('invalid');
                return;
            }

            var parts = date.split('-');
            var year  = parseInt(parts[0]);
            var month = parseInt(parts[1]) - 1; // 0-indexed
            var day   = parseInt(parts[2]);

            activities.push({ name: name, year: year, month: month, day: day, tag: tag });

            // If added activity is in current view, navigate to that month
            currentYear  = year;
            currentMonth = month;

            closeModal();
            renderCalendar();
            renderUpcoming();
        }

        function handleLogout() {
            window.location.href = '{{ url("/login") }}';
        }

        // Init
        renderCalendar();
        renderUpcoming();
    </script>

</body>
</html>