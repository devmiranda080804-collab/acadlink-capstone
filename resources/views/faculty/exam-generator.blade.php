<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Generator – CBMA System</title>
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
        .content { flex: 1; overflow-y: auto; padding: 24px 28px; }

        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 3px; }
        .page-sub { font-size: 11.5px; color: #888; }

        .page-header-right { display: flex; gap: 8px; }

        .btn-item-bank {
            background: #fff;
            border: 1px solid #ccc;
            color: #333;
            font-size: 12.5px;
            padding: 7px 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn-item-bank:hover { background: #f0f0f0; }

        .btn-new-exam {
            background: #0f2557;
            color: #fff;
            border: none;
            font-size: 12.5px;
            font-weight: 600;
            padding: 7px 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn-new-exam:hover { background: #1a3a7a; }

        /* Tabs */
        .tabs {
            display: flex;
            border-bottom: 2px solid #e0e0e0;
            margin-bottom: 20px;
        }

        .tab-item {
            padding: 8px 18px;
            font-size: 13px;
            color: #666;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: color 0.15s;
            user-select: none;
        }

        .tab-item:hover { color: #0f2557; }

        .tab-item.active {
            color: #0f2557;
            font-weight: 700;
            border-bottom: 2px solid #0f2557;
        }

        /* Tab content panels */
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        /* Two-column layout (TOS Generator tab) */
        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; align-items: start; }

        .panel {
            background: #fff;
            border: 1px solid #e4e4e4;
            border-radius: 8px;
            padding: 18px 20px;
        }

        .panel-title { font-size: 13px; font-weight: 700; color: #1a1a2e; margin-bottom: 16px; }

        .form-group { margin-bottom: 14px; }

        .form-group label {
            display: block;
            font-size: 11.5px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .form-group select,
        .form-group input {
            width: 100%;
            height: 34px;
            padding: 0 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 12px;
            color: #333;
            background: #fff;
            outline: none;
            appearance: none;
            -webkit-appearance: none;
            transition: border-color 0.15s;
        }

        .form-group select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%23666' d='M5 7L0 2h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            cursor: pointer;
        }

        .form-group select:focus,
        .form-group input:focus { border-color: #0f2557; }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px; }
        .form-row .form-group { margin-bottom: 0; }

        .btn-generate {
            width: 100%;
            height: 36px;
            background: #0f2557;
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 4px;
            transition: background 0.15s;
        }

        .btn-generate:hover { background: #1a3a7a; }

        /* OBE Data */
        .obe-section {
            background: #fff;
            border: 1px solid #e4e4e4;
            border-radius: 8px;
            padding: 16px 20px;
            margin-top: 14px;
        }

        .obe-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
        .obe-title { font-size: 13px; font-weight: 700; color: #1a1a2e; }

        .btn-sync {
            display: flex;
            align-items: center;
            gap: 5px;
            background: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 5px 12px;
            font-size: 12px;
            color: #444;
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn-sync:hover { background: #ebebeb; }

        .obe-field-label { font-size: 11.5px; color: #888; margin-bottom: 6px; }

        .obe-value-row { display: flex; align-items: center; gap: 10px; }

        .obe-value-box {
            width: 90px;
            height: 34px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background: #f9f9f9;
            display: flex;
            align-items: center;
            padding: 0 10px;
            font-size: 13px;
            font-weight: 600;
            color: #333;
        }

        .obe-auto-label { font-size: 11px; color: #aaa; }
        .obe-hint { font-size: 10.5px; color: #aaa; margin-top: 8px; line-height: 1.5; }

        /* Right panel: Generated TOS */
        .tos-panel-title { font-size: 13px; font-weight: 700; color: #1a1a2e; margin-bottom: 14px; }

        .tos-empty {
            text-align: center;
            padding: 50px 10px;
            color: #bbb;
            font-size: 12.5px;
        }

        .tos-empty .empty-icon { font-size: 32px; opacity: 0.3; margin-bottom: 8px; }

        /* Generic empty for other tabs */
        .tab-empty {
            text-align: center;
            padding: 60px 10px;
            color: #bbb;
            font-size: 13px;
            background: #fff;
            border: 1px solid #e4e4e4;
            border-radius: 8px;
        }

        .tab-empty .empty-icon { font-size: 34px; opacity: 0.3; margin-bottom: 10px; }

        /* ═══════════════════ EXAM BUILDER ═══════════════════ */
        .eb-form-bar {
            background: #fff; border: 1px solid #e4e4e4; border-radius: 8px;
            padding: 14px 18px; display: flex; align-items: flex-end; gap: 12px;
            flex-wrap: wrap; margin-bottom: 12px;
        }

        .eb-form-group { display: flex; flex-direction: column; gap: 4px; }
        .eb-form-group label { font-size: 11px; font-weight: 700; color: #555; }
        .eb-form-group select {
            height: 32px; padding: 0 28px 0 8px; border: 1px solid #ccc;
            border-radius: 5px; font-size: 12px; color: #333; background: #fff;
            outline: none; appearance: none; -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%23666' d='M5 7L0 2h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 8px center;
            cursor: pointer; min-width: 160px;
        }

        .eb-btn-group { display: flex; gap: 8px; align-items: flex-end; margin-left: auto; }

        .btn-eb-save {
            background: #0f2557; color: #fff; border: none; border-radius: 5px;
            font-size: 12px; font-weight: 600; padding: 7px 16px; cursor: pointer;
            transition: background 0.15s;
        }
        .btn-eb-save:hover { background: #1a3a7a; }

        .btn-eb-preview {
            background: #fff; color: #333; border: 1px solid #ccc; border-radius: 5px;
            font-size: 12px; font-weight: 600; padding: 7px 16px; cursor: pointer;
            transition: background 0.15s;
        }
        .btn-eb-preview:hover { background: #f5f5f5; }

        /* Sections bar */
        .eb-sections-bar {
            background: #fff; border: 1px solid #e4e4e4; border-radius: 8px;
            padding: 10px 18px; display: flex; align-items: center; gap: 16px;
            margin-bottom: 12px; flex-wrap: wrap;
        }

        .eb-sections-label { font-size: 11.5px; font-weight: 700; color: #555; white-space: nowrap; }

        .eb-section-link {
            font-size: 12px; color: #0f2557; cursor: pointer; text-decoration: underline;
            white-space: nowrap;
        }

        /* Section panel */
        .section-panel {
            background: #fff; border: 1px solid #e4e4e4; border-radius: 8px;
            padding: 18px 20px; margin-bottom: 12px;
        }

        .section-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 6px;
        }

        .section-title-input {
            font-size: 14px; font-weight: 700; color: #1a1a2e;
            border: none; outline: none; background: transparent;
            border-bottom: 1px dashed #ccc; min-width: 200px;
        }

        .section-inst {
            font-size: 11.5px; color: #888; margin-bottom: 14px;
        }

        .btn-del-section {
            background: none; border: none; color: #ddd; font-size: 14px;
            cursor: pointer; padding: 2px 6px; border-radius: 4px;
            transition: color 0.15s, background 0.15s;
        }
        .btn-del-section:hover { color: #ef4444; background: #fee2e2; }

        /* Question card */
        .question-card {
            border: 1px solid #e8e8e8; border-radius: 6px;
            padding: 14px 16px; margin-bottom: 10px; background: #fafafa;
        }

        .q-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 10px; }
        .q-num { font-size: 12.5px; font-weight: 600; color: #333; flex: 1; }
        .q-actions { display: flex; gap: 6px; }

        .btn-q-action {
            background: none; border: none; color: #aaa; font-size: 14px;
            cursor: pointer; padding: 2px 5px; border-radius: 3px;
            transition: color 0.15s, background 0.15s;
        }
        .btn-q-action:hover { color: #0f2557; background: #f0f4ff; }
        .btn-q-action.del:hover { color: #ef4444; background: #fee2e2; }

        .q-option {
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 6px; font-size: 12.5px; color: #333;
        }

        .q-option input[type="radio"],
        .q-option input[type="checkbox"] { flex-shrink: 0; }

        .q-footer {
            display: flex; align-items: center; gap: 12px;
            margin-top: 10px; font-size: 11.5px; color: #888;
        }

        .q-type-label {
            font-size: 11px; color: #555; background: #e8f0fe;
            padding: 2px 10px; border-radius: 12px; font-weight: 600;
        }

        .q-blooms-badge {
            font-size: 11px; color: #0f2557; background: #dbeafe;
            padding: 2px 10px; border-radius: 12px; font-weight: 600;
        }

        .q-pts {
            margin-left: auto; display: flex; align-items: center; gap: 5px;
            font-size: 11.5px; color: #555;
        }

        .q-pts input {
            width: 40px; height: 24px; text-align: center;
            border: 1px solid #ccc; border-radius: 4px; font-size: 12px;
        }

        /* Add Section / Add Question buttons */
        .btn-add-section {
            background: #fff; border: 1.5px dashed #ccc; color: #888;
            font-size: 12.5px; padding: 8px 24px; border-radius: 6px;
            cursor: pointer; transition: border-color 0.15s, color 0.15s;
        }
        .btn-add-section:hover { border-color: #0f2557; color: #0f2557; }

        .btn-add-question {
            width: 100%; background: #fff; border: 1.5px dashed #ccc; color: #888;
            font-size: 12px; padding: 8px; border-radius: 6px; margin-top: 8px;
            cursor: pointer; transition: border-color 0.15s, color 0.15s;
        }
        .btn-add-question:hover { border-color: #0f2557; color: #0f2557; }

        /* ═══════════════════ QUESTION TYPE MODAL ═══════════════════ */
        .qtype-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 999;
            align-items: center; justify-content: center;
        }
        .qtype-overlay.open { display: flex; }

        .qtype-modal {
            background: #fff; border-radius: 12px;
            padding: 28px 28px 20px; width: 520px; max-width: 96vw;
            box-shadow: 0 10px 40px rgba(0,0,0,0.25);
        }

        .qtype-title { font-size: 16px; font-weight: 700; color: #1a1a2e; margin-bottom: 20px; }

        .qtype-grid {
            display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
            margin-bottom: 20px;
        }

        .qtype-btn {
            display: flex; align-items: flex-start; gap: 10px;
            border: 1px solid #e0e0e0; border-radius: 8px;
            padding: 12px 14px; cursor: pointer; background: #fff;
            transition: border-color 0.15s, background 0.15s;
            text-align: left;
        }

        .qtype-btn:hover { border-color: #0f2557; background: #f0f4ff; }

        .qtype-btn .qt-icon {
            width: 32px; height: 32px; border-radius: 6px;
            background: #f0f0f0; display: flex; align-items: center;
            justify-content: center; font-size: 15px; flex-shrink: 0;
        }

        .qtype-btn .qt-text {}
        .qtype-btn .qt-name { font-size: 12.5px; font-weight: 700; color: #1a1a2e; margin-bottom: 2px; }
        .qtype-btn .qt-desc { font-size: 10.5px; color: #888; }

        .btn-qtype-cancel {
            width: 100%; background: #fff; border: 1px solid #ccc; color: #444;
            font-size: 13px; font-weight: 600; padding: 10px; border-radius: 6px;
            cursor: pointer; transition: background 0.15s;
        }
        .btn-qtype-cancel:hover { background: #f5f5f5; }

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
            <li class="active">
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

            <div class="page-header">
                <div>
                    <div class="page-title">Assessment Generator</div>
                    <div class="page-sub">TOS builder, exam generator, and item bank</div>
                </div>
                <div class="page-header-right">
                    <button class="btn-item-bank" type="button" onclick="switchTab('item-bank')">Item Bank</button>
                    <button class="btn-new-exam" type="button">+ New Exam</button>
                </div>
            </div>

            {{-- Tabs --}}
            <div class="tabs">
                <div class="tab-item active" data-tab="tos" onclick="switchTab('tos')">TOS Generator</div>
                <div class="tab-item" data-tab="exam-builder" onclick="switchTab('exam-builder')">Exam Builder</div>
                <div class="tab-item" data-tab="item-bank" onclick="switchTab('item-bank')">Item Bank</div>
            </div>

            {{-- TOS Generator Tab --}}
            <div class="tab-content active" id="tab-tos">
                <div class="two-col">

                    {{-- Left: Config + OBE --}}
                    <div>
                        <div class="panel">
                            <div class="panel-title">TOS Configuration</div>

                            <div class="form-group">
                                <label>Subject</label>
                                <select>
                                    <option value="" disabled selected>Select subject</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Examination Type</label>
                                <select>
                                    <option value="" disabled selected>Select type</option>
                                    <option>Prelim Examination</option>
                                    <option>Midterm Examination</option>
                                    <option>Semi-Final Examination</option>
                                    <option>Final Examination</option>
                                </select>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Total Items</label>
                                    <input type="number" placeholder="e.g. 50">
                                </div>
                                <div class="form-group">
                                    <label>Exam Duration (mins)</label>
                                    <input type="number" placeholder="e.g. 120">
                                </div>
                            </div>

                            <button class="btn-generate" type="button" onclick="generateTOS()">Generate TOS</button>
                        </div>

                        <div class="obe-section">
                            <div class="obe-header">
                                <span class="obe-title">OBE Data</span>
                                <button class="btn-sync" type="button">↻ Sync</button>
                            </div>
                            <div class="obe-field-label">Total Hours (from OBE)</div>
                            <div class="obe-value-row">
                                <div class="obe-value-box">—</div>
                                <span class="obe-auto-label">Auto-synced</span>
                            </div>
                            <p class="obe-hint">This value is automatically fetched from your OBE data and cannot be manually edited.</p>
                        </div>
                    </div>

                    {{-- Right: Generated TOS Table --}}
                    <div class="panel" id="tos-result-panel">
                        <div class="tos-panel-title">Generated Table of Specifications</div>
                        <div class="tos-empty" id="tos-empty">
                            <div class="empty-icon">📋</div>
                            No TOS generated yet.<br>Fill in the configuration and click <strong>Generate TOS</strong>.
                        </div>
                    </div>

                </div>
            </div>

            {{-- Exam Builder Tab --}}
            <div class="tab-content" id="tab-exam-builder">

                {{-- Top form bar --}}
                <div class="eb-form-bar">
                    <div class="eb-form-group">
                        <label>Exam Title</label>
                        <select id="eb-title">
                            <option value="" disabled selected>Select exam</option>
                            <option>Midterm Examination</option>
                            <option>Prelim Examination</option>
                            <option>Semi-Final Examination</option>
                            <option>Final Examination</option>
                        </select>
                    </div>
                    <div class="eb-form-group">
                        <label>Subject</label>
                        <select id="eb-subject">
                            <option value="" disabled selected>Select subject</option>
                            <option>Financial Accounting and Reporting</option>
                            <option>Managerial Economics</option>
                            <option>Intermediate Accounting 1</option>
                            <option>Cost Accounting and Control</option>
                            <option>Business Laws and Regulations</option>
                        </select>
                    </div>
                    <div class="eb-form-group">
                        <label>Exam Type</label>
                        <select id="eb-type">
                            <option value="" disabled selected>Select type</option>
                            <option>Midterm Examination</option>
                            <option>Prelim Examination</option>
                            <option>Final Examination</option>
                        </select>
                    </div>
                    <div class="eb-form-group">
                        <label>Blooms Level</label>
                        <select id="eb-blooms">
                            <option value="" disabled selected>Add content</option>
                            <option>Remember</option>
                            <option>Understand</option>
                            <option>Apply</option>
                            <option>Analyze</option>
                            <option>Evaluate</option>
                            <option>Create</option>
                        </select>
                    </div>
                    <div class="eb-btn-group">
                        <button class="btn-eb-save" onclick="saveExam()">💾 Save</button>
                        <button class="btn-eb-preview" onclick="previewExam()">👁 Preview</button>
                    </div>
                </div>

                {{-- Sections list + Add Section --}}
                <div class="eb-sections-bar" id="eb-sections-bar">
                    <span class="eb-sections-label">Sections</span>
                    <div id="eb-section-links"></div>
                </div>

                {{-- Sections content --}}
                <div id="eb-sections-content"></div>

                {{-- Add Section button --}}
                <div style="text-align:center;margin-top:10px;">
                    <button class="btn-add-section" onclick="addSection()">+ Add Section</button>
                </div>

            </div>

            {{-- Item Bank Tab --}}
            <div class="tab-content" id="tab-item-bank">
                <div class="tab-empty">
                    <div class="empty-icon">🗃️</div>
                    Your item bank is empty.<br>Add questions to build your item bank.
                </div>
            </div>

        </div>
    </div>

    {{-- ════════════ QUESTION TYPE MODAL ════════════ --}}
    <div class="qtype-overlay" id="qtype-overlay">
        <div class="qtype-modal">
            <div class="qtype-title">Choose Question Type</div>
            <div class="qtype-grid">
                <button class="qtype-btn" onclick="addQuestion('mc-single')">
                    <div class="qt-icon">◎</div>
                    <div class="qt-text">
                        <div class="qt-name">Multiple Choice (Single)</div>
                        <div class="qt-desc">Single correct answer from multiple options</div>
                    </div>
                </button>
                <button class="qtype-btn" onclick="addQuestion('fill-blank')">
                    <div class="qt-icon">T</div>
                    <div class="qt-text">
                        <div class="qt-name">Fill in the Blank</div>
                        <div class="qt-desc">Each blank filled with frames</div>
                    </div>
                </button>
                <button class="qtype-btn" onclick="addQuestion('true-false')">
                    <div class="qt-icon">✓</div>
                    <div class="qt-text">
                        <div class="qt-name">True or False</div>
                        <div class="qt-desc">Single true/false question</div>
                    </div>
                </button>
                <button class="qtype-btn" onclick="addQuestion('short-answer')">
                    <div class="qt-icon">T</div>
                    <div class="qt-text">
                        <div class="qt-name">Short Answer / Essay</div>
                        <div class="qt-desc">Longform text answer</div>
                    </div>
                </button>
                <button class="qtype-btn" onclick="addQuestion('matching')">
                    <div class="qt-icon">⇄</div>
                    <div class="qt-text">
                        <div class="qt-name">Matching Type</div>
                        <div class="qt-desc">Matching items from two columns</div>
                    </div>
                </button>
                <button class="qtype-btn" onclick="addQuestion('ordering')">
                    <div class="qt-icon">↕</div>
                    <div class="qt-text">
                        <div class="qt-name">Ordering / Sequencing</div>
                        <div class="qt-desc">Arrange items in order</div>
                    </div>
                </button>
                <button class="qtype-btn" onclick="addQuestion('enumeration')">
                    <div class="qt-icon">≡</div>
                    <div class="qt-text">
                        <div class="qt-name">Enumeration</div>
                        <div class="qt-desc">List of answers</div>
                    </div>
                </button>
                <button class="qtype-btn" onclick="addQuestion('identification')">
                    <div class="qt-icon">◎</div>
                    <div class="qt-text">
                        <div class="qt-name">Identification</div>
                        <div class="qt-desc">Identify the correct term or concept</div>
                    </div>
                </button>
                <button class="qtype-btn" onclick="addQuestion('diagram')">
                    <div class="qt-icon">🖼</div>
                    <div class="qt-text">
                        <div class="qt-name">Label the Diagram</div>
                        <div class="qt-desc">Single text, label with frames</div>
                    </div>
                </button>
            </div>
            <button class="btn-qtype-cancel" onclick="closeQTypeModal()">Cancel</button>
        </div>
    </div>

    <script>
        // ── Tab switching ──
        function switchTab(tabName) {
            document.querySelectorAll('.tab-item').forEach(function(t) {
                t.classList.toggle('active', t.dataset.tab === tabName);
            });
            document.querySelectorAll('.tab-content').forEach(function(c) {
                c.classList.remove('active');
            });
            var target = document.getElementById('tab-' + tabName);
            if (target) target.classList.add('active');
        }

        // ── TOS Generator ──
        function generateTOS() {
            var subject  = document.querySelector('#tab-tos select:nth-of-type(1)').value;
            var examType = document.querySelector('#tab-tos select:nth-of-type(2)').value;
            var total    = document.querySelector('#tab-tos input[type="number"]:nth-of-type(1)').value;
            var panel    = document.getElementById('tos-result-panel');

            if (!subject || !examType || !total) {
                panel.innerHTML =
                    '<div class="tos-panel-title">Generated Table of Specifications</div>' +
                    '<div class="tos-empty" style="color:#ef4444;"><div class="empty-icon">⚠️</div>Please fill in all fields before generating the TOS.</div>';
                return;
            }

            panel.innerHTML =
                '<div class="tos-panel-title">Generated Table of Specifications</div>' +
                '<div style="overflow-x:auto;"><table style="width:100%;border-collapse:collapse;font-size:11.5px;">' +
                '<thead>' +
                '<tr style="background:#f5f5f5;">' +
                '<th style="padding:8px;border:1px solid #e0e0e0;text-align:left;">Topic</th>' +
                '<th style="padding:8px;border:1px solid #e0e0e0;">No. of Hours</th>' +
                '<th style="padding:8px;border:1px solid #e0e0e0;">% Weight</th>' +
                '<th colspan="6" style="padding:8px;border:1px solid #e0e0e0;text-align:center;">Bloom\'s Taxonomy Level</th>' +
                '<th style="padding:8px;border:1px solid #e0e0e0;">Total</th></tr>' +
                '<tr style="background:#fafafa;">' +
                '<th style="padding:6px 8px;border:1px solid #e0e0e0;"></th>' +
                '<th style="padding:6px 8px;border:1px solid #e0e0e0;"></th>' +
                '<th style="padding:6px 8px;border:1px solid #e0e0e0;"></th>' +
                '<th style="padding:6px 8px;border:1px solid #e0e0e0;text-align:center;font-size:11px;">R</th>' +
                '<th style="padding:6px 8px;border:1px solid #e0e0e0;text-align:center;font-size:11px;">U</th>' +
                '<th style="padding:6px 8px;border:1px solid #e0e0e0;text-align:center;font-size:11px;">Ap</th>' +
                '<th style="padding:6px 8px;border:1px solid #e0e0e0;text-align:center;font-size:11px;">An</th>' +
                '<th style="padding:6px 8px;border:1px solid #e0e0e0;text-align:center;font-size:11px;">E</th>' +
                '<th style="padding:6px 8px;border:1px solid #e0e0e0;text-align:center;font-size:11px;">C</th>' +
                '<th style="padding:6px 8px;border:1px solid #e0e0e0;"></th></tr>' +
                '</thead><tbody>' +
                '<tr><td colspan="10" style="padding:20px;text-align:center;color:#bbb;border:1px solid #e0e0e0;">No topics added yet.</td></tr>' +
                '</tbody><tfoot>' +
                '<tr style="background:#f5f5f5;font-weight:700;">' +
                '<td style="padding:8px;border:1px solid #e0e0e0;">TOTAL</td>' +
                '<td style="padding:8px;border:1px solid #e0e0e0;text-align:center;">—</td>' +
                '<td style="padding:8px;border:1px solid #e0e0e0;text-align:center;">100%</td>' +
                '<td colspan="6" style="padding:8px;border:1px solid #e0e0e0;"></td>' +
                '<td style="padding:8px;border:1px solid #e0e0e0;text-align:center;">' + total + '</td>' +
                '</tr></tfoot></table></div>';
        }

        // ══════════════════════════════
        // EXAM BUILDER
        // ══════════════════════════════
        var ebSections  = [];   // [{id, title, instruction, questions:[]}]
        var currentSectionId = null;

        var QTYPE_LABELS = {
            'mc-single':    'Multiple Choice (Single)',
            'fill-blank':   'Fill in the Blank',
            'true-false':   'True or False',
            'short-answer': 'Short Answer / Essay',
            'matching':     'Matching Type',
            'ordering':     'Ordering / Sequencing',
            'enumeration':  'Enumeration',
            'identification':'Identification',
            'diagram':      'Label the Diagram'
        };

        var QTYPE_DEFAULTS = {
            'mc-single':    ['O(1)', 'O(n)', 'O(log n)', 'O(n²)'],
            'true-false':   ['True', 'False'],
            'fill-blank':   [],
            'short-answer': [],
            'matching':     ['A. Item 1', 'B. Item 2', 'C. Item 3'],
            'ordering':     ['Step 1', 'Step 2', 'Step 3'],
            'enumeration':  ['Item 1', 'Item 2', 'Item 3'],
            'identification':[],
            'diagram':      []
        };

        function renderEB() {
            renderSectionsBar();
            renderSectionsContent();
        }

        function renderSectionsBar() {
            var bar = document.getElementById('eb-section-links');
            bar.innerHTML = '';
            ebSections.forEach(function(sec) {
                var span = document.createElement('span');
                span.className = 'eb-section-link';
                span.textContent = sec.title;
                span.onclick = function() {
                    document.getElementById('sec-' + sec.id).scrollIntoView({ behavior: 'smooth' });
                };
                bar.appendChild(span);
            });
        }

        function renderSectionsContent() {
            var container = document.getElementById('eb-sections-content');
            container.innerHTML = '';
            ebSections.forEach(function(sec) {
                var div = document.createElement('div');
                div.className = 'section-panel';
                div.id = 'sec-' + sec.id;

                var qHTML = '';
                sec.questions.forEach(function(q, qi) {
                    qHTML += buildQuestionHTML(sec.id, qi, q);
                });

                div.innerHTML =
                    '<div class="section-header">' +
                        '<input class="section-title-input" value="' + sec.title + '" ' +
                            'onchange="updateSectionTitle(\'' + sec.id + '\', this.value)">' +
                        '<button class="btn-del-section" onclick="deleteSection(\'' + sec.id + '\')" title="Delete section">🗑</button>' +
                    '</div>' +
                    '<div class="section-inst">' + sec.instruction + '</div>' +
                    '<div id="questions-' + sec.id + '">' + qHTML + '</div>' +
                    '<button class="btn-add-question" onclick="openQTypeModal(\'' + sec.id + '\')">+ Add Question</button>';

                container.appendChild(div);
            });
        }

        function buildQuestionHTML(secId, qi, q) {
            var num = qi + 1;
            var optHTML = '';

            if (q.type === 'mc-single' || q.type === 'matching' || q.type === 'ordering' || q.type === 'enumeration') {
                q.options.forEach(function(opt) {
                    optHTML += '<div class="q-option"><input type="radio" disabled><span>' + opt + '</span></div>';
                });
            } else if (q.type === 'true-false') {
                optHTML = '<div class="q-option"><input type="radio" disabled><span>True</span></div>' +
                          '<div class="q-option"><input type="radio" disabled><span>False</span></div>';
            } else if (q.type === 'fill-blank' || q.type === 'identification') {
                optHTML = '<div style="border-bottom:1px solid #ccc;height:24px;margin:8px 0;width:200px;"></div>';
            } else if (q.type === 'short-answer') {
                optHTML = '<div style="border:1px solid #e0e0e0;border-radius:4px;height:50px;margin:8px 0;background:#fafafa;"></div>';
            } else if (q.type === 'diagram') {
                optHTML = '<div style="border:1px dashed #ccc;border-radius:6px;height:80px;display:flex;align-items:center;justify-content:center;color:#bbb;font-size:12px;margin:8px 0;">Diagram area</div>';
            }

            return '<div class="question-card" id="q-' + secId + '-' + qi + '">' +
                '<div class="q-header">' +
                    '<div class="q-num">' + num + '. ' + q.text + '</div>' +
                    '<div class="q-actions">' +
                        '<button class="btn-q-action" onclick="copyQuestion(\'' + secId + '\',' + qi + ')" title="Copy">⧉</button>' +
                        '<button class="btn-q-action del" onclick="deleteQuestion(\'' + secId + '\',' + qi + ')" title="Delete">🗑</button>' +
                    '</div>' +
                '</div>' +
                optHTML +
                '<div class="q-footer">' +
                    '<span class="q-type-label">' + QTYPE_LABELS[q.type] + '</span>' +
                    '<span class="q-blooms-badge">' + (q.blooms || 'Remember') + '</span>' +
                    '<div class="q-pts"><input type="number" value="' + (q.pts || 1) + '" min="1" ' +
                        'onchange="updatePts(\'' + secId + '\',' + qi + ',this.value)" style="width:40px;height:24px;text-align:center;border:1px solid #ccc;border-radius:4px;font-size:12px;"> pts</div>' +
                '</div>' +
            '</div>';
        }

        // Section actions
        function addSection() {
            var id    = 'sec' + Date.now();
            var num   = ebSections.length + 1;
            var types = ['Multiple Choice', 'True or False', 'Identification', 'Enumeration', 'Essay'];
            var type  = types[Math.min(num - 1, types.length - 1)];
            var instr = num === 1
                ? 'Choose the letter of the best answer. Write your answer on the space provided.'
                : 'Write TRUE if the statement is correct, otherwise write FALSE.';

            ebSections.push({ id: id, title: 'Test ' + num + ' - ' + type, instruction: instr, questions: [] });
            renderEB();
        }

        function deleteSection(secId) {
            if (!confirm('Delete this section?')) return;
            ebSections = ebSections.filter(function(s) { return s.id !== secId; });
            renderEB();
        }

        function updateSectionTitle(secId, val) {
            var sec = ebSections.find(function(s) { return s.id === secId; });
            if (sec) { sec.title = val; renderSectionsBar(); }
        }

        // Question Type Modal
        function openQTypeModal(secId) {
            currentSectionId = secId;
            document.getElementById('qtype-overlay').classList.add('open');
        }

        function closeQTypeModal() {
            document.getElementById('qtype-overlay').classList.remove('open');
        }

        document.getElementById('qtype-overlay').addEventListener('click', function(e) {
            if (e.target === this) closeQTypeModal();
        });

        function addQuestion(type) {
            closeQTypeModal();
            var sec = ebSections.find(function(s) { return s.id === currentSectionId; });
            if (!sec) return;

            var blooms = document.getElementById('eb-blooms').value || 'Remember';
            var defaultTexts = {
                'mc-single':    'What is the time complexity of accessing an element in an array by index?',
                'true-false':   'A binary tree can have at most two children per node.',
                'fill-blank':   'The process of ________ involves converting data into information.',
                'short-answer': 'Explain the concept of recursion in your own words.',
                'matching':     'Match each term with its correct definition.',
                'ordering':     'Arrange the following steps in the correct order.',
                'enumeration':  'Enumerate the following items.',
                'identification':'Identify the term being described.',
                'diagram':      'Label the parts of the diagram below.'
            };

            sec.questions.push({
                type:    type,
                text:    defaultTexts[type] || 'Question text here.',
                options: QTYPE_DEFAULTS[type] ? QTYPE_DEFAULTS[type].slice() : [],
                blooms:  blooms,
                pts:     1
            });

            renderEB();
        }

        function deleteQuestion(secId, qi) {
            var sec = ebSections.find(function(s) { return s.id === secId; });
            if (sec) { sec.questions.splice(qi, 1); renderEB(); }
        }

        function copyQuestion(secId, qi) {
            var sec = ebSections.find(function(s) { return s.id === secId; });
            if (sec) {
                var copy = JSON.parse(JSON.stringify(sec.questions[qi]));
                sec.questions.splice(qi + 1, 0, copy);
                renderEB();
            }
        }

        function updatePts(secId, qi, val) {
            var sec = ebSections.find(function(s) { return s.id === secId; });
            if (sec && sec.questions[qi]) sec.questions[qi].pts = parseInt(val) || 1;
        }

        function saveExam() {
            alert('Exam saved successfully! (Frontend prototype — data is stored in memory only.)');
        }

        function previewExam() {
            if (ebSections.length === 0) {
                alert('No sections to preview. Add sections and questions first.');
                return;
            }
            var total = 0;
            var summary = ebSections.map(function(sec) {
                var q = sec.questions.length;
                var pts = sec.questions.reduce(function(s, q) { return s + (q.pts || 1); }, 0);
                total += pts;
                return sec.title + ': ' + q + ' question(s), ' + pts + ' pts';
            }).join('\n');
            alert('EXAM PREVIEW\n\n' + summary + '\n\nTotal Points: ' + total);
        }

        function handleLogout() {
            window.location.href = '{{ url("/login") }}';
        }
    </script>

</body>
</html>