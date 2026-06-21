<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles & Permissions – CBMA System</title>
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

        .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 20px; }
        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 3px; }
        .page-sub { font-size: 11.5px; color: #888; }

        .btn-save {
            display: flex; align-items: center; gap: 6px;
            background: #0f2557; color: #fff;
            border: none; border-radius: 6px;
            font-size: 12.5px; font-weight: 600;
            padding: 9px 18px; cursor: pointer;
            transition: background 0.15s;
        }
        .btn-save:hover { background: #1a3a7a; }

        /* Permissions table */
        .perm-panel {
            background: #fff;
            border: 1px solid #e4e4e4;
            border-radius: 8px;
            overflow: hidden;
        }

        .perm-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .perm-table thead tr {
            background: #fff;
            border-bottom: 1px solid #e8e8e8;
        }

        .perm-table th {
            padding: 13px 20px;
            text-align: left;
            font-size: 12.5px;
            font-weight: 700;
            color: #444;
        }

        .perm-table th:first-child { width: 220px; }

        .perm-table th:not(:first-child) {
            text-align: center;
            width: 120px;
        }

        .perm-table td {
            padding: 12px 20px;
            border-bottom: 1px solid #f5f5f5;
            color: #333;
            font-size: 13px;
        }

        .perm-table tbody tr:last-child td { border-bottom: none; }
        .perm-table tbody tr:hover { background: #fafbff; }

        .perm-table td:not(:first-child) { text-align: center; }

        /* Custom checkbox */
        .cb-wrap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .cb-wrap input[type="checkbox"] {
            display: none;
        }

        .cb-box {
            width: 18px; height: 18px;
            border: 2px solid #ccc;
            border-radius: 3px;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            transition: background 0.15s, border-color 0.15s;
            flex-shrink: 0;
        }

        .cb-wrap input[type="checkbox"]:checked + .cb-box {
            background: #e53e3e;
            border-color: #e53e3e;
        }

        .cb-wrap input[type="checkbox"]:checked + .cb-box::after {
            content: '';
            display: block;
            width: 5px; height: 9px;
            border: 2px solid #fff;
            border-top: none; border-left: none;
            transform: rotate(45deg) translateY(-1px);
        }

        /* Save notification */
        .save-toast {
            display: none;
            position: fixed; bottom: 24px; right: 24px;
            background: #0f2557; color: #fff;
            padding: 10px 20px; border-radius: 6px;
            font-size: 13px; font-weight: 600;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            z-index: 999;
            animation: fadeIn 0.2s ease;
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

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
            <li class="active"><a href="{{ url('/admin/roles-permissions') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>Roles & Permissions</a></li>
            <li><a href="{{ url('/admin/system-approvals') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>System Approvals</a></li>
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

            <div class="page-header">
                <div>
                    <div class="page-title">Roles & Permissions</div>
                    <div class="page-sub">Configure access control for each role</div>
                </div>
                <button class="btn-save" type="button" onclick="saveChanges()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Save Changes
                </button>
            </div>

            <div class="perm-panel">
                <table class="perm-table" id="perm-table">
                    <thead>
                        <tr>
                            <th>Module</th>
                            <th>Admin / Dean</th>
                            <th>Program Head</th>
                            <th>Secretary</th>
                            <th>Faculty</th>
                        </tr>
                    </thead>
                    <tbody id="perm-tbody"></tbody>
                </table>
            </div>

        </div>
    </div>

    {{-- Save toast --}}
    <div class="save-toast" id="save-toast">✅ Permissions saved successfully!</div>

    <script>
        // Permission matrix — [admin, programHead, secretary, faculty]
        var permissions = [
            { module: 'Dashboard',             admin: true,  ph: true,  sec: true,  fac: true  },
            { module: 'User Management',        admin: true,  ph: false, sec: false, fac: false },
            { module: 'Template Review',        admin: true,  ph: true,  sec: false, fac: false },
            { module: 'Document Repository',    admin: true,  ph: true,  sec: true,  fac: true  },
            { module: 'Exam Generator',         admin: true,  ph: false, sec: false, fac: true  },
            { module: 'Analytics',              admin: true,  ph: true,  sec: false, fac: true  },
            { module: 'System Settings',        admin: true,  ph: false, sec: true,  fac: false },
            { module: 'Course Filing',          admin: true,  ph: true,  sec: true,  fac: false },
            { module: 'Template Distribution',  admin: true,  ph: false, sec: true,  fac: false },
        ];

        function renderTable() {
            var tbody = document.getElementById('perm-tbody');
            tbody.innerHTML = '';

            permissions.forEach(function(row, idx) {
                var tr = document.createElement('tr');
                tr.innerHTML =
                    '<td>' + row.module + '</td>' +
                    makeCheckbox(idx, 'admin', row.admin) +
                    makeCheckbox(idx, 'ph',    row.ph)    +
                    makeCheckbox(idx, 'sec',   row.sec)   +
                    makeCheckbox(idx, 'fac',   row.fac);
                tbody.appendChild(tr);
            });
        }

        function makeCheckbox(idx, field, checked) {
            var id = 'cb-' + idx + '-' + field;
            return '<td>' +
                '<label class="cb-wrap" for="' + id + '">' +
                    '<input type="checkbox" id="' + id + '" ' + (checked ? 'checked' : '') +
                        ' onchange="togglePerm(' + idx + ',\'' + field + '\',this.checked)">' +
                    '<span class="cb-box"></span>' +
                '</label>' +
            '</td>';
        }

        function togglePerm(idx, field, val) {
            permissions[idx][field] = val;
        }

        function saveChanges() {
            var toast = document.getElementById('save-toast');
            toast.style.display = 'block';
            setTimeout(function() { toast.style.display = 'none'; }, 2500);
        }

        function handleLogout() {
            window.location.href = '{{ url("/login") }}';
        }

        renderTable();
    </script>

</body>
</html>