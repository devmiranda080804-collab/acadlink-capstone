<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Template Approvals – CBMA System</title>
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

        .filter-tabs { display: flex; gap: 4px; margin-bottom: 18px; }
        .filter-tab { padding: 7px 16px; font-size: 12px; color: #666; background: #fff; border: 1px solid #e0e0e0; border-radius: 6px; cursor: pointer; user-select: none; }
        .filter-tab:hover { border-color: #0f2557; color: #0f2557; }
        .filter-tab.active { background: #0f2557; color: #fff; border-color: #0f2557; font-weight: 600; }

        .approval-panel { background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; overflow: hidden; }
        .approval-table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
        .approval-table thead tr { background: #fafafa; border-bottom: 1px solid #eee; }
        .approval-table th { padding: 12px 16px; text-align: left; font-size: 11.5px; font-weight: 700; color: #666; }
        .approval-table td { padding: 13px 16px; border-bottom: 1px solid #f5f5f5; color: #333; vertical-align: middle; }
        .approval-table tbody tr:last-child td { border-bottom: none; }
        .approval-table tbody tr:hover { background: #fafbff; }
        .approval-table td.empty-row { text-align: center; color: #999; padding: 40px 16px; }

        .type-label { color: #0f2557; font-weight: 600; }
        .program-tag { display: inline-block; font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 10px; background: #f0f4ff; color: #0f2557; }

        .status-badge { font-size: 10px; font-weight: 700; padding: 4px 10px; border-radius: 12px; white-space: nowrap; }
        .status-pending_approval { background: #fef9c3; color: #92400e; }
        .status-approved         { background: #d1fae5; color: #065f46; }
        .status-rejected         { background: #f3f4f6; color: #6b7280; }
        .status-pending_review   { background: #dbeafe; color: #1e40af; }
        .status-needs_revision   { background: #fee2e2; color: #991b1b; }

        .action-buttons { display: flex; gap: 6px; }
        .btn-approve { display: inline-flex; align-items: center; gap: 4px; background: #0f2557; color: #fff; border: none; font-size: 11px; font-weight: 600; padding: 6px 12px; border-radius: 5px; cursor: pointer; }
        .btn-approve:hover { background: #1a3a7a; }
        .btn-reject { display: inline-flex; align-items: center; gap: 4px; background: #ef4444; color: #fff; border: none; font-size: 11px; font-weight: 600; padding: 6px 12px; border-radius: 5px; cursor: pointer; }
        .btn-reject:hover { background: #dc2626; }
        .btn-view { display: inline-flex; align-items: center; gap: 4px; background: #fff; color: #444; border: 1px solid #d0d0d0; font-size: 11px; font-weight: 600; padding: 6px 12px; border-radius: 5px; text-decoration: none; }
        .btn-view:hover { background: #f5f5f5; }
        .btn-approve svg, .btn-reject svg, .btn-view svg { width: 11px; height: 11px; }

        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 999; align-items: center; justify-content: center; }
        .modal-overlay.open { display: flex; }
        .modal { background: #fff; border-radius: 10px; padding: 24px 26px; width: 440px; max-width: 95vw; box-shadow: 0 8px 32px rgba(0,0,0,0.25); }
        .modal-title { font-size: 15px; font-weight: 700; color: #1a1a2e; margin-bottom: 8px; }
        .modal-sub { font-size: 11.5px; color: #888; margin-bottom: 16px; }
        .modal-field label { display: block; font-size: 11.5px; font-weight: 700; color: #333; margin-bottom: 4px; }
        .modal-field textarea { width: 100%; min-height: 90px; padding: 8px 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 12.5px; outline: none; font-family: Arial, sans-serif; resize: vertical; }
        .modal-field textarea:focus { border-color: #0f2557; }
        .modal-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 18px; }
        .btn-cancel { background: #fff; border: 1px solid #ccc; color: #444; font-size: 12.5px; font-weight: 600; padding: 8px 18px; border-radius: 5px; cursor: pointer; }
        .btn-cancel:hover { background: #f5f5f5; }
        .btn-submit-reject { background: #ef4444; color: #fff; border: none; font-size: 12.5px; font-weight: 600; padding: 8px 20px; border-radius: 5px; cursor: pointer; }
        .btn-submit-reject:hover { background: #dc2626; }

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

            <div class="page-title">Template Approvals</div>
            <div class="page-sub">Review and approve pending templates and formats</div>

            {{-- Filter tabs (JS-based) --}}
            <div class="filter-tabs">
                <span class="filter-tab active" onclick="filterRows('all', this)">All</span>
                <span class="filter-tab" onclick="filterRows('pending_approval', this)">Pending Approval</span>
                <span class="filter-tab" onclick="filterRows('approved', this)">Approved</span>
                <span class="filter-tab" onclick="filterRows('rejected', this)">Rejected</span>
            </div>

            <div class="approval-panel">
                <table class="approval-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Title</th>
                            <th>Program</th>
                            <th>Submitted By</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="approval-tbody">
                        @forelse($templates as $template)
                            <tr data-status="{{ $template->status }}">
                                <td><span class="type-label">{{ ucwords(str_replace('_', ' ', $template->type)) }}</span></td>
                                <td>{{ $template->title }}</td>
                                <td><span class="program-tag">{{ $template->program }}</span></td>
                                <td>{{ $template->faculty->name }}</td>
                                <td>{{ $template->created_at->format('Y-m-d') }}</td>
                                <td><span class="status-badge status-{{ $template->status }}">{{ $template->status_label }}</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <a class="btn-view" href="{{ Storage::url($template->file_path) }}" target="_blank">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                            View
                                        </a>
                                        @if($template->status === 'pending_approval')
                                            <form method="POST" action="{{ url('/admin/template-approvals/' . $template->id . '/approve') }}" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn-approve">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                                    Approve
                                                </button>
                                            </form>
                                            <button type="button" class="btn-reject"
                                                onclick="openRejectModal('{{ url('/admin/template-approvals/' . $template->id . '/reject') }}', '{{ addslashes($template->title) }}')">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                                Reject
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="empty-row">Wala pang templates para sa approval.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    {{-- Reject Modal --}}
    <div class="modal-overlay" id="reject-overlay">
        <div class="modal">
            <form id="reject-form" method="POST">
                @csrf
                <div class="modal-title">Reject Template</div>
                <div class="modal-sub" id="reject-sub">Bigyan ng dahilan kung bakit tinatanggihan ang template.</div>
                <div class="modal-field">
                    <label>Rejection Reason <span style="color:#ef4444">*</span></label>
                    <textarea name="review_note" placeholder="Halimbawa: Hindi sumusunod sa opisyal na format ng syllabus..." required></textarea>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeRejectModal()">Cancel</button>
                    <button type="submit" class="btn-submit-reject">Reject Template</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function filterRows(status, el) {
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            el.classList.add('active');
            document.querySelectorAll('#approval-tbody tr').forEach(row => {
                if (!row.dataset.status) return; // empty row
                row.style.display = (status === 'all' || row.dataset.status === status) ? '' : 'none';
            });
        }

        function openRejectModal(action, title) {
            document.getElementById('reject-form').action = action;
            document.getElementById('reject-sub').textContent = 'Tinatanggihan ang: ' + title;
            document.getElementById('reject-overlay').classList.add('open');
        }
        function closeRejectModal() {
            document.getElementById('reject-overlay').classList.remove('open');
        }
        document.getElementById('reject-overlay').addEventListener('click', function(e) {
            if (e.target === this) closeRejectModal();
        });
    </script>

</body>
</html>