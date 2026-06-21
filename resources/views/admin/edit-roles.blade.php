<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Roles – CBMA System</title>
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
        .edit-panel {
            background: #fff;
            border: 1px solid #e4e4e4;
            border-radius: 8px;
            overflow: hidden;
        }

        .edit-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
        }

        .edit-table thead tr { border-bottom: 1px solid #e8e8e8; }

        .edit-table th {
            padding: 12px 20px;
            text-align: left;
            font-size: 11.5px;
            font-weight: 700;
            color: #555;
            letter-spacing: 0.3px;
        }

        .edit-table td {
            padding: 13px 20px;
            border-bottom: 1px solid #f5f5f5;
            color: #333;
            vertical-align: middle;
        }

        .edit-table tbody tr:last-child td { border-bottom: none; }
        .edit-table tbody tr:hover { background: #fafbff; }

        /* User name link style */
        .user-link { color: #2563eb; font-weight: 600; text-decoration: none; }
        .user-link:hover { text-decoration: underline; }

        /* Edit role button */
        .btn-edit-role {
            display: inline-flex; align-items: center; gap: 4px;
            background: none; border: none;
            color: #0f2557; font-size: 12px; font-weight: 600;
            cursor: pointer; padding: 3px 6px; border-radius: 4px;
            transition: background 0.15s;
        }
        .btn-edit-role:hover { background: #f0f4ff; }

        .table-empty { text-align: center; padding: 36px; color: #bbb; font-size: 12.5px; }

        /* ═══════════════════ MODAL ═══════════════════ */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 999; align-items: center; justify-content: center; }
        .modal-overlay.open { display: flex; }

        .modal { background: #fff; border-radius: 10px; padding: 24px 26px; width: 400px; max-width: 95vw; box-shadow: 0 8px 32px rgba(0,0,0,0.25); }

        .modal-title { font-size: 15px; font-weight: 700; color: #1a1a2e; margin-bottom: 6px; }
        .modal-sub { font-size: 11.5px; color: #888; margin-bottom: 18px; }

        .modal-field { margin-bottom: 14px; }
        .modal-field label { display: block; font-size: 11.5px; font-weight: 700; color: #333; margin-bottom: 4px; }
        .modal-field select {
            width: 100%; height: 34px; padding: 0 10px;
            border: 1px solid #ccc; border-radius: 5px;
            font-size: 12.5px; color: #333; outline: none; background: #fff;
            appearance: none; -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%23666' d='M5 7L0 2h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 10px center;
            cursor: pointer; transition: border-color 0.15s;
        }
        .modal-field select:focus { border-color: #0f2557; }

        .modal-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 18px; }
        .btn-cancel { background: #fff; border: 1px solid #ccc; color: #444; font-size: 12.5px; font-weight: 600; padding: 8px 18px; border-radius: 5px; cursor: pointer; transition: background 0.15s; }
        .btn-cancel:hover { background: #f5f5f5; }
        .btn-save { background: #0f2557; color: #fff; border: none; font-size: 12.5px; font-weight: 600; padding: 8px 20px; border-radius: 5px; cursor: pointer; transition: background 0.15s; }
        .btn-save:hover { background: #1a3a7a; }

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
            <li><a href="{{ url('/admin/system-approvals') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>System Approvals</a></li>
            <li class="active"><a href="{{ url('/admin/edit-roles') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>Edit Roles</a></li>
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

            <div class="page-title">Edit role</div>
            <div class="page-sub">Review and approve pending templates and formats</div>

            <div class="edit-panel">
                <table class="edit-table">
                    <thead>
                        <tr>
                            <th>USER</th>
                            <th>EMAIL</th>
                            <th>ROLE</th>
                            <th>DEPARTMENT</th>
                            <th>LAST ACTIVE</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody id="edit-tbody">
                        <tr>
                            <td colspan="6" class="table-empty">No users found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    {{-- ════════════ EDIT ROLE MODAL ════════════ --}}
    <div class="modal-overlay" id="modal-overlay">
        <div class="modal">
            <div class="modal-title">Edit Role</div>
            <div class="modal-sub" id="modal-sub">Change role for this user</div>

            <div class="modal-field">
                <label>New Role</label>
                <select id="new-role">
                    <option value="Dean">Dean</option>
                    <option value="Secretary">Secretary</option>
                    <option value="Dept Head">Dept Head</option>
                    <option value="Faculty">Faculty</option>
                    <option value="Student">Student</option>
                </select>
            </div>

            <div class="modal-actions">
                <button class="btn-cancel" type="button" onclick="closeModal()">Cancel</button>
                <button class="btn-save" type="button" onclick="saveRole()">Save</button>
            </div>
        </div>
    </div>

    <script>
        var users = [];
        var editIdx = -1;

        function renderTable() {
            var tbody = document.getElementById('edit-tbody');

            if (users.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="table-empty">No users found.</td></tr>';
                return;
            }

            tbody.innerHTML = '';
            users.forEach(function(u, idx) {
                var tr = document.createElement('tr');
                tr.innerHTML =
                    '<td><span class="user-link">' + u.name + '</span></td>' +
                    '<td>' + u.email + '</td>' +
                    '<td>' + u.role + '</td>' +
                    '<td>' + u.department + '</td>' +
                    '<td>' + u.lastActive + '</td>' +
                    '<td><button class="btn-edit-role" onclick="openEditModal(' + idx + ')">✏ Edit role</button></td>';
                tbody.appendChild(tr);
            });
        }

        function openEditModal(idx) {
            editIdx = idx;
            document.getElementById('modal-sub').textContent = 'Change role for ' + users[idx].name;
            document.getElementById('new-role').value = users[idx].role;
            document.getElementById('modal-overlay').classList.add('open');
        }

        function closeModal() {
            document.getElementById('modal-overlay').classList.remove('open');
        }

        document.getElementById('modal-overlay').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        function saveRole() {
            users[editIdx].role = document.getElementById('new-role').value;
            closeModal();
            renderTable();
        }

        function handleLogout() {
            window.location.href = '{{ url("/login") }}';
        }

        renderTable();
    </script>

</body>
</html>