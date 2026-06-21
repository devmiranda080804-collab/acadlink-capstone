<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements – CBMA System</title>
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

        .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 22px; }
        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 3px; }
        .page-sub { font-size: 11.5px; color: #888; }

        /* Announcements list panel */
        .ann-panel {
            background: #fff;
            border: 1px solid #e4e4e4;
            border-radius: 8px;
            padding: 10px 0;
            min-height: 300px;
        }

        .ann-item {
            padding: 14px 20px;
            border-bottom: 1px solid #f0f0f0;
            position: relative;
        }

        .ann-item:last-child { border-bottom: none; }
        .ann-item:hover { background: #fafbff; }

        .ann-item-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 4px;
        }

        .ann-item-left {}

        .ann-title { font-size: 12.5px; font-weight: 700; color: #1a1a2e; margin-bottom: 2px; }
        .ann-meta { font-size: 10.5px; color: #aaa; }

        /* Tag badges */
        .badge {
            font-size: 10.5px; font-weight: 600;
            padding: 3px 12px; border-radius: 12px;
            flex-shrink: 0;
        }

        .badge-general  { background: #dbeafe; color: #1e40af; }
        .badge-priority { background: #fef9c3; color: #92400e; }
        .badge-faculty  { background: #d1fae5; color: #065f46; }
        .badge-urgent   { background: #fee2e2; color: #991b1b; }

        .ann-body { font-size: 11.5px; color: #555; line-height: 1.6; margin-top: 6px; }

        /* Delete button */
        .btn-del {
            background: none; border: none;
            color: #ddd; font-size: 14px; cursor: pointer;
            padding: 2px 6px; border-radius: 4px;
            transition: color 0.15s, background 0.15s;
            margin-left: 8px;
        }
        .btn-del:hover { color: #ef4444; background: #fee2e2; }

        .ann-empty {
            text-align: center; padding: 50px 20px;
            color: #bbb; font-size: 12.5px;
        }
        .ann-empty .empty-icon { font-size: 30px; opacity: 0.3; margin-bottom: 8px; }

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

        .modal-title { font-size: 15px; font-weight: 700; color: #1a1a2e; margin-bottom: 18px; }

        .modal-field { margin-bottom: 13px; }
        .modal-field label { display: block; font-size: 11.5px; font-weight: 700; color: #333; margin-bottom: 4px; }
        .modal-field input,
        .modal-field select,
        .modal-field textarea {
            width: 100%; padding: 7px 10px;
            border: 1px solid #ccc; border-radius: 5px;
            font-size: 12.5px; color: #333; outline: none;
            font-family: Arial, sans-serif;
            transition: border-color 0.15s;
        }
        .modal-field input:focus,
        .modal-field select:focus,
        .modal-field textarea:focus { border-color: #0f2557; }
        .modal-field textarea { resize: vertical; min-height: 90px; }
        .modal-field select {
            appearance: none; -webkit-appearance: none;
            background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%23666' d='M5 7L0 2h10z'/%3E%3C/svg%3E") no-repeat right 10px center;
            cursor: pointer;
        }
        .modal-field input.invalid { border-color: #ef4444; }

        .modal-error {
            display: none; background: #fee2e2; border: 1px solid #fca5a5;
            color: #b91c1c; font-size: 11px; padding: 6px 10px;
            border-radius: 4px; margin-bottom: 12px;
        }

        .modal-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 18px; }

        .btn-cancel { background: #fff; border: 1px solid #ccc; color: #444; font-size: 12.5px; font-weight: 600; padding: 8px 18px; border-radius: 5px; cursor: pointer; transition: background 0.15s; }
        .btn-cancel:hover { background: #f5f5f5; }

        .btn-save-modal { background: #0f2557; color: #fff; border: none; font-size: 12.5px; font-weight: 600; padding: 8px 20px; border-radius: 5px; cursor: pointer; transition: background 0.15s; }
        .btn-save-modal:hover { background: #1a3a7a; }

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
            <li><a href="{{ url('/faculty/dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>Dashboard</a></li>
            <li><a href="{{ url('/faculty/my-template') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>My Template</a></li>
            <li><a href="{{ url('/faculty/exam-generator') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 7h8M8 12h8M8 17h4"/></svg>Exam Generator</a></li>
            <li><a href="{{ url('/faculty/shared-library') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>Shared Library</a></li>
            <li><a href="{{ url('/faculty/course-coordination') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20"/></svg>Course Coordination</a></li>
            <li><a href="{{ url('/faculty/analytics') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>Analytics</a></li>
            <li><a href="{{ url('/faculty/user-manuals') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>User manuals</a></li>
            <li><a href="{{ url('/faculty/calendar') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Calendar Of Activities</a></li>
            <li class="active"><a href="{{ url('/faculty/announcements') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>Announcements</a></li>
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
                    <div class="page-title">Announcements</div>
                    <div class="page-sub">Review and approve pending templates and formats</div>
                </div>
            </div>

            {{-- Announcements list --}}
            <div class="ann-panel" id="ann-panel">
                <div class="ann-empty" id="ann-empty">
                    <div class="empty-icon">📢</div>
                    No announcements yet.
                </div>
            </div>

        </div>
    </div>

    <script>
        var announcements = [];

        var TAG_LABELS = {
            general:  'General',
            priority: 'Priority',
            faculty:  'Faculty only',
            urgent:   'Urgent'
        };

        var TAG_CLASSES = {
            general:  'badge-general',
            priority: 'badge-priority',
            faculty:  'badge-faculty',
            urgent:   'badge-urgent'
        };

        function renderAnnouncements() {
            var panel = document.getElementById('ann-panel');

            if (announcements.length === 0) {
                panel.innerHTML =
                    '<div class="ann-empty" id="ann-empty">' +
                    '<div class="empty-icon">📢</div>' +
                    'No announcements yet.</div>';
                return;
            }

            panel.innerHTML = '';
            announcements.forEach(function(a, idx) {
                var item = document.createElement('div');
                item.className = 'ann-item';
                item.innerHTML =
                    '<div class="ann-item-header">' +
                        '<div class="ann-item-left">' +
                            '<div class="ann-title">' + a.title + '</div>' +
                            '<div class="ann-meta">Posted by ' + a.postedBy + ' • ' + a.time + '</div>' +
                        '</div>' +
                        '<span class="badge ' + TAG_CLASSES[a.tag] + '">' + TAG_LABELS[a.tag] + '</span>' +
                    '</div>' +
                    '<div class="ann-body">' + a.body + '</div>';
                panel.appendChild(item);
            });
        }

        function handleLogout() {
            window.location.href = '{{ url("/login") }}';
        }

        renderAnnouncements();
    </script>

</body>
</html>