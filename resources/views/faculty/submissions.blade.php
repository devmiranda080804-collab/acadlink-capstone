<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submissions & Deadlines – CBMA System</title>
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
            display: flex; flex-direction: column;
            flex-shrink: 0; overflow-y: auto;
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
        .nav-list li.active a { background-color: rgba(255,255,255,0.08); color: #fff; border-left: 3px solid #fff; }
        .nav-list li a svg { width: 18px; height: 18px; flex-shrink: 0; opacity: 0.85; }

        .sidebar-logout { padding: 12px 0; border-top: 1px solid rgba(255,255,255,0.1); }
        .sidebar-logout a {
            display: flex; align-items: center; gap: 11px;
            padding: 11px 20px; color: #c8d6ec; text-decoration: none; font-size: 13px;
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
        .user-avatar { width: 34px; height: 34px; border-radius: 50%; background-color: #0f2557; color: #fff; font-size: 12px; font-weight: 700; display: flex; align-items: center; justify-content: center; }

        /* ═══════════════════ CONTENT ═══════════════════ */
        .content { flex: 1; overflow-y: auto; padding: 24px 28px 28px; }

        .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 18px; }
        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 3px; }
        .page-sub { font-size: 11.5px; color: #888; }

        .btn-notification {
            display: flex; align-items: center; gap: 6px;
            background: #fff; color: #333;
            border: 1px solid #ccc; border-radius: 6px;
            font-size: 12.5px; font-weight: 600;
            padding: 8px 16px; cursor: pointer;
            transition: background 0.15s; white-space: nowrap;
        }
        .btn-notification:hover { background: #f5f5f5; }

        /* Alert banner */
        .alert-banner {
            display: flex; align-items: flex-start; gap: 12px;
            background: #fef2f2; border: 1px solid #fecaca;
            border-radius: 8px; padding: 14px 18px;
            margin-bottom: 18px;
        }

        .alert-banner.hidden { display: none; }

        .alert-icon { font-size: 18px; flex-shrink: 0; margin-top: 1px; }

        .alert-text .alert-title {
            font-size: 13px; font-weight: 700; color: #dc2626; margin-bottom: 2px;
        }

        .alert-text .alert-sub { font-size: 11.5px; color: #ef4444; }

        /* Table */
        .table-wrap {
            background: #fff;
            border: 1px solid #e4e4e4;
            border-radius: 8px;
            overflow: hidden;
        }

        .sub-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
        }

        .sub-table thead tr {
            background: #f9f9f9;
            border-bottom: 2px solid #e0e0e0;
        }

        .sub-table th {
            padding: 11px 16px;
            text-align: left;
            font-size: 11.5px; font-weight: 700; color: #555;
        }

        .sub-table td {
            padding: 13px 16px;
            border-bottom: 1px solid #f5f5f5;
            color: #333; vertical-align: middle;
        }

        .sub-table tbody tr:last-child td { border-bottom: none; }
        .sub-table tbody tr:hover { background: #fafbff; }

        .sub-table .subject-name { font-size: 12.5px; font-weight: 600; color: #1a1a2e; }

        /* Days remaining colors */
        .days-soon     { color: #f59e0b; font-weight: 600; font-size: 12px; }
        .days-overdue  { color: #ef4444; font-weight: 600; font-size: 12px; }
        .days-ok       { color: #10b981; font-weight: 600; font-size: 12px; }

        /* Status badges */
        .badge {
            display: inline-block; padding: 3px 12px;
            border-radius: 12px; font-size: 11px; font-weight: 700;
        }
        .badge-due-soon  { background: #fef3c7; color: #92400e; }
        .badge-overdue   { background: #fee2e2; color: #991b1b; }
        .badge-due-soon2 { background: #fef3c7; color: #92400e; }
        .badge-on-track  { background: #d1fae5; color: #065f46; }

        /* View action */
        .btn-view-action {
            display: flex; align-items: center; gap: 4px;
            background: none; border: none;
            color: #555; font-size: 12px; font-weight: 600;
            cursor: pointer; transition: color 0.15s;
        }
        .btn-view-action:hover { color: #0f2557; }

        /* Empty table */
        .table-empty {
            text-align: center; padding: 40px 20px;
            color: #bbb; font-size: 12.5px;
        }

        /* ═══════════════════ MODAL ═══════════════════ */
        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.45); z-index: 999;
            align-items: center; justify-content: center;
        }
        .modal-overlay.open { display: flex; }

        .modal {
            background: #fff; border-radius: 10px;
            padding: 24px 26px; width: 460px; max-width: 95vw;
            box-shadow: 0 8px 32px rgba(0,0,0,0.25);
        }

        .modal-title { font-size: 15px; font-weight: 700; color: #1a1a2e; margin-bottom: 6px; }
        .modal-sub { font-size: 11.5px; color: #888; margin-bottom: 18px; }

        .modal-row { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 8px; }
        .modal-info-item {}
        .modal-info-label { font-size: 10.5px; font-weight: 700; color: #aaa; margin-bottom: 2px; text-transform: uppercase; }
        .modal-info-value { font-size: 12.5px; color: #333; font-weight: 600; }

        .modal-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 20px; }

        .btn-close-modal { background: #0f2557; color: #fff; border: none; font-size: 12.5px; font-weight: 600; padding: 8px 20px; border-radius: 5px; cursor: pointer; transition: background 0.15s; }
        .btn-close-modal:hover { background: #1a3a7a; }

        svg { display: inline-block; vertical-align: middle; }
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
        @if($navPermissions['dashboard'] ?? true)
        <li class="{{ request()->is('faculty/dashboard') ? 'active' : '' }}">
            <a href="{{ url('/faculty/dashboard') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
        </li>
        @endif
        @if($navPermissions['my-template'] ?? true)
        <li class="{{ request()->is('faculty/my-template*') ? 'active' : '' }}">
            <a href="{{ url('/faculty/my-template') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                My Template
            </a>
        </li>
        @endif
        @if($navPermissions['exam-generator'] ?? true)
        <li class="{{ request()->is('faculty/exam-generator*') ? 'active' : '' }}">
            <a href="{{ url('/faculty/exam-generator') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                Exam Generator
            </a>
        </li>
        @endif
        @if($navPermissions['shared-library'] ?? true)
        <li class="{{ request()->is('faculty/shared-library*') ? 'active' : '' }}">
            <a href="{{ url('/faculty/shared-library') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
                Shared Library
            </a>
        </li>
        @endif
        @if($navPermissions['course-coordination'] ?? true)
        <li class="{{ request()->is('faculty/course-coordination*') ? 'active' : '' }}">
            <a href="{{ url('/faculty/course-coordination') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                Course Coordination
            </a>
        </li>
        @endif
        @if($navPermissions['analytics'] ?? true)
        <li class="{{ request()->is('faculty/analytics*') ? 'active' : '' }}">
            <a href="{{ url('/faculty/analytics') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                Analytics
            </a>
        </li>
        @endif
        @if($navPermissions['calendar'] ?? true)
        <li class="{{ request()->is('faculty/calendar*') ? 'active' : '' }}">
            <a href="{{ url('/faculty/calendar') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Calendar of Activities
            </a>
        </li>
        @endif
        @if($navPermissions['announcements'] ?? true)
        <li class="{{ request()->is('faculty/announcements*') ? 'active' : '' }}">
            <a href="{{ url('/faculty/announcements') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                Announcements
            </a>
        </li>
        @endif
        @if($navPermissions['submissions'] ?? true)
        <li class="{{ request()->is('faculty/submissions*') ? 'active' : '' }}">
            <a href="{{ url('/faculty/submissions') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                Submissions and Deadline
            </a>
        </li>
        @endif
        @if($navPermissions['cms'] ?? true)
        <li class="{{ request()->is('faculty/cms*') ? 'active' : '' }}">
            <a href="{{ url('/faculty/cms') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                CMS
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
                    <div class="page-title">Submissions & Deadlines</div>
                    <div class="page-sub">Track your pending materials and due date</div>
                </div>
                <button class="btn-notification" type="button">
                    🔔 Notification
                </button>
            </div>

            {{-- Alert banner — shows when there are overdue items --}}
            <div class="alert-banner hidden" id="alert-banner">
                <span class="alert-icon">⚠️</span>
                <div class="alert-text">
                    <div class="alert-title" id="alert-title">You have overdue submissions</div>
                    <div class="alert-sub">Please submit as soon as possible or contact your department head</div>
                </div>
            </div>

            {{-- Submissions table --}}
            <div class="table-wrap">
                <table class="sub-table">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Material Type</th>
                            <th>Assigned By</th>
                            <th>Due Date</th>
                            <th>Days Remaining</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sub-tbody">
                        <tr>
                            <td colspan="7" class="table-empty">No submissions or deadlines found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    {{-- ════════════ VIEW MODAL ════════════ --}}
    <div class="modal-overlay" id="view-modal">
        <div class="modal">
            <div class="modal-title" id="view-subject">—</div>
            <div class="modal-sub" id="view-material">—</div>

            <div class="modal-row">
                <div class="modal-info-item">
                    <div class="modal-info-label">Assigned By</div>
                    <div class="modal-info-value" id="view-assigned">—</div>
                </div>
                <div class="modal-info-item">
                    <div class="modal-info-label">Due Date</div>
                    <div class="modal-info-value" id="view-due">—</div>
                </div>
            </div>

            <div class="modal-row">
                <div class="modal-info-item">
                    <div class="modal-info-label">Days Remaining</div>
                    <div class="modal-info-value" id="view-days">—</div>
                </div>
                <div class="modal-info-item">
                    <div class="modal-info-label">Status</div>
                    <div class="modal-info-value" id="view-status">—</div>
                </div>
            </div>

            <div class="modal-actions">
                <button class="btn-close-modal" type="button" onclick="closeViewModal()">Close</button>
            </div>
        </div>
    </div>

    <script>
        // Frontend-only submissions data (no database)
        var submissions = [];

        function getDaysRemaining(dueDateStr) {
            var due   = new Date(dueDateStr);
            var today = new Date();
            today.setHours(0,0,0,0);
            due.setHours(0,0,0,0);
            return Math.floor((due - today) / (1000 * 60 * 60 * 24));
        }

        function getStatusBadge(days) {
            if (days < 0)  return '<span class="badge badge-overdue">Overdue</span>';
            if (days <= 2) return '<span class="badge badge-due-soon">Due soon</span>';
            if (days <= 3) return '<span class="badge badge-due-soon2">Due soon</span>';
            return '<span class="badge badge-on-track">On Track</span>';
        }

        function getDaysClass(days) {
            if (days < 0)  return 'days-overdue';
            if (days <= 3) return 'days-soon';
            return 'days-ok';
        }

        function getDaysLabel(days) {
            if (days < 0)  return Math.abs(days) + ' day' + (Math.abs(days) !== 1 ? 's' : '') + ' overdue';
            if (days === 0) return 'Today';
            return days + ' day' + (days !== 1 ? 's' : '');
        }

        function formatDate(str) {
            var d = new Date(str);
            return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        }

        function renderTable() {
            var tbody   = document.getElementById('sub-tbody');
            var banner  = document.getElementById('alert-banner');
            var alertTitle = document.getElementById('alert-title');

            if (submissions.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="table-empty">No submissions or deadlines found.</td></tr>';
                banner.classList.add('hidden');
                return;
            }

            var overdueCount = submissions.filter(function(s) { return getDaysRemaining(s.dueDate) < 0; }).length;

            if (overdueCount > 0) {
                alertTitle.textContent = 'You have ' + overdueCount + ' overdue submission' + (overdueCount > 1 ? 's' : '');
                banner.classList.remove('hidden');
            } else {
                banner.classList.add('hidden');
            }

            tbody.innerHTML = '';
            submissions.forEach(function(s, idx) {
                var days   = getDaysRemaining(s.dueDate);
                var tr     = document.createElement('tr');
                tr.innerHTML =
                    '<td><span class="subject-name">' + s.subject + '</span></td>' +
                    '<td>' + s.materialType + '</td>' +
                    '<td>' + s.assignedBy + '</td>' +
                    '<td>' + formatDate(s.dueDate) + '</td>' +
                    '<td><span class="' + getDaysClass(days) + '">' + getDaysLabel(days) + '</span></td>' +
                    '<td>' + getStatusBadge(days) + '</td>' +
                    '<td>' +
                        '<button class="btn-view-action" onclick="viewSubmission(' + idx + ')">' +
                            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>' +
                            ' View' +
                        '</button>' +
                    '</td>';
                tbody.appendChild(tr);
            });
        }

        function viewSubmission(idx) {
            var s    = submissions[idx];
            var days = getDaysRemaining(s.dueDate);

            document.getElementById('view-subject').textContent  = s.subject;
            document.getElementById('view-material').textContent = s.materialType;
            document.getElementById('view-assigned').textContent = s.assignedBy;
            document.getElementById('view-due').textContent      = formatDate(s.dueDate);
            document.getElementById('view-days').textContent     = getDaysLabel(days);
            document.getElementById('view-status').innerHTML     = getStatusBadge(days);

            document.getElementById('view-modal').classList.add('open');
        }

        function closeViewModal() {
            document.getElementById('view-modal').classList.remove('open');
        }

        document.getElementById('view-modal').addEventListener('click', function(e) {
            if (e.target === this) closeViewModal();
        });

        function handleLogout() {
            window.location.href = '{{ url("/login") }}';
        }

        // Init
        renderTable();
    </script>

</body>
</html>