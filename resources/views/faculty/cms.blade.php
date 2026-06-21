<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS – CBMA System</title>
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

        /* ── Top two-column layout ── */
        .top-grid {
            display: grid;
            grid-template-columns: 230px 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }

        /* Left CMS nav */
        .cms-nav {
            background: #fff;
            border: 1px solid #e4e4e4;
            border-radius: 8px;
            overflow: hidden;
        }

        .cms-nav-item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 11px 16px;
            font-size: 12.5px; color: #333;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.15s;
            user-select: none;
        }

        .cms-nav-item:last-child { border-bottom: none; }
        .cms-nav-item:hover { background: #f5f7ff; color: #0f2557; }

        .cms-nav-item.active {
            background: #0f2557;
            color: #fff;
            font-weight: 700;
        }

        .cms-nav-item.active .nav-arrow { color: #fff; }
        .nav-arrow { font-size: 11px; color: #aaa; }

        /* Right panel */
        .cms-panel {
            background: #fff;
            border: 1px solid #e4e4e4;
            border-radius: 8px;
            padding: 18px 20px;
            min-height: 260px;
        }

        .cms-panel-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 8px;
        }

        .cms-panel-title { font-size: 14px; font-weight: 700; color: #1a1a2e; }

        .btn-add-new {
            background: #0f2557; color: #fff;
            border: none; border-radius: 5px;
            font-size: 12px; font-weight: 600;
            padding: 7px 14px; cursor: pointer;
            transition: background 0.15s;
            display: flex; align-items: center; gap: 4px;
        }
        .btn-add-new:hover { background: #1a3a7a; }

        .cms-panel-desc { font-size: 11.5px; color: #aaa; margin-bottom: 14px; }

        .panel-empty {
            text-align: center; padding: 40px 20px;
            color: #ccc; font-size: 12px;
        }

        /* ── Sub-section panels (bottom) ── */
        .sub-sections {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
        }

        .sub-panel {
            background: #fff;
            border: 1px solid #e4e4e4;
            border-radius: 8px;
            padding: 16px;
            display: flex; flex-direction: column;
            min-height: 220px;
        }

        .sub-panel-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 12px;
        }

        .sub-panel-title { font-size: 12.5px; font-weight: 700; color: #1a1a2e; }

        .btn-add-sm {
            background: #0f2557; color: #fff;
            border: none; border-radius: 4px;
            font-size: 11px; font-weight: 600;
            padding: 5px 10px; cursor: pointer;
            transition: background 0.15s;
        }
        .btn-add-sm:hover { background: #1a3a7a; }

        /* Sub-panel table */
        .sub-table {
            width: 100%; border-collapse: collapse; font-size: 11.5px;
        }

        .sub-table thead tr { background: #f9f9f9; border-bottom: 1px solid #e8e8e8; }
        .sub-table th { padding: 7px 8px; text-align: left; font-size: 11px; font-weight: 700; color: #666; }
        .sub-table td { padding: 8px 8px; border-bottom: 1px solid #f5f5f5; color: #444; vertical-align: middle; }
        .sub-table tbody tr:last-child td { border-bottom: none; }
        .sub-table tbody tr:hover { background: #fafbff; }

        /* Toggle switch */
        .toggle { position: relative; width: 30px; height: 16px; display: inline-block; cursor: pointer; }
        .toggle input { opacity: 0; width: 0; height: 0; }
        .toggle-slider { position: absolute; inset: 0; background: #ccc; border-radius: 16px; transition: background 0.2s; }
        .toggle-slider::before { content: ''; position: absolute; width: 12px; height: 12px; background: #fff; border-radius: 50%; top: 2px; left: 2px; transition: transform 0.2s; }
        .toggle input:checked + .toggle-slider { background: #0f2557; }
        .toggle input:checked + .toggle-slider::before { transform: translateX(14px); }

        /* Icon btn */
        .icon-btn { background: none; border: none; cursor: pointer; color: #aaa; font-size: 13px; padding: 2px 4px; transition: color 0.15s; }
        .icon-btn:hover { color: #ef4444; }

        .sub-empty { text-align: center; padding: 20px 10px; color: #ccc; font-size: 11.5px; }

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
            <li><a href="{{ url('/faculty/announcements') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>Announcements</a></li>
            <li><a href="{{ url('/faculty/submissions') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Submissions and Deadline</a></li>
            <li class="active"><a href="{{ url('/faculty/cms') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 010 14.14M4.93 4.93a10 10 0 000 14.14"/></svg>CMS</a></li>
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

            <div class="page-title">Content Management System</div>
            <div class="page-sub">Manage system settings, templates, and configurations</div>

            {{-- Top: Left nav + Right panel --}}
            <div class="top-grid">

                {{-- Left CMS navigation --}}
                <div class="cms-nav">
                    <div class="cms-nav-item active" onclick="selectNav(this,'template-elements')">Template Elements <span class="nav-arrow">▶</span></div>
                    <div class="cms-nav-item" onclick="selectNav(this,'dropdown-options')">Dropdown Options <span class="nav-arrow">▶</span></div>
                    <div class="cms-nav-item" onclick="selectNav(this,'departments')">Departments <span class="nav-arrow">▶</span></div>
                    <div class="cms-nav-item" onclick="selectNav(this,'semester-settings')">Semester settings <span class="nav-arrow">▶</span></div>
                    <div class="cms-nav-item" onclick="selectNav(this,'notification-triggers')">Notification Triggers <span class="nav-arrow">▶</span></div>
                    <div class="cms-nav-item" onclick="selectNav(this,'announcements-cms')">Announcements <span class="nav-arrow">▶</span></div>
                </div>

                {{-- Right content panel --}}
                <div class="cms-panel">
                    <div class="cms-panel-header">
                        <div class="cms-panel-title" id="panel-title">Template Elements</div>
                        <button class="btn-add-new" onclick="handleAddNew()">+ Add new</button>
                    </div>
                    <div class="cms-panel-desc" id="panel-desc">Manage template sections, fields, and layout components used across all instructional material templates.</div>
                    <div class="panel-empty" id="panel-body">No items found. Click <strong>+ Add new</strong> to get started.</div>
                </div>

            </div>

            {{-- Bottom 4-column sub-sections --}}
            <div class="sub-sections">

                {{-- Dropdown Options --}}
                <div class="sub-panel">
                    <div class="sub-panel-header">
                        <div class="sub-panel-title">Dropdown Options</div>
                        <button class="btn-add-sm" onclick="handleAddNew()">+ Add</button>
                    </div>
                    <table class="sub-table">
                        <thead>
                            <tr>
                                <th>Field Name</th>
                                <th>Custom Options</th>
                                <th>Object Type</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="dropdown-tbody">
                            <tr><td colspan="4" class="sub-empty">No options yet.</td></tr>
                        </tbody>
                    </table>
                </div>

                {{-- Semester Settings --}}
                <div class="sub-panel">
                    <div class="sub-panel-header">
                        <div class="sub-panel-title">Semester Settings</div>
                        <button class="btn-add-sm" onclick="handleAddNew()">+ Add</button>
                    </div>
                    <table class="sub-table">
                        <thead>
                            <tr>
                                <th>Academic Year</th>
                                <th>Semester</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="4" class="sub-empty">No semesters configured.</td></tr>
                        </tbody>
                    </table>
                </div>

                {{-- Notification Triggers --}}
                <div class="sub-panel">
                    <div class="sub-panel-header">
                        <div class="sub-panel-title">Notification Triggers</div>
                        <button class="btn-add-sm" onclick="handleAddNew()">+ Add</button>
                    </div>
                    <table class="sub-table">
                        <thead>
                            <tr>
                                <th>Trigger Name</th>
                                <th>Recipient</th>
                                <th>Message</th>
                                <th>Active</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="trigger-tbody">
                            <tr><td colspan="5" class="sub-empty">No triggers configured.</td></tr>
                        </tbody>
                    </table>
                </div>

                {{-- Announcements --}}
                <div class="sub-panel">
                    <div class="sub-panel-header">
                        <div class="sub-panel-title">Announcements</div>
                        <button class="btn-add-sm" onclick="handleAddNew()">+ Add</button>
                    </div>
                    <div class="sub-empty">No CMS announcements yet.</div>
                </div>

            </div>

        </div>
    </div>

    <script>
        var navData = {
            'template-elements':   { title: 'Template Elements',   desc: 'Manage template sections, fields, and layout components used across all instructional material templates.' },
            'dropdown-options':    { title: 'Dropdown Options',     desc: 'Configure dropdown fields and their selectable values used in forms and templates.' },
            'departments':         { title: 'Departments',          desc: 'Manage academic departments, program heads, and department-level configurations.' },
            'semester-settings':   { title: 'Semester Settings',    desc: 'Configure academic year and semester periods used across the system.' },
            'notification-triggers': { title: 'Notification Triggers', desc: 'Set up automated notification rules triggered by system events.' },
            'announcements-cms':   { title: 'Announcements',        desc: 'Manage system-wide announcements and their visibility settings.' }
        };

        function selectNav(el, key) {
            document.querySelectorAll('.cms-nav-item').forEach(function(i) { i.classList.remove('active'); });
            el.classList.add('active');
            var data = navData[key];
            document.getElementById('panel-title').textContent = data.title;
            document.getElementById('panel-desc').textContent  = data.desc;
            document.getElementById('panel-body').innerHTML    = 'No items found. Click <strong>+ Add new</strong> to get started.';
        }

        function handleAddNew() {
            alert('This feature is not yet available in this prototype.');
        }

        function handleLogout() {
            window.location.href = '{{ url("/login") }}';
        }
    </script>

</body>
</html>