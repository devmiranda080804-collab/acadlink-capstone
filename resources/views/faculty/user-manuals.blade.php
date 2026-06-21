<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Manual – CBMA System</title>
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
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 22px 16px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-logo img {
            width: 72px; height: 72px;
            border-radius: 50%;
            object-fit: contain;
            background: #1b3d7a;
        }

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
            color: #fff;
            border-left: 3px solid #fff;
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

        .role-badge {
            background-color: #0f2557; color: #fff;
            font-size: 12px; font-weight: 600;
            padding: 5px 16px; border-radius: 20px;
        }

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

        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 2px; }
        .page-sub { font-size: 11.5px; color: #888; margin-bottom: 20px; }

        /* Two-column layout */
        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            align-items: start;
        }

        /* ── Getting Started panel ── */
        .panel {
            background: #fff;
            border: 1px solid #e4e4e4;
            border-radius: 8px;
            padding: 18px 20px;
        }

        .panel-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 16px;
        }

        /* Step items */
        .step-item {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
            align-items: flex-start;
        }

        .step-item:last-child { margin-bottom: 0; }

        .step-number {
            width: 22px; height: 22px;
            border-radius: 50%;
            background-color: #0f2557;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .step-content {}

        .step-title {
            font-size: 12.5px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 3px;
        }

        .step-desc {
            font-size: 11px;
            color: #888;
            line-height: 1.55;
        }

        /* ── Right column panels ── */

        /* Role Permissions */
        .role-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .role-table tr {
            border-bottom: 1px solid #f0f0f0;
        }

        .role-table tr:last-child { border-bottom: none; }

        .role-table td {
            padding: 9px 6px;
            color: #333;
            vertical-align: top;
        }

        .role-table td:first-child {
            font-weight: 700;
            color: #0f2557;
            width: 90px;
            white-space: nowrap;
        }

        .role-table tr:hover { background: #fafbff; }

        /* Support & Help */
        .support-section { margin-top: 14px; }

        .support-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 12px;
        }

        .support-block { margin-bottom: 14px; }

        .support-block-title {
            font-size: 12px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        .support-block p {
            font-size: 11.5px;
            color: #555;
            line-height: 1.7;
        }

        .support-block a {
            color: #2563eb;
            text-decoration: none;
            font-size: 11.5px;
        }

        .support-block a:hover { text-decoration: underline; }

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
            <li>
                <a href="{{ url('/faculty/dashboard') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="{{ url('/faculty/my-template') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    My Template
                </a>
            </li>
            <li>
                <a href="{{ url('/faculty/exam-generator') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 7h8M8 12h8M8 17h4"/></svg>
                    Exam Generator
                </a>
            </li>
            <li>
                <a href="{{ url('/faculty/shared-library') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                    Shared Library
                </a>
            </li>
            <li>
                <a href="{{ url('/faculty/course-coordination') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20"/></svg>
                    Course Coordination
                </a>
            </li>
            <li>
                <a href="{{ url('/faculty/analytics') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                    Analytics
                </a>
            </li>
            <li class="active">
                <a href="{{ url('/faculty/user-manuals') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    User manuals
                </a>
            </li>
            <li><a href="{{ url('/faculty/calendar') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Calendar Of Activities</a></li>
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

            <div class="page-title">User Manual</div>
            <div class="page-sub">Set by Secretary/Dean/Admin</div>

            <div class="two-col">

                {{-- LEFT: Getting Started --}}
                <div class="panel">
                    <div class="panel-title">Getting Started</div>

                    <div class="step-item">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <div class="step-title">Midterm Examinations Begin</div>
                            <div class="step-desc">Log in with your assigned credentials to access the admin dashboard and overview of system activities.</div>
                        </div>
                    </div>

                    <div class="step-item">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <div class="step-title">Manage Programs & Subjects</div>
                            <div class="step-desc">Navigate to Academic section to add, edit, or remove programs and subjects. Ensure all course details are accurate.</div>
                        </div>
                    </div>

                    <div class="step-item">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <div class="step-title">Upload Syllabus & Content</div>
                            <div class="step-desc">Use the Syllabus page to upload course outlines. Add questions to the question bank for assessment creation.</div>
                        </div>
                    </div>

                    <div class="step-item">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <div class="step-title">Post Announcements</div>
                            <div class="step-desc">Create announcements for students and faculty. Choose appropriate audience tags (General, Faculty, Faculty Only).</div>
                        </div>
                    </div>

                    <div class="step-item">
                        <div class="step-number">5</div>
                        <div class="step-content">
                            <div class="step-title">Monitor User Activity</div>
                            <div class="step-desc">Check Role Monitoring and Audit Logs regularly to track system usage and maintain security compliance.</div>
                        </div>
                    </div>

                </div>

                {{-- RIGHT: Role Permissions + Support & Help --}}
                <div>

                    {{-- Role Permissions --}}
                    <div class="panel">
                        <div class="panel-title">Role Permissions</div>
                        <table class="role-table">
                            <tbody>
                                <tr>
                                    <td>Dean</td>
                                    <td>Full system access and approval rights</td>
                                </tr>
                                <tr>
                                    <td>Secretary</td>
                                    <td>Manage calendar, memos, announcements</td>
                                </tr>
                                <tr>
                                    <td>Dept Head</td>
                                    <td>Department-level program and subject management</td>
                                </tr>
                                <tr>
                                    <td>Faculty</td>
                                    <td>Subject content, syllabus, and question bank</td>
                                </tr>
                                <tr>
                                    <td>Student</td>
                                    <td>View-only access to relevant content</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Support & Help --}}
                    <div class="panel support-section">
                        <div class="support-title">Support & Help</div>

                        <div class="support-block">
                            <div class="support-block-title">Technical Support</div>
                            <p>
                                Email: <a href="mailto:support@college.edu">support@college.edu</a><br>
                                Hours: Monday–Friday, 8:00 AM – 5:00 PM
                            </p>
                        </div>

                        <div class="support-block">
                            <div class="support-block-title">IT Helpdesk</div>
                            <p>
                                Phone: (02) 8888-1234<br>
                                Local: 101
                            </p>
                        </div>

                        <div class="support-block">
                            <div class="support-block-title">System Administrator</div>
                            <p>
                                Email: <a href="mailto:admin@college.edu">admin@college.edu</a>
                            </p>
                        </div>
                    </div>

                </div>

            </div>{{-- end two-col --}}

        </div>{{-- end content --}}
    </div>{{-- end main --}}

    <script>
        function handleLogout() {
            window.location.href = '{{ url("/login") }}';
        }
    </script>

</body>
</html>