<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Coordination – CBMA System</title>
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

        /* ── Select Course box ── */
        .course-select-box {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 16px 18px;
            margin-bottom: 0;
        }

        .course-select-box label {
            display: block;
            font-size: 11.5px;
            font-weight: 600;
            color: #444;
            margin-bottom: 8px;
        }

        .course-select-wrap {
            position: relative;
            display: inline-block;
            width: 100%;
            max-width: 320px;
        }

        .course-select-wrap select {
            width: 100%;
            height: 34px;
            padding: 0 32px 0 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 12.5px;
            color: #333;
            background: #fff;
            outline: none;
            appearance: none;
            -webkit-appearance: none;
            cursor: pointer;
            transition: border-color 0.15s;
        }

        .course-select-wrap select:focus { border-color: #0f2557; }

        .course-select-wrap::after {
            content: '';
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 5px solid #666;
            pointer-events: none;
        }

        /* ── Sub-tabs: Master Folder / Collaboration ── */
        .sub-tabs {
            display: flex;
            gap: 0;
            border-bottom: 2px solid #e0e0e0;
            margin-bottom: 18px;
            margin-top: 16px;
        }

        .sub-tab {
            padding: 7px 18px;
            font-size: 12.5px;
            color: #666;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: color 0.15s;
            user-select: none;
            text-decoration: none;
        }

        .sub-tab:hover { color: #0f2557; }

        .sub-tab.active {
            color: #0f2557;
            font-weight: 700;
            border-bottom: 2px solid #0f2557;
        }

        /* ── Tab content ── */
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        /* ═══════════════════════
           MASTER FOLDER TAB
        ═══════════════════════ */
        .folder-panel {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
        }

        .folder-panel-header {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 13px 18px;
            border-bottom: 1px solid #f0f0f0;
            background: #fafafa;
        }

        .folder-panel-header .folder-icon { font-size: 16px; color: #e6a817; }

        .folder-panel-header .folder-title {
            font-size: 13px;
            font-weight: 700;
            color: #1a1a2e;
        }

        .folder-panel-header .folder-sub {
            font-size: 11px;
            color: #999;
            margin-top: 1px;
        }

        /* File row */
        .file-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 11px 18px;
            border-bottom: 1px solid #f5f5f5;
            transition: background 0.12s;
        }

        .file-row:last-child { border-bottom: none; }
        .file-row:hover { background: #f9fbff; }

        .file-left {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .file-icon { font-size: 18px; margin-top: 1px; flex-shrink: 0; }

        .file-name {
            font-size: 12.5px;
            font-weight: 600;
            color: #1a1a2e;
            margin-bottom: 2px;
        }

        .file-meta {
            font-size: 10.5px;
            color: #aaa;
        }

        .btn-view {
            background: #fff;
            border: 1px solid #d0d0d0;
            color: #333;
            font-size: 11.5px;
            font-weight: 600;
            padding: 5px 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.15s;
            flex-shrink: 0;
        }

        .btn-view:hover { background: #f5f5f5; }

        .btn-view-blue {
            background: #0f2557;
            border: none;
            color: #fff;
            font-size: 11.5px;
            font-weight: 600;
            padding: 5px 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.15s;
            flex-shrink: 0;
        }

        .btn-view-blue:hover { background: #1a3a7a; }

        .folder-empty {
            text-align: center;
            padding: 36px;
            color: #bbb;
            font-size: 12.5px;
        }

        /* ═══════════════════════
           COLLABORATION TAB
        ═══════════════════════ */
        .collab-layout {
            display: grid;
            grid-template-columns: 1fr 220px;
            gap: 16px;
            align-items: start;
        }

        /* Document panel */
        .doc-panel {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
        }

        .doc-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            border-bottom: 1px solid #f0f0f0;
            background: #fafafa;
            flex-wrap: wrap;
            gap: 8px;
        }

        .doc-title-input {
            font-size: 13px;
            font-weight: 700;
            color: #1a1a2e;
            border: none;
            background: transparent;
            outline: none;
            min-width: 140px;
        }

        .toolbar-actions {
            display: flex;
            gap: 6px;
            align-items: center;
            flex-wrap: wrap;
        }

        .toolbar-btn {
            display: flex;
            align-items: center;
            gap: 4px;
            background: #fff;
            border: 1px solid #d0d0d0;
            color: #333;
            font-size: 11.5px;
            padding: 5px 12px;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.15s;
        }

        .toolbar-btn:hover { background: #f5f5f5; }

        .toolbar-btn.primary {
            background: #0f2557;
            color: #fff;
            border-color: #0f2557;
        }

        .toolbar-btn.primary:hover { background: #1a3a7a; }

        .doc-meta {
            font-size: 10.5px;
            color: #aaa;
            padding: 6px 14px 4px;
            border-bottom: 1px solid #f5f5f5;
        }

        .doc-body {
            padding: 18px 18px 24px;
            font-size: 12.5px;
            color: #333;
            line-height: 1.7;
            min-height: 340px;
        }

        .doc-body h3 {
            font-size: 13px;
            font-weight: 700;
            color: #1a1a2e;
            margin: 14px 0 6px;
        }

        .doc-body h3:first-child { margin-top: 0; }

        .doc-body p { margin-bottom: 6px; }

        .doc-body ul {
            padding-left: 18px;
            margin-bottom: 8px;
        }

        .doc-body ul li { margin-bottom: 3px; }

        .doc-empty {
            text-align: center;
            padding: 60px 20px;
            color: #bbb;
            font-size: 12.5px;
        }

        .doc-empty .empty-icon { font-size: 32px; opacity: 0.3; margin-bottom: 10px; }

        /* Right panel: Outline / Comments / Template */
        .side-panel {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
        }

        .side-tabs {
            display: flex;
            border-bottom: 1px solid #e0e0e0;
        }

        .side-tab {
            flex: 1;
            text-align: center;
            padding: 9px 4px;
            font-size: 11.5px;
            color: #666;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
            transition: color 0.15s;
            user-select: none;
        }

        .side-tab:hover { color: #0f2557; }

        .side-tab.active {
            color: #0f2557;
            font-weight: 700;
            border-bottom: 2px solid #0f2557;
        }

        .side-tab-content { display: none; padding: 14px; }
        .side-tab-content.active { display: block; }

        .outline-item {
            padding: 5px 0;
            font-size: 12px;
            color: #444;
            border-bottom: 1px solid #f5f5f5;
            cursor: pointer;
            transition: color 0.15s;
        }

        .outline-item:hover { color: #0f2557; }
        .outline-item:last-child { border-bottom: none; }

        .side-empty {
            text-align: center;
            padding: 20px 10px;
            color: #bbb;
            font-size: 11.5px;
        }

        /* Comment cards */
        .comment-card {
            border: 1px solid #e8e8e8;
            border-radius: 7px;
            padding: 10px 12px;
            margin-bottom: 10px;
            background: #fff;
        }

        .comment-card:last-child { margin-bottom: 0; }

        .comment-author {
            font-size: 12px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 1px;
        }

        .comment-time {
            font-size: 10.5px;
            color: #aaa;
            margin-bottom: 6px;
        }

        .comment-text {
            font-size: 11.5px;
            color: #444;
            line-height: 1.5;
        }

        /* Template items */
        .template-item {
            border: 1px solid #e8e8e8;
            border-radius: 7px;
            padding: 11px 14px;
            margin-bottom: 8px;
            font-size: 12.5px;
            color: #1a1a2e;
            background: #fff;
            cursor: pointer;
            transition: background 0.15s, border-color 0.15s;
        }

        .template-item:last-child { margin-bottom: 0; }
        .template-item:hover { background: #f0f4ff; border-color: #0f2557; }

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
            <li class="active">
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

            <div class="page-title">Course Coordination</div>
            <div class="page-sub">Access master course folder and instructor collaboration</div>

            {{-- Select Course --}}
            <div class="course-select-box">
                <label>Select Course</label>
                <div class="course-select-wrap">
                    <select id="course-select" onchange="onCourseChange()">
                        <option value="" disabled selected>— Select a course —</option>
                        <option value="cae1">CAE1 – Financial Accounting and Reporting</option>
                        <option value="cae2">CAE2 – Marketing Management</option>
                        <option value="cae3">CAE3 – Intermediate Accounting 1</option>
                        <option value="cae4">CAE4 – Cost Accounting and Control</option>
                        <option value="cae5">CAE5 – Business Laws and Regulations</option>
                        <option value="cs101">CS 101 – Introduction to Programming</option>
                    </select>
                </div>
            </div>

            {{-- Sub-tabs --}}
            <div class="sub-tabs">
                <span class="sub-tab active" onclick="switchSubTab('master-folder', this)">Master Folder</span>
                <span class="sub-tab" onclick="switchSubTab('collaboration', this)">Collaboration</span>
            </div>

            {{-- ══════════════════════════════
                 MASTER FOLDER TAB
            ══════════════════════════════ --}}
            <div class="tab-content active" id="tab-master-folder">

                {{-- No course selected state --}}
                <div id="mf-no-course" class="folder-panel">
                    <div class="folder-empty">
                        📁 Please select a course above to view its master folder.
                    </div>
                </div>

                {{-- Course selected: folder contents --}}
                <div id="mf-contents" style="display:none;">
                    <div class="folder-panel">
                        <div class="folder-panel-header">
                            <span class="folder-icon">📁</span>
                            <div>
                                <div class="folder-title">Official Course Materials</div>
                                <div class="folder-sub">Read only access to master course folder</div>
                            </div>
                        </div>

                        {{-- File: Official Syllabus --}}
                        <div class="file-row">
                            <div class="file-left">
                                <span class="file-icon">📄</span>
                                <div>
                                    <div class="file-name">Official Syllabus</div>
                                    <div class="file-meta">PDF • v2.1 • Updated —</div>
                                </div>
                            </div>
                            <button class="btn-view" type="button">View</button>
                        </div>

                        {{-- File: Table of Specifications --}}
                        <div class="file-row">
                            <div class="file-left">
                                <span class="file-icon">📊</span>
                                <div>
                                    <div class="file-name">Table of Specifications</div>
                                    <div class="file-meta">Excel • v1.0 • Updated —</div>
                                </div>
                            </div>
                            <button class="btn-view" type="button">View</button>
                        </div>

                        {{-- File: Exam Item Bank --}}
                        <div class="file-row">
                            <div class="file-left">
                                <span class="file-icon">📝</span>
                                <div>
                                    <div class="file-name">Exam Item Bank</div>
                                    <div class="file-meta">Word • v2.0 • Updated —</div>
                                </div>
                            </div>
                            <button class="btn-view" type="button">View</button>
                        </div>

                        {{-- File: Core Learning Materials --}}
                        <div class="file-row">
                            <div class="file-left">
                                <span class="file-icon">📚</span>
                                <div>
                                    <div class="file-name">Core Learning Materials</div>
                                    <div class="file-meta">ZIP • v2.0 • Updated —</div>
                                </div>
                            </div>
                            <button class="btn-view-blue" type="button">Inline</button>
                        </div>

                    </div>
                </div>

            </div>{{-- end master-folder tab --}}

            {{-- ══════════════════════════════
                 COLLABORATION TAB
            ══════════════════════════════ --}}
            <div class="tab-content" id="tab-collaboration">

                {{-- No course selected --}}
                <div id="collab-no-course">
                    <div class="folder-panel">
                        <div class="folder-empty">
                            🤝 Please select a course above to start collaborating.
                        </div>
                    </div>
                </div>

                {{-- Course selected: collaboration editor --}}
                <div id="collab-contents" style="display:none;">
                    <div class="collab-layout">

                        {{-- Left: Document editor --}}
                        <div class="doc-panel">
                            <div class="doc-toolbar">
                                <input class="doc-title-input" type="text" value="Lesson Plan – Week 1" id="doc-title">
                                <div class="toolbar-actions">
                                    <button class="toolbar-btn primary" type="button">
                                        💾 Save
                                    </button>
                                    <button class="toolbar-btn" type="button">
                                        ↗ Share
                                    </button>
                                    <button class="toolbar-btn" type="button">
                                        🕐 Version History
                                    </button>
                                    <button class="toolbar-btn" type="button">
                                        ⬆ Export
                                    </button>
                                </div>
                            </div>
                            <div class="doc-meta">0 others editing • No recent Section: Add your drawings</div>

                            <div class="doc-body" id="doc-body" contenteditable="true">
                                <h3>I. Course Information</h3>
                                <p>Course Code: —<br>
                                Course: —<br>
                                Outcome: —<br>
                                Semester: —</p>

                                <h3>II. Learning Outcomes</h3>
                                <ul>
                                    <li>—</li>
                                    <li>—</li>
                                    <li>—</li>
                                </ul>

                                <h3>III. Teaching Strategies</h3>
                                <p>—</p>
                            </div>
                        </div>

                        {{-- Right: Outline / Comments / Template --}}
                        <div class="side-panel">
                            <div class="side-tabs">
                                <div class="side-tab active" onclick="switchSideTab('outline', this)">Outline</div>
                                <div class="side-tab" onclick="switchSideTab('comments', this)">Comments</div>
                                <div class="side-tab" onclick="switchSideTab('template', this)">Template</div>
                            </div>

                            {{-- Outline --}}
                            <div class="side-tab-content active" id="side-outline">
                                <div class="outline-item">I. Course Information</div>
                                <div class="outline-item">II. Learning Outcomes</div>
                                <div class="outline-item">III. Teaching Strategies</div>
                            </div>

                            {{-- Comments --}}
                            <div class="side-tab-content" id="side-comments">
                                <div class="side-empty">No comments yet.</div>
                            </div>

                            {{-- Template --}}
                            <div class="side-tab-content" id="side-template">
                                <div class="side-empty">No templates applied.</div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>{{-- end collaboration tab --}}

        </div>{{-- end content --}}
    </div>{{-- end main --}}

    <script>
        // ── Course change handler ──
        function onCourseChange() {
            var val = document.getElementById('course-select').value;
            var hasCourse = val !== '';

            // Master folder
            document.getElementById('mf-no-course').style.display   = hasCourse ? 'none'  : 'block';
            document.getElementById('mf-contents').style.display     = hasCourse ? 'block' : 'none';

            // Collaboration
            document.getElementById('collab-no-course').style.display   = hasCourse ? 'none'  : 'block';
            document.getElementById('collab-contents').style.display     = hasCourse ? 'block' : 'none';
        }

        // ── Sub-tab switcher (Master Folder / Collaboration) ──
        function switchSubTab(tabName, el) {
            document.querySelectorAll('.sub-tab').forEach(function(t) {
                t.classList.remove('active');
            });
            el.classList.add('active');

            document.querySelectorAll('.tab-content').forEach(function(c) {
                c.classList.remove('active');
            });
            document.getElementById('tab-' + tabName).classList.add('active');
        }

        // ── Side panel tab switcher (Outline / Comments / Template) ──
        function switchSideTab(tabName, el) {
            document.querySelectorAll('.side-tab').forEach(function(t) {
                t.classList.remove('active');
            });
            el.classList.add('active');

            document.querySelectorAll('.side-tab-content').forEach(function(c) {
                c.classList.remove('active');
            });
            document.getElementById('side-' + tabName).classList.add('active');
        }

        // ── Logout ──
        function handleLogout() {
            window.location.href = '{{ url("/login") }}';
        }
    </script>

</body>
</html>