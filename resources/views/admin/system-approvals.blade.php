<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Approvals – CBMA System</title>
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

        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 3px; }
        .page-sub { font-size: 11.5px; color: #888; margin-bottom: 20px; }

        /* Table panel */
        .approval-panel {
            background: #fff;
            border: 1px solid #e4e4e4;
            border-radius: 8px;
            overflow: hidden;
        }

        .approval-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
        }

        .approval-table thead tr {
            border-bottom: 1px solid #e8e8e8;
        }

        .approval-table th {
            padding: 12px 20px;
            text-align: left;
            font-size: 12px;
            font-weight: 700;
            color: #444;
        }

        .approval-table td {
            padding: 13px 20px;
            border-bottom: 1px solid #f5f5f5;
            color: #333;
            vertical-align: middle;
        }

        .approval-table tbody tr:last-child td { border-bottom: none; }
        .approval-table tbody tr:hover { background: #fafbff; }

        /* Type colors */
        .type-template { color: #2563eb; font-weight: 600; }
        .type-format   { color: #7c3aed; font-weight: 600; }

        /* Status badge */
        .badge-pending  { background: #f3f4f6; color: #555; font-size: 11px; font-weight: 600; padding: 3px 12px; border-radius: 12px; }
        .badge-approved { background: #d1fae5; color: #065f46; font-size: 11px; font-weight: 600; padding: 3px 12px; border-radius: 12px; }
        .badge-rejected { background: #fee2e2; color: #991b1b; font-size: 11px; font-weight: 600; padding: 3px 12px; border-radius: 12px; }

        /* Action buttons */
        .action-btns { display: flex; align-items: center; gap: 6px; }

        .btn-approve {
            display: flex; align-items: center; gap: 4px;
            background: #0f2557; color: #fff;
            border: none; border-radius: 4px;
            font-size: 11px; font-weight: 600;
            padding: 5px 12px; cursor: pointer;
            transition: background 0.15s;
        }
        .btn-approve:hover { background: #1a3a7a; }
        .btn-approve:disabled { background: #9ca3af; cursor: not-allowed; }

        .btn-reject {
            display: flex; align-items: center; gap: 4px;
            background: #ef4444; color: #fff;
            border: none; border-radius: 4px;
            font-size: 11px; font-weight: 600;
            padding: 5px 12px; cursor: pointer;
            transition: background 0.15s;
        }
        .btn-reject:hover { background: #dc2626; }
        .btn-reject:disabled { background: #9ca3af; cursor: not-allowed; }

        .table-empty { text-align: center; padding: 36px; color: #bbb; font-size: 12.5px; }

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
            <li><a href="{{ url('/admin/dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>Dashboard</a></li>
            <li><a href="{{ url('/admin/account-management') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>Account Management</a></li>
            <li><a href="{{ url('/admin/roles-permissions') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>Roles & Permissions</a></li>
            <li class="active"><a href="{{ url('/admin/system-approvals') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>System Approvals</a></li>
            <li><a href="{{ url('/admin/edit-roles') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>Edit Roles</a></li>
            <li><a href="{{ url('/admin/audit-logs') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>Audit Logs</a></li>
            <li><a href="{{ url('/admin/announcements') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>Announcements</a></li>
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
                <span class="role-badge">Admin/Dean</span>
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

            <div class="page-title">System Approvals</div>
            <div class="page-sub">Review and approve pending templates and formats</div>

            <div class="approval-panel">
                <table class="approval-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Name</th>
                            <th>Submitted By</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="approval-tbody">
                        <tr>
                            <td colspan="6" class="table-empty">No pending approvals.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script>
        // Frontend-only approval data
        var approvals = [];

        function renderTable() {
            var tbody = document.getElementById('approval-tbody');

            if (approvals.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="table-empty">No pending approvals.</td></tr>';
                return;
            }

            tbody.innerHTML = '';
            approvals.forEach(function(a, idx) {
                var tr = document.createElement('tr');
                tr.id = 'row-' + idx;

                var statusBadge = '';
                if (a.status === 'Pending')  statusBadge = '<span class="badge-pending">Pending</span>';
                if (a.status === 'Approved') statusBadge = '<span class="badge-approved">Approved</span>';
                if (a.status === 'Rejected') statusBadge = '<span class="badge-rejected">Rejected</span>';

                var typeClass = a.type === 'Template' ? 'type-template' : 'type-format';
                var isPending = a.status === 'Pending';

                tr.innerHTML =
                    '<td><span class="' + typeClass + '">' + a.type + '</span></td>' +
                    '<td>' + a.name + '</td>' +
                    '<td>' + a.submittedBy + '</td>' +
                    '<td>' + a.date + '</td>' +
                    '<td>' + statusBadge + '</td>' +
                    '<td><div class="action-btns">' +
                        '<button class="btn-approve" ' + (!isPending ? 'disabled' : '') + ' onclick="approveItem(' + idx + ')">✔ Approve</button>' +
                        '<button class="btn-reject"  ' + (!isPending ? 'disabled' : '') + ' onclick="rejectItem(' + idx + ')">✕ Reject</button>' +
                    '</div></td>';

                tbody.appendChild(tr);
            });
        }

        function approveItem(idx) {
            approvals[idx].status = 'Approved';
            renderTable();
        }

        function rejectItem(idx) {
            approvals[idx].status = 'Rejected';
            renderTable();
        }

        function handleLogout() {
            window.location.href = '{{ url("/login") }}';
        }

        renderTable();
    </script>

</body>
</html>