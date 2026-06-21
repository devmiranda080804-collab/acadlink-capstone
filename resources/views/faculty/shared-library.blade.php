<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shared Library – CBMA System</title>
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
            width: 72px;
            height: 72px;
            border-radius: 50%;
            object-fit: contain;
            background: #1b3d7a;
        }

        .sidebar-logo .brand { color: #fff; font-size: 15px; font-weight: 700; margin-top: 8px; }
        .sidebar-logo .brand-sub { color: #a0b4d6; font-size: 10px; margin-top: 2px; }

        .nav-list { list-style: none; padding: 10px 0; flex: 1; }

        .nav-list li a {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 11px 20px;
            color: #c8d6ec;
            text-decoration: none;
            font-size: 13px;
            transition: background 0.15s, color 0.15s;
        }

        .nav-list li a:hover { background-color: rgba(255,255,255,0.08); color: #fff; }

        .nav-list li.active a {
            background-color: rgba(255,255,255,0.08);
            color: #fff;
            border-left: 3px solid #fff;
        }

        .nav-list li a svg { width: 18px; height: 18px; flex-shrink: 0; opacity: 0.85; }

        .sidebar-logout {
            padding: 12px 0;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-logout a {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 11px 20px;
            color: #c8d6ec;
            text-decoration: none;
            font-size: 13px;
            transition: background 0.15s;
        }

        .sidebar-logout a:hover { background-color: rgba(255,255,255,0.08); color: #fff; }

        /* ═══════════════════ MAIN ═══════════════════ */
        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background-color: #f5f6fa;
        }

        .topnav {
            background: #fff;
            border-bottom: 1px solid #e0e0e0;
            padding: 0 24px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .topnav .label { font-size: 12px; color: #666; }
        .topnav-right { display: flex; align-items: center; gap: 12px; }

        .role-badge {
            background-color: #0f2557;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            padding: 5px 16px;
            border-radius: 20px;
        }

        .user-info { display: flex; align-items: center; gap: 8px; }
        .user-text { text-align: right; }
        .user-name { font-size: 12px; font-weight: 600; color: #222; }
        .user-email { font-size: 10px; color: #888; }

        .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background-color: #0f2557;
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ═══════════════════ CONTENT ═══════════════════ */
        .content { flex: 1; overflow-y: auto; padding: 24px 28px 28px; }

        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 3px; }
        .page-sub { font-size: 11.5px; color: #888; margin-bottom: 18px; }

        /* ── Search & Filter row ── */
        .search-filter-row {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 18px;
        }

        .search-wrap {
            flex: 1;
            position: relative;
        }

        .search-wrap svg {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 14px;
            height: 14px;
            color: #aaa;
        }

        .search-wrap input {
            width: 100%;
            height: 34px;
            padding: 0 10px 0 32px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 12px;
            color: #333;
            outline: none;
            background: #fff;
            transition: border-color 0.15s;
        }

        .search-wrap input:focus { border-color: #0f2557; }

        .filter-select {
            height: 34px;
            padding: 0 28px 0 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 12px;
            color: #333;
            background: #fff;
            outline: none;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%23666' d='M5 7L0 2h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            cursor: pointer;
            min-width: 85px;
        }

        /* ── Resource Cards ── */
        .resource-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-bottom: 20px;
        }

        .resource-card {
            background: #fff;
            border: 1px solid #e4e4e4;
            border-radius: 8px;
            padding: 14px 16px 16px;
        }

        .rc-icon { color: #e6a817; font-size: 20px; margin-bottom: 8px; }

        .rc-title {
            font-size: 12.5px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 5px;
        }

        .rc-meta {
            font-size: 10.5px;
            color: #888;
            line-height: 1.7;
            margin-bottom: 2px;
        }

        .btn-download {
            width: 100%;
            height: 32px;
            background: #0f2557;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            transition: background 0.15s;
        }

        .btn-download:hover { background: #1a3a7a; }

        /* Empty resource cards */
        .empty-cards {
            grid-column: 1 / -1;
            text-align: center;
            padding: 28px;
            color: #bbb;
            font-size: 12.5px;
            background: #fff;
            border: 1px solid #e4e4e4;
            border-radius: 8px;
        }

        .empty-cards .empty-icon { font-size: 28px; opacity: 0.3; margin-bottom: 8px; }

        /* ── OBE: CO-PO Mapping ── */
        .obe-section {
            background: #fff;
            border: 1px solid #e4e4e4;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 16px;
        }

        .obe-title {
            font-size: 13px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 12px;
        }

        .obe-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .obe-table th {
            background: #f5f5f5;
            padding: 8px 14px;
            text-align: left;
            font-weight: 600;
            color: #555;
            border: 1px solid #e0e0e0;
            font-size: 11.5px;
        }

        .obe-table td {
            padding: 8px 14px;
            border: 1px solid #ebebeb;
            color: #444;
            font-size: 12px;
        }

        .obe-table tbody tr:hover { background: #fafafa; }

        .obe-empty {
            text-align: center;
            padding: 20px;
            color: #bbb;
            font-size: 12px;
        }

        /* ── Syllabus Table section ── */
        .syllabus-header-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 10px;
        }

        .btn-upload-syllabus {
            background: #0f2557;
            color: #fff;
            border: none;
            font-size: 12px;
            font-weight: 600;
            padding: 8px 18px;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background 0.15s;
        }

        .btn-upload-syllabus:hover { background: #1a3a7a; }

        .syllabus-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e4e4e4;
        }

        .syllabus-table thead tr {
            background: #f9f9f9;
            border-bottom: 2px solid #e0e0e0;
        }

        .syllabus-table th {
            padding: 11px 16px;
            text-align: left;
            font-size: 12px;
            font-weight: 700;
            color: #444;
        }

        .syllabus-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #f0f0f0;
            color: #333;
            font-size: 12.5px;
            vertical-align: middle;
        }

        .syllabus-table tbody tr:last-child td { border-bottom: none; }
        .syllabus-table tbody tr:hover { background: #fafbff; }

        .badge-v {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-none {
            font-size: 11px;
            font-weight: 600;
            color: #f59e0b;
        }

        .btn-change {
            background: #0f2557;
            color: #fff;
            border: none;
            font-size: 11.5px;
            font-weight: 600;
            padding: 5px 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn-change:hover { background: #1a3a7a; }

        .btn-upload-row {
            background: #fff;
            color: #0f2557;
            border: 1.5px solid #0f2557;
            font-size: 11.5px;
            font-weight: 600;
            padding: 5px 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn-upload-row:hover { background: #f0f4ff; }

        .syllabus-empty {
            text-align: center;
            padding: 30px;
            color: #bbb;
            font-size: 12.5px;
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
            <li class="active">
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
            <li><a href="{{ url('/faculty/analytics') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>Analytics</a></li>
            <li><a href="{{ url('/faculty/user-manuals') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>User manuals</a></li>
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

        {{-- Top nav --}}
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

        {{-- Content --}}
        <div class="content">

            <div class="page-title">Shared Library</div>
            <div class="page-sub">Browse and download shared teaching resources</div>

            {{-- Search & Filter --}}
            <div class="search-filter-row">
                <div class="search-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" id="search-input" placeholder="Search resources..." oninput="filterResources()">
                </div>
                <select class="filter-select" id="filter-type" onchange="filterResources()">
                    <option value="all">ALL</option>
                    <option value="slides">Slides</option>
                    <option value="case-study">Case Study</option>
                    <option value="activity">Activity</option>
                </select>
                <select class="filter-select" id="filter-code" onchange="filterResources()">
                    <option value="all">Code</option>
                </select>
            </div>

            {{-- Resource Cards --}}
            <div class="resource-cards" id="resource-cards">
                <div class="empty-cards">
                    <div class="empty-icon">📂</div>
                    No shared resources available yet.
                </div>
            </div>

            {{-- OBE: CO-PO Mapping --}}
            <div class="obe-section">
                <div class="obe-title">OBE: CO-PO Mapping</div>
                <table class="obe-table" id="obe-table">
                    <thead>
                        <tr>
                            <th>Course Outcome</th>
                            <th>PO1</th>
                            <th>PO2</th>
                            <th>PO3</th>
                            <th>PO4</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="obe-empty">No CO-PO mapping data available.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Upload Syllabus --}}
            <div class="syllabus-header-row">
                <button class="btn-upload-syllabus" type="button" onclick="handleUploadSyllabus()">
                    ⬆ Upload Syllabus
                </button>
            </div>

            {{-- Syllabus Table --}}
            <table class="syllabus-table">
                <thead>
                    <tr>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>Last Updated</th>
                        <th>Updated By</th>
                        <th>Version</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="syllabus-tbody">
                    <tr>
                        <td colspan="6" class="syllabus-empty">No syllabus records found.</td>
                    </tr>
                </tbody>
            </table>

        </div>{{-- end content --}}
    </div>{{-- end main --}}

    <script>
        // ── Search / filter (frontend only — filters UI cards when data exists) ──
        function filterResources() {
            // No data to filter for now — placeholder for future use
        }

        // ── Upload Syllabus button ──
        function handleUploadSyllabus() {
            alert('Upload Syllabus feature is not yet available in this prototype.');
        }

        // ── Logout ──
        function handleLogout() {
            window.location.href = '{{ url("/login") }}';
        }
    </script>

</body>
</html>