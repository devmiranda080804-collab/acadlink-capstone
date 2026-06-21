<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Template Review – CBMA System</title>
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

        /* Two-column layout */
        .review-layout {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 16px;
            align-items: start;
        }

        /* ── Left: Template cards list ── */
        .template-list { display: flex; flex-direction: column; gap: 12px; }

        .template-card {
            background: #fff;
            border: 1px solid #e4e4e4;
            border-radius: 8px;
            padding: 14px 16px;
            cursor: pointer;
            transition: box-shadow 0.15s;
        }

        .template-card:hover { box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        .template-card.selected { border-color: #0f2557; box-shadow: 0 0 0 2px rgba(15,37,87,0.15); }

        .card-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 8px;
        }

        .card-type { display: flex; align-items: center; gap: 6px; font-size: 12.5px; font-weight: 700; color: #1a1a2e; }
        .card-type-icon { font-size: 14px; }

        /* Status badges */
        .badge { font-size: 10.5px; font-weight: 700; padding: 3px 12px; border-radius: 12px; }
        .badge-active        { background: #d1fae5; color: #065f46; }
        .badge-submitted     { background: #dbeafe; color: #1e40af; }
        .badge-needs-revision{ background: #fee2e2; color: #991b1b; }
        .badge-approved      { background: #d1fae5; color: #065f46; }
        .badge-rejected      { background: #fee2e2; color: #991b1b; }
        .badge-pending       { background: #fef3c7; color: #92400e; }

        .card-subject { font-size: 12.5px; color: #333; margin-bottom: 4px; font-weight: 600; }
        .card-subject-dot { display: flex; align-items: center; gap: 6px; }
        .card-subject-dot::before { content: '●'; color: #e6a817; font-size: 8px; }

        .card-meta { font-size: 10.5px; color: #aaa; margin-top: 3px; }
        .card-date { font-size: 10.5px; color: #aaa; text-align: right; margin-top: 6px; }

        .empty-list {
            background: #fff; border: 1px solid #e4e4e4; border-radius: 8px;
            text-align: center; padding: 40px 20px; color: #bbb; font-size: 12.5px;
        }

        /* ── Right: Preview panel ── */
        .preview-panel {
            background: #fff;
            border: 1px solid #e4e4e4;
            border-radius: 8px;
            padding: 18px 18px;
            min-height: 300px;
        }

        .preview-title { font-size: 13px; font-weight: 700; color: #1a1a2e; margin-bottom: 14px; }

        .preview-field { margin-bottom: 12px; }
        .preview-label { font-size: 10.5px; font-weight: 700; color: #aaa; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 3px; }
        .preview-value { font-size: 12.5px; color: #333; }

        .preview-status-wrap { margin-bottom: 12px; }

        .preview-body {
            font-size: 11.5px; color: #555; line-height: 1.65;
            border-top: 1px solid #f0f0f0; padding-top: 12px; margin-top: 4px;
        }

        /* Action buttons in preview */
        .preview-actions { display: flex; gap: 8px; margin-top: 16px; }

        .btn-approve {
            flex: 1; background: #0f2557; color: #fff;
            border: none; border-radius: 5px;
            font-size: 12px; font-weight: 600;
            padding: 8px 0; cursor: pointer;
            transition: background 0.15s;
        }
        .btn-approve:hover { background: #1a3a7a; }

        .btn-reject {
            flex: 1; background: #ef4444; color: #fff;
            border: none; border-radius: 5px;
            font-size: 12px; font-weight: 600;
            padding: 8px 0; cursor: pointer;
            transition: background 0.15s;
        }
        .btn-reject:hover { background: #dc2626; }

        .preview-empty {
            text-align: center; padding: 40px 10px;
            color: #bbb; font-size: 12px;
        }

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
            <li><a href="{{ url('/program-head/dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>Dashboard</a></li>
            <li class="active"><a href="{{ url('/program-head/template-review') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>Template Review</a></li>
            <li><a href="{{ url('/program-head/course-oversight') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>Course Oversight</a></li>
            <li><a href="{{ url('/program-head/account-management') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>Account Management</a></li>
            <li><a href="{{ url('/program-head/announcements') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>Announcements</a></li>
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
                <span class="role-badge">Program head</span>
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

            <div class="page-title">Template Review</div>
            <div class="page-sub">Review and approve submitted templates</div>

            <div class="review-layout">

                {{-- Left: Template list --}}
                <div class="template-list" id="template-list">
                    <div class="empty-list">No templates submitted for review yet.</div>
                </div>

                {{-- Right: Preview panel --}}
                <div class="preview-panel" id="preview-panel">
                    <div class="preview-title">Preview</div>
                    <div class="preview-empty">Select a template from the list to preview details.</div>
                </div>

            </div>

        </div>
    </div>

    <script>
        // Frontend-only template data — empty by default
        var templates = [];
        var selectedIdx = -1;

        var STATUS_BADGES = {
            active:         '<span class="badge badge-active">Active</span>',
            submitted:      '<span class="badge badge-submitted">Submitted</span>',
            needs_revision: '<span class="badge badge-needs-revision">Needs Revision</span>',
            approved:       '<span class="badge badge-approved">Approved</span>',
            rejected:       '<span class="badge badge-rejected">Rejected</span>',
            pending:        '<span class="badge badge-pending">Pending</span>',
        };

        function renderList() {
            var list = document.getElementById('template-list');

            if (templates.length === 0) {
                list.innerHTML = '<div class="empty-list">No templates submitted for review yet.</div>';
                return;
            }

            list.innerHTML = '';
            templates.forEach(function(t, idx) {
                var card = document.createElement('div');
                card.className = 'template-card' + (idx === selectedIdx ? ' selected' : '');
                card.onclick = function() { selectTemplate(idx); };
                card.innerHTML =
                    '<div class="card-header">' +
                        '<div class="card-type"><span class="card-type-icon">📄</span> Template Review</div>' +
                        (STATUS_BADGES[t.status] || '') +
                    '</div>' +
                    '<div class="card-subject-dot"><span class="card-subject">' + t.subject + '</span></div>' +
                    '<div class="card-meta">' + t.type + '<br>Submitted by ' + t.submittedBy + '</div>' +
                    '<div class="card-date">' + t.date + '</div>';
                list.appendChild(card);
            });
        }

        function selectTemplate(idx) {
            selectedIdx = idx;
            renderList();
            renderPreview();
        }

        function renderPreview() {
            var panel = document.getElementById('preview-panel');

            if (selectedIdx < 0 || !templates[selectedIdx]) {
                panel.innerHTML =
                    '<div class="preview-title">Preview</div>' +
                    '<div class="preview-empty">Select a template from the list to preview details.</div>';
                return;
            }

            var t = templates[selectedIdx];
            var canAct = t.status === 'submitted' || t.status === 'pending';

            panel.innerHTML =
                '<div class="preview-title">Preview</div>' +
                '<div class="preview-field"><div class="preview-label">Preview</div><div class="preview-value">' + t.subject + '</div></div>' +
                '<div class="preview-field"><div class="preview-label">Submitted by</div><div class="preview-value">' + t.submittedBy + '</div></div>' +
                '<div class="preview-field"><div class="preview-label">Submitted Date</div><div class="preview-value">' + t.date + '</div></div>' +
                '<div class="preview-status-wrap"><div class="preview-label">Status</div>' + (STATUS_BADGES[t.status] || t.status) + '</div>' +
                '<div class="preview-field"><div class="preview-label">Content Preview</div></div>' +
                '<div class="preview-body">' + (t.preview || 'No preview available.') + '</div>' +
                (canAct ?
                    '<div class="preview-actions">' +
                        '<button class="btn-approve" onclick="approveTemplate()">✔ Approve</button>' +
                        '<button class="btn-reject" onclick="rejectTemplate()">✕ Reject</button>' +
                    '</div>' : '');
        }

        function approveTemplate() {
            templates[selectedIdx].status = 'approved';
            renderList();
            renderPreview();
        }

        function rejectTemplate() {
            templates[selectedIdx].status = 'rejected';
            renderList();
            renderPreview();
        }

        function handleLogout() {
            window.location.href = '{{ url("/login") }}';
        }

        renderList();
    </script>

</body>
</html>