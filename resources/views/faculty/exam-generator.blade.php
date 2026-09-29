<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        .nav-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 16px; height: 16px; padding: 0 4px; margin-left: auto; background: #ef4444; color: #fff; font-size: 10px; font-weight: 700; border-radius: 999px; }

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

        .eb-form-group input[type="text"] {
            height: 32px; padding: 0 8px; border: 1px solid #ccc;
            border-radius: 5px; font-size: 12px; color: #333; background: #fff;
            outline: none; min-width: 160px;
        }
        .eb-form-group input[type="text"]:disabled { background: #f5f5f5; color: #999; }
        .eb-readonly {
            height: 32px; padding: 0 8px; border: 1px solid #eee; border-radius: 5px;
            font-size: 12px; color: #666; background: #f9f9f9; min-width: 160px;
            display: flex; align-items: center;
        }
        .eb-btn-group button:disabled, .btn-add-section:disabled { opacity: 0.5; cursor: not-allowed; }

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

        /* ═══════════════════ GENERIC MODAL (New Exam) ═══════════════════ */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 999; align-items: center; justify-content: center; }
        .modal-overlay.open { display: flex; }
        .modal { background: #fff; border-radius: 10px; padding: 24px 26px; width: 420px; max-width: 95vw; box-shadow: 0 8px 32px rgba(0,0,0,0.25); }
        .modal-title { font-size: 15px; font-weight: 700; color: #1a1a2e; margin-bottom: 16px; }
        .modal-row { display: flex; gap: 10px; }
        .modal-row .modal-field { flex: 1; }
        .modal-field { margin-bottom: 13px; }
        .modal-field label { display: block; font-size: 11.5px; font-weight: 700; color: #333; margin-bottom: 4px; }
        .modal-field select, .modal-field input {
            width: 100%; padding: 8px 10px; border: 1px solid #ccc; border-radius: 5px;
            font-size: 12.5px; color: #333; outline: none;
        }
        .modal-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 18px; }
        .btn-cancel { background: #fff; border: 1px solid #ccc; color: #444; font-size: 12.5px; font-weight: 600; padding: 8px 18px; border-radius: 5px; cursor: pointer; }
        .btn-cancel:hover { background: #f5f5f5; }
        .btn-save { background: #0f2557; color: #fff; border: none; font-size: 12.5px; font-weight: 600; padding: 8px 20px; border-radius: 5px; cursor: pointer; }
        .btn-save:hover { background: #1a3a7a; }

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
        @if($navPermissions['dashboard'] ?? true)
        <li class="{{ request()->is('faculty/dashboard') ? 'active' : '' }}">
            <a href="{{ url('/faculty/dashboard') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
        </li>
        @endif
        @if($navPermissions['my-template'] ?? true)
        <li class="{{ request()->is('faculty/my-template*') ? 'active' : '' }}">
            <a href="{{ url('/faculty/my-template') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                Templates
            </a>
        </li>
        @endif
        @if($navPermissions['exam-generator'] ?? true)
        <li class="{{ request()->is('faculty/exam-generator*') ? 'active' : '' }}">
            <a href="{{ url('/faculty/exam-generator') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                Exam Generator
            </a>
        </li>
        @endif
        @if($navPermissions['shared-library'] ?? true)
        <li class="{{ request()->is('faculty/shared-library*') ? 'active' : '' }}">
            <a href="{{ url('/faculty/shared-library') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
                Shared Library
            </a>
        </li>
        @endif
        @if($navPermissions['course-coordination'] ?? true)
        <li class="{{ request()->is('faculty/course-coordination*') ? 'active' : '' }}">
            <a href="{{ url('/faculty/course-coordination') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                Course Coordination
            </a>
        </li>
        @endif
        @if($navPermissions['analytics'] ?? true)
        <li class="{{ request()->is('faculty/analytics*') ? 'active' : '' }}">
            <a href="{{ url('/faculty/analytics') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                Analytics
            </a>
        </li>
        @endif
        @if($navPermissions['calendar'] ?? true)
        <li class="{{ request()->is('faculty/calendar*') ? 'active' : '' }}">
            <a href="{{ url('/faculty/calendar') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Calendar of Activities
            </a>
        </li>
        @endif
        @if($navPermissions['announcements'] ?? true)
        <li class="{{ request()->is('faculty/announcements*') ? 'active' : '' }}">
            <a href="{{ url('/faculty/announcements') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                Announcements
                @if(($unreadAnnouncementsCount ?? 0) > 0)
                    <span class="nav-badge">{{ $unreadAnnouncementsCount }}</span>
                @endif
            </a>
        </li>
        @endif
        @if($navPermissions['submissions'] ?? true)
        <li class="{{ request()->is('faculty/submissions*') ? 'active' : '' }}">
            <a href="{{ url('/faculty/submissions') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                Submissions and Deadline
            </a>
        </li>
        @endif
        @if($navPermissions['cms'] ?? true)
        <li class="{{ request()->is('faculty/cms*') ? 'active' : '' }}">
            <a href="{{ url('/faculty/cms') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                CMS
            </a>
        </li>
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

    {{-- ════════════ MAIN ════════════ --}}
    <div class="main">

        <div class="topnav">
            <span class="label">Current role</span>
            <div class="topnav-right">
                <span class="role-badge">Faculty</span>
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

            <div class="page-header">
                <div>
                    <div class="page-title">Assessment Generator</div>
                    <div class="page-sub">TOS builder, exam generator, and item bank</div>
                </div>
                <div class="page-header-right">
                    <button class="btn-item-bank" type="button" onclick="switchTab('item-bank')">Item Bank</button>
                    <button class="btn-new-exam" type="button" onclick="openNewExamModal()">+ New Exam</button>
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
                                <select id="tos-subject" onchange="onTosSubjectChange()">
                                    <option value="" disabled selected>Select subject</option>
                                    @foreach($assignments as $a)
                                        <option value="{{ $a->id }}">{{ $a->course->code }} — {{ $a->course->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Examination Type</label>
                                <select id="tos-period">
                                    <option value="" disabled selected>Select type</option>
                                    <option value="Prelim">Prelim Examination</option>
                                    <option value="Midterm">Midterm Examination</option>
                                    <option value="Final">Final Examination</option>
                                </select>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Total Items</label>
                                    <input type="number" id="tos-total-items" min="1" placeholder="e.g. 50">
                                </div>
                                <div class="form-group">
                                    <label>Exam Duration (mins)</label>
                                    <input type="number" id="tos-duration" min="1" placeholder="e.g. 120">
                                </div>
                            </div>

                            <button class="btn-generate" type="button" onclick="generateTOS()">Generate TOS</button>
                        </div>

                        <div class="obe-section">
                            <div class="obe-header">
                                <span class="obe-title">OBE Data</span>
                            </div>
                            <div class="obe-field-label">Total Hours (from OBE)</div>
                            <div class="obe-value-row">
                                <div class="obe-value-box" id="tos-total-hours">—</div>
                                <span class="obe-auto-label">Auto-synced</span>
                            </div>
                            <p class="obe-hint">This value is automatically fetched from your OBTL topics & hours and cannot be manually edited.</p>
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
                        <label>Exam</label>
                        <select id="eb-exam-select" onchange="onExamSelectChange()">
                            <option value="" selected>Select exam...</option>
                            @foreach($exams as $ex)
                                <option value="{{ $ex->id }}">{{ $ex->programAssignment->course->code }} — {{ $ex->title }} ({{ $ex->grading_period }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="eb-form-group">
                        <label>Title</label>
                        <input type="text" id="eb-title-input" placeholder="e.g. Midterm Examination" disabled>
                    </div>
                    <div class="eb-form-group">
                        <label>Subject</label>
                        <div class="eb-readonly" id="eb-subject-label">—</div>
                    </div>
                    <div class="eb-form-group">
                        <label>Exam Type</label>
                        <div class="eb-readonly" id="eb-type-label">—</div>
                    </div>
                    <div class="eb-btn-group">
                        <button class="btn-eb-save" onclick="saveExam()" id="btn-save-exam" disabled>💾 Save</button>
                        <button class="btn-eb-preview" onclick="previewExam()" id="btn-preview-exam" disabled>👁 Preview</button>
                        <button class="btn-eb-preview" onclick="finalizeExam()" id="btn-finalize-exam" disabled>🔒 Finalize</button>
                    </div>
                </div>

                {{-- Live TOS progress: actual items added vs. the TOS Generator target --}}
                <div id="eb-tos-progress"></div>

                {{-- Sections list + Add Section --}}
                <div class="eb-sections-bar" id="eb-sections-bar">
                    <span class="eb-sections-label">Sections</span>
                    <div id="eb-section-links"></div>
                </div>

                {{-- Sections content --}}
                <div id="eb-sections-content"></div>
                <div class="tab-empty" id="eb-no-exam-notice">
                    <div class="empty-icon">📝</div>
                    Select an exam above, or use <strong>+ New Exam</strong> to start one.
                </div>

                {{-- Add Section button --}}
                <div style="text-align:center;margin-top:10px;">
                    <button class="btn-add-section" onclick="addSection()" id="btn-add-section" disabled>+ Add Section</button>
                </div>

            </div>

            {{-- Item Bank Tab --}}
            <div class="tab-content" id="tab-item-bank">
                <div class="form-group" style="max-width:360px;margin-bottom:16px;">
                    <label>Filter by Subject</label>
                    <select id="bank-course-select" onchange="loadItemBank()">
                        <option value="" disabled selected>Select subject</option>
                        @foreach($assignments->unique('course_id') as $a)
                            <option value="{{ $a->course_id }}">{{ $a->course->code }} — {{ $a->course->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="bank-items-container">
                    <div class="tab-empty">
                        <div class="empty-icon">🗃️</div>
                        Select a subject above to see its reusable items.
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ════════════ NEW EXAM MODAL ════════════ --}}
    <div class="modal-overlay" id="new-exam-overlay">
        <div class="modal">
            <div class="modal-title">New Exam</div>
            <div class="modal-field">
                <label>Subject <span style="color:#ef4444">*</span></label>
                <select id="ne-subject">
                    <option value="" disabled selected>Select subject</option>
                    @foreach($assignments as $a)
                        <option value="{{ $a->id }}">{{ $a->course->code }} — {{ $a->course->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-row">
                <div class="modal-field">
                    <label>Grading Period <span style="color:#ef4444">*</span></label>
                    <select id="ne-period">
                        <option value="Prelim">Prelim</option>
                        <option value="Midterm">Midterm</option>
                        <option value="Final">Final</option>
                    </select>
                </div>
                <div class="modal-field">
                    <label>Duration (mins)</label>
                    <input type="number" id="ne-duration" min="1" placeholder="e.g. 120">
                </div>
            </div>
            <div class="modal-field">
                <label>Title <span style="color:#ef4444">*</span></label>
                <input type="text" id="ne-title" placeholder="e.g. Midterm Examination" required>
            </div>
            <div class="modal-field">
                <label>Target Items <span style="font-size:10px;color:#999;">(optional — from TOS Generator)</span></label>
                <input type="number" id="ne-target-items" min="1" placeholder="e.g. 50">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeNewExamModal()">Cancel</button>
                <button type="button" class="btn-save" onclick="createExam()">Create</button>
            </div>
        </div>
    </div>

    {{-- ════════════ AI GENERATE QUESTIONS MODAL ════════════ --}}
    <div class="modal-overlay" id="ai-gen-overlay">
        <div class="modal">
            <div class="modal-title">🤖 Generate Questions with AI</div>
            <div class="modal-field">
                <label>Topic <span style="color:#ef4444">*</span></label>
                <select id="ai-gen-topic">
                    <option value="" disabled selected>Select topic</option>
                </select>
            </div>
            <div class="modal-row">
                <div class="modal-field">
                    <label>Question Type <span style="color:#ef4444">*</span></label>
                    <select id="ai-gen-type">
                        @foreach(\App\Services\QuestionGeneratorService::SUPPORTED_TYPES as $typeKey)
                            <option value="{{ $typeKey }}">{{ \App\Support\BloomLevels::TYPES[$typeKey]['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-field">
                    <label>How many?</label>
                    <input type="number" id="ai-gen-count" min="1" max="10" value="3">
                </div>
            </div>
            <div id="ai-gen-error" style="display:none;color:#ef4444;font-size:11.5px;margin-bottom:10px;"></div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeAiGenModal()">Cancel</button>
                <button type="button" class="btn-save" id="ai-gen-submit" onclick="submitAiGen()">Generate</button>
            </div>
        </div>
    </div>

    {{-- ════════════ EXAM PREVIEW MODAL ════════════ --}}
    <div class="modal-overlay" id="preview-overlay">
        <div class="modal" style="width:760px;max-height:85vh;overflow-y:auto;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                <div class="modal-title" style="margin:0;">Exam Preview</div>
                <button type="button" class="btn-cancel" onclick="closePreview()">Close</button>
            </div>
            <div id="preview-body"></div>
        </div>
    </div>

    {{-- ════════════ QUESTION TYPE MODAL ════════════ --}}
    <div class="qtype-overlay" id="qtype-overlay">
        <div class="qtype-modal">
            <div class="qtype-title" id="qtype-title">Choose Question Type</div>
            <div class="qtype-grid">
                @foreach(\App\Support\BloomLevels::TYPES as $typeKey => $t)
                    <button class="qtype-btn" onclick="addQuestion('{{ $typeKey }}')">
                        <div class="qt-icon">{{ $t['icon'] }}</div>
                        <div class="qt-text">
                            <div class="qt-name">{{ $t['label'] }}</div>
                            <div class="qt-desc">{{ $t['desc'] }}{{ $t['bloom'] ? ' — ' . $t['bloom'] . ' (' . $t['category'] . ')' : '' }}</div>
                        </div>
                    </button>
                @endforeach
            </div>
            <button class="btn-qtype-cancel" onclick="closeQTypeModal()">Cancel</button>
        </div>
    </div>

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        const QUESTION_TYPES = @json(\App\Support\BloomLevels::TYPES);
        const TOPICS_BY_ASSIGNMENT = @json($topicsByAssignment);
        const ASSIGNMENTS = @json($assignments->map(fn($a) => ['id' => $a->id, 'label' => $a->course->code . ' — ' . $a->course->title]));

        let currentExam = null;
        let tosTargetCache = null;
        let qtypeTarget = { secId: null, qi: null };

        // ── fetch helper ──
        async function api(url, opts) {
            opts = opts || {};
            opts.headers = Object.assign({ 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, opts.headers || {});
            if (opts.body) {
                opts.headers['Content-Type'] = 'application/json';
                opts.body = JSON.stringify(opts.body);
            }
            const res = await fetch(url, opts);
            if (!res.ok) {
                const err = await res.json().catch(function () { return {}; });
                throw new Error(err.message || ('Request failed (' + res.status + ')'));
            }
            return res.status === 204 ? null : res.json();
        }

        function escapeHtml(s) {
            const d = document.createElement('div');
            d.textContent = s == null ? '' : String(s);
            return d.innerHTML;
        }
        function escapeAttr(s) { return escapeHtml(s).replace(/"/g, '&quot;'); }

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

        // ══════════════════════════════
        // TOS GENERATOR
        // ══════════════════════════════
        function onTosSubjectChange() { /* topics are fetched fresh on Generate */ }

        async function generateTOS() {
            var assignmentId = document.getElementById('tos-subject').value;
            var period       = document.getElementById('tos-period').value;
            var totalItems   = parseInt(document.getElementById('tos-total-items').value);
            var panel        = document.getElementById('tos-result-panel');

            if (!assignmentId || !period || !totalItems) {
                panel.innerHTML =
                    '<div class="tos-panel-title">Generated Table of Specifications</div>' +
                    '<div class="tos-empty" style="color:#ef4444;"><div class="empty-icon">⚠️</div>Please fill in all fields before generating the TOS.</div>';
                return;
            }

            var result;
            try {
                result = await api('/faculty/exam-generator/tos-target', {
                    method: 'POST',
                    body: { program_assignment_id: assignmentId, grading_period: period, total_items: totalItems }
                });
            } catch (e) {
                panel.innerHTML =
                    '<div class="tos-panel-title">Generated Table of Specifications</div>' +
                    '<div class="tos-empty" style="color:#ef4444;"><div class="empty-icon">⚠️</div>' + escapeHtml(e.message) + '</div>';
                return;
            }

            tosTargetCache = { assignmentId: assignmentId, period: period, totalItems: totalItems, result: result };
            document.getElementById('tos-total-hours').textContent = result.total_hours + ' hrs';

            if (!result.topics || result.topics.length === 0) {
                panel.innerHTML =
                    '<div class="tos-panel-title">Generated Table of Specifications</div>' +
                    '<div class="tos-empty"><div class="empty-icon">⚠️</div>No OBTL topics have been added yet for this subject\'s ' + period + ' period. Ask your Program Head to add them under Course Oversight → Topics & Hours.</div>';
                return;
            }

            var rows = result.topics.map(function(t) {
                return '<tr>' +
                    '<td style="padding:8px;border:1px solid #e0e0e0;">' + escapeHtml(t.topic) + '</td>' +
                    '<td style="padding:8px;border:1px solid #e0e0e0;text-align:center;">' + t.hours + '</td>' +
                    '<td style="padding:8px;border:1px solid #e0e0e0;text-align:center;">' + t.weight_percent + '%</td>' +
                    '<td style="padding:8px;border:1px solid #e0e0e0;text-align:center;">' + t.lots_target + '</td>' +
                    '<td style="padding:8px;border:1px solid #e0e0e0;text-align:center;">' + t.hots_target + '</td>' +
                    '<td style="padding:8px;border:1px solid #e0e0e0;text-align:center;font-weight:700;">' + t.target_items + '</td>' +
                '</tr>';
            }).join('');

            panel.innerHTML =
                '<div class="tos-panel-title">Generated Table of Specifications</div>' +
                '<div style="overflow-x:auto;"><table style="width:100%;border-collapse:collapse;font-size:11.5px;">' +
                '<thead><tr style="background:#f5f5f5;">' +
                '<th style="padding:8px;border:1px solid #e0e0e0;text-align:left;">Topic</th>' +
                '<th style="padding:8px;border:1px solid #e0e0e0;">Hours</th>' +
                '<th style="padding:8px;border:1px solid #e0e0e0;">% Weight</th>' +
                '<th style="padding:8px;border:1px solid #e0e0e0;">LOTS</th>' +
                '<th style="padding:8px;border:1px solid #e0e0e0;">HOTS</th>' +
                '<th style="padding:8px;border:1px solid #e0e0e0;">Items</th>' +
                '</tr></thead><tbody>' + rows + '</tbody>' +
                '<tfoot><tr style="background:#f5f5f5;font-weight:700;">' +
                '<td style="padding:8px;border:1px solid #e0e0e0;">TOTAL</td>' +
                '<td style="padding:8px;border:1px solid #e0e0e0;text-align:center;">' + result.total_hours + '</td>' +
                '<td style="padding:8px;border:1px solid #e0e0e0;text-align:center;">100%</td>' +
                '<td colspan="2" style="padding:8px;border:1px solid #e0e0e0;"></td>' +
                '<td style="padding:8px;border:1px solid #e0e0e0;text-align:center;">' + result.total_items + '</td>' +
                '</tr></tfoot></table></div>' +
                '<div style="margin-top:14px;text-align:right;"><button class="btn-generate" type="button" onclick="startExamFromTOS()">Start Exam from this TOS →</button></div>';
        }

        function startExamFromTOS() {
            if (!tosTargetCache) return;
            var a = ASSIGNMENTS.find(function(x) { return String(x.id) === String(tosTargetCache.assignmentId); });
            document.getElementById('ne-subject').value = tosTargetCache.assignmentId;
            document.getElementById('ne-period').value = tosTargetCache.period;
            document.getElementById('ne-target-items').value = tosTargetCache.totalItems;
            document.getElementById('ne-title').value = tosTargetCache.period + ' Examination' + (a ? ' — ' + a.label : '');
            openNewExamModal();
        }

        // ══════════════════════════════
        // NEW EXAM MODAL
        // ══════════════════════════════
        function openNewExamModal() { document.getElementById('new-exam-overlay').classList.add('open'); }
        function closeNewExamModal() { document.getElementById('new-exam-overlay').classList.remove('open'); }
        document.getElementById('new-exam-overlay').addEventListener('click', function(e) { if (e.target === this) closeNewExamModal(); });

        async function createExam() {
            var body = {
                program_assignment_id: document.getElementById('ne-subject').value,
                grading_period: document.getElementById('ne-period').value,
                title: document.getElementById('ne-title').value,
                duration_minutes: document.getElementById('ne-duration').value || null,
                target_items: document.getElementById('ne-target-items').value || null
            };
            if (!body.program_assignment_id || !body.title) { alert('Subject and Title are required.'); return; }

            try {
                var exam = await api('/faculty/exam-generator', { method: 'POST', body: body });
                closeNewExamModal();
                var opt = document.createElement('option');
                opt.value = exam.id;
                opt.textContent = exam.program_assignment.course.code + ' — ' + exam.title + ' (' + exam.grading_period + ')';
                document.getElementById('eb-exam-select').appendChild(opt);
                document.getElementById('eb-exam-select').value = exam.id;
                switchTab('exam-builder');
                await loadExam(exam.id);
            } catch (e) {
                alert(e.message);
            }
        }

        // ══════════════════════════════
        // EXAM BUILDER — load / state
        // ══════════════════════════════
        async function onExamSelectChange() {
            var id = document.getElementById('eb-exam-select').value;
            if (!id) { clearExamBuilder(); return; }
            await loadExam(id);
        }

        async function loadExam(id) {
            try {
                currentExam = await api('/faculty/exam-generator/' + id);
            } catch (e) {
                alert(e.message);
                return;
            }
            currentExam.sections.forEach(function(sec) {
                (sec.questions || []).forEach(function(q) { if (!q.children) q.children = []; });
            });

            document.getElementById('eb-exam-select').value = currentExam.id;
            document.getElementById('eb-title-input').value = currentExam.title;
            document.getElementById('eb-title-input').disabled = false;
            document.getElementById('eb-subject-label').textContent = currentExam.program_assignment.course.code + ' — ' + currentExam.program_assignment.course.title;
            document.getElementById('eb-type-label').textContent = currentExam.grading_period;
            var finalized = currentExam.status === 'finalized';
            document.getElementById('btn-save-exam').disabled = finalized;
            document.getElementById('btn-preview-exam').disabled = false;
            document.getElementById('btn-finalize-exam').disabled = finalized;
            document.getElementById('btn-add-section').disabled = finalized;
            document.getElementById('eb-no-exam-notice').style.display = 'none';

            renderEB();
            await refreshTosProgress();
        }

        function clearExamBuilder() {
            currentExam = null;
            document.getElementById('eb-title-input').value = '';
            document.getElementById('eb-title-input').disabled = true;
            document.getElementById('eb-subject-label').textContent = '—';
            document.getElementById('eb-type-label').textContent = '—';
            document.getElementById('btn-save-exam').disabled = true;
            document.getElementById('btn-preview-exam').disabled = true;
            document.getElementById('btn-finalize-exam').disabled = true;
            document.getElementById('btn-add-section').disabled = true;
            document.getElementById('eb-no-exam-notice').style.display = '';
            document.getElementById('eb-sections-content').innerHTML = '';
            document.getElementById('eb-section-links').innerHTML = '';
            document.getElementById('eb-tos-progress').innerHTML = '';
        }

        function findSection(secId) {
            return currentExam.sections.find(function(s) { return String(s.id) === String(secId); });
        }
        function findQuestion(secId, qi, ci) {
            var sec = findSection(secId);
            if (!sec) return null;
            var q = sec.questions[qi];
            if (ci === null || ci === undefined) return q;
            return q.children[ci];
        }

        // ══════════════════════════════
        // SECTIONS
        // ══════════════════════════════
        function addSection() {
            if (!currentExam) return;
            var id = 'new-' + Date.now();
            var num = currentExam.sections.length + 1;
            currentExam.sections.push({ id: id, title: 'Test ' + num, instructions: '', questions: [] });
            renderEB();
        }
        function deleteSection(secId) {
            if (!confirm('Delete this section?')) return;
            currentExam.sections = currentExam.sections.filter(function(s) { return String(s.id) !== String(secId); });
            renderEB();
        }
        function updateSectionTitle(secId, val) {
            var sec = findSection(secId);
            if (sec) { sec.title = val; renderSectionsBar(); }
        }
        function updateSectionInstructions(secId, val) {
            var sec = findSection(secId);
            if (sec) sec.instructions = val;
        }

        function renderEB() {
            if (!currentExam) return;
            renderSectionsBar();
            renderSectionsContent();
        }

        function renderSectionsBar() {
            var bar = document.getElementById('eb-section-links');
            bar.innerHTML = '';
            currentExam.sections.forEach(function(sec) {
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
            var disabled = currentExam.status === 'finalized' ? 'disabled' : '';

            currentExam.sections.forEach(function(sec) {
                var div = document.createElement('div');
                div.className = 'section-panel';
                div.id = 'sec-' + sec.id;

                var qHTML = '';
                (sec.questions || []).forEach(function(q, qi) { qHTML += buildQuestionHTML(sec.id, qi, q); });

                div.innerHTML =
                    '<div class="section-header">' +
                        '<input class="section-title-input" value="' + escapeAttr(sec.title) + '" ' + disabled +
                            ' onchange="updateSectionTitle(\'' + sec.id + '\', this.value)">' +
                        '<button class="btn-del-section" onclick="deleteSection(\'' + sec.id + '\')" title="Delete section" ' + disabled + '>🗑</button>' +
                    '</div>' +
                    '<textarea class="section-inst" oninput="updateSectionInstructions(\'' + sec.id + '\', this.value)" ' + disabled +
                        ' placeholder="Instructions for this section..." style="width:100%;border:1px solid #eee;border-radius:4px;padding:6px 8px;font-size:12px;color:#666;min-height:30px;margin:6px 0;">' + escapeHtml(sec.instructions || '') + '</textarea>' +
                    '<div id="questions-' + sec.id + '">' + qHTML + '</div>' +
                    '<button class="btn-add-question" onclick="openQTypeModal(\'' + sec.id + '\')" ' + disabled + '>+ Add Question</button>' +
                    ' <button class="btn-add-question" onclick="openAiGenModal(\'' + sec.id + '\')" ' + disabled + '>🤖 Generate with AI</button>';

                container.appendChild(div);
            });
        }

        // ══════════════════════════════
        // QUESTIONS
        // ══════════════════════════════
        function defaultOptionsFor(type) {
            switch (type) {
                case 'mc-single': return { choices: ['', '', '', ''], correct: 0 };
                case 'true-false': return { answer: true };
                case 'modified-true-false': return { answer: true, correction: '' };
                case 'identification': return { answer: '' };
                case 'enumeration': return { answers: [''] };
                case 'fill-blank': return { answers: [''] };
                case 'matching': return { left: [''], right: [''] };
                case 'ordering': return { items: [''] };
                case 'diagram': return { image_note: '', labels: [''] };
                case 'problem-solving': return { answer: '', solution_steps: '' };
                case 'short-answer': return { rubric: '' };
                default: return null;
            }
        }

        function topicOptionsHtml(selected) {
            var byAssignment = (currentExam && TOPICS_BY_ASSIGNMENT[currentExam.program_assignment_id]) || {};
            var topics = byAssignment[currentExam.grading_period] || [];
            var html = '<option value="">— No topic —</option>';
            topics.forEach(function(t) {
                html += '<option value="' + escapeAttr(t.topic) + '" ' + (selected === t.topic ? 'selected' : '') + '>' + escapeHtml(t.topic) + ' (' + t.hours + ' hrs)</option>';
            });
            return html;
        }

        function listTextarea(secId, qi, ci, field, arr, placeholder) {
            var value = (arr || []).join('\n');
            return '<textarea placeholder="' + placeholder + '" ' +
                'oninput="setOptionListField(\'' + secId + '\',' + qi + ',' + (ci === null ? 'null' : ci) + ',\'' + field + '\',this.value)" ' +
                'style="width:100%;border:1px solid #ddd;border-radius:4px;padding:6px 8px;font-size:12px;min-height:60px;">' + escapeHtml(value) + '</textarea>';
        }

        function renderOptionsEditor(secId, qi, ci, q) {
            var o = q.options || {};
            var ciArg = ci === null ? 'null' : ci;
            var idPrefix = secId + '-' + qi + (ci !== null ? '-' + ci : '');

            if (q.type === 'mc-single') {
                var choices = o.choices || [];
                var html = choices.map(function(c, i) {
                    return '<div class="q-option">' +
                        '<input type="radio" name="mc-' + idPrefix + '" ' + (o.correct === i ? 'checked' : '') +
                            ' onchange="setMcCorrect(\'' + secId + '\',' + qi + ',' + ciArg + ',' + i + ')">' +
                        '<input type="text" value="' + escapeAttr(c) + '" placeholder="Choice ' + (i + 1) + '" ' +
                            'oninput="setMcChoice(\'' + secId + '\',' + qi + ',' + ciArg + ',' + i + ',this.value)" ' +
                            'style="flex:1;border:1px solid #ddd;border-radius:4px;padding:4px 8px;font-size:12px;">' +
                        (choices.length > 2 ? '<button type="button" onclick="removeMcChoice(\'' + secId + '\',' + qi + ',' + ciArg + ',' + i + ')" style="border:none;background:none;color:#ef4444;cursor:pointer;">✕</button>' : '') +
                    '</div>';
                }).join('');
                html += '<button type="button" class="btn-add-question" style="margin-top:6px;padding:4px 10px;font-size:11px;" onclick="addMcChoice(\'' + secId + '\',' + qi + ',' + ciArg + ')">+ Add choice</button>';
                return html;
            }
            if (q.type === 'true-false' || q.type === 'modified-true-false') {
                var extra = q.type === 'modified-true-false'
                    ? '<input type="text" value="' + escapeAttr(o.correction || '') + '" placeholder="Correct term if False" ' +
                        'oninput="setOptionField(\'' + secId + '\',' + qi + ',' + ciArg + ',\'correction\',this.value)" ' +
                        'style="margin-top:6px;width:100%;border:1px solid #ddd;border-radius:4px;padding:5px 8px;font-size:12px;">'
                    : '';
                return '<div class="q-option"><input type="radio" name="tf-' + idPrefix + '" ' + (o.answer === true ? 'checked' : '') +
                        ' onchange="setOptionField(\'' + secId + '\',' + qi + ',' + ciArg + ',\'answer\',true)"><span>True</span></div>' +
                    '<div class="q-option"><input type="radio" name="tf-' + idPrefix + '" ' + (o.answer === false ? 'checked' : '') +
                        ' onchange="setOptionField(\'' + secId + '\',' + qi + ',' + ciArg + ',\'answer\',false)"><span>False</span></div>' + extra;
            }
            if (q.type === 'identification') {
                return '<input type="text" value="' + escapeAttr(o.answer || '') + '" placeholder="Correct answer" ' +
                    'oninput="setOptionField(\'' + secId + '\',' + qi + ',' + ciArg + ',\'answer\',this.value)" ' +
                    'style="width:100%;border:1px solid #ddd;border-radius:4px;padding:6px 8px;font-size:12px;">';
            }
            if (q.type === 'enumeration') return listTextarea(secId, qi, ci, 'answers', o.answers, 'One answer per line');
            if (q.type === 'fill-blank') return listTextarea(secId, qi, ci, 'answers', o.answers, "One blank's answer per line, in order");
            if (q.type === 'ordering') return listTextarea(secId, qi, ci, 'items', o.items, 'Items in the correct order, one per line');
            if (q.type === 'matching') {
                return '<div style="display:flex;gap:10px;">' +
                    '<div style="flex:1;"><div style="font-size:10.5px;color:#888;margin-bottom:3px;">Column A</div>' + listTextarea(secId, qi, ci, 'left', o.left, 'One per line') + '</div>' +
                    '<div style="flex:1;"><div style="font-size:10.5px;color:#888;margin-bottom:3px;">Column B (matched by line)</div>' + listTextarea(secId, qi, ci, 'right', o.right, 'One per line') + '</div>' +
                '</div>';
            }
            if (q.type === 'diagram') {
                return '<input type="text" value="' + escapeAttr(o.image_note || '') + '" placeholder="Diagram reference / description" ' +
                        'oninput="setOptionField(\'' + secId + '\',' + qi + ',' + ciArg + ',\'image_note\',this.value)" ' +
                        'style="width:100%;border:1px solid #ddd;border-radius:4px;padding:6px 8px;font-size:12px;margin-bottom:6px;">' +
                    listTextarea(secId, qi, ci, 'labels', o.labels, 'Labels, one per line');
            }
            if (q.type === 'problem-solving') {
                return '<input type="text" value="' + escapeAttr(o.answer || '') + '" placeholder="Final answer" ' +
                        'oninput="setOptionField(\'' + secId + '\',' + qi + ',' + ciArg + ',\'answer\',this.value)" ' +
                        'style="width:100%;border:1px solid #ddd;border-radius:4px;padding:6px 8px;font-size:12px;margin-bottom:6px;">' +
                    '<textarea placeholder="Solution steps" oninput="setOptionField(\'' + secId + '\',' + qi + ',' + ciArg + ',\'solution_steps\',this.value)" ' +
                        'style="width:100%;border:1px solid #ddd;border-radius:4px;padding:6px 8px;font-size:12px;min-height:50px;">' + escapeHtml(o.solution_steps || '') + '</textarea>';
            }
            if (q.type === 'short-answer') {
                return '<textarea placeholder="Rubric / expected answer notes (optional)" ' +
                    'oninput="setOptionField(\'' + secId + '\',' + qi + ',' + ciArg + ',\'rubric\',this.value)" ' +
                    'style="width:100%;border:1px solid #ddd;border-radius:4px;padding:6px 8px;font-size:12px;min-height:50px;">' + escapeHtml(o.rubric || '') + '</textarea>';
            }
            return '';
        }

        function buildQuestionHTML(secId, qi, q, ci) {
            ci = (ci === undefined) ? null : ci;
            var ct = QUESTION_TYPES[q.type] || {};
            var num = ci === null ? (qi + 1) : (qi + 1) + String.fromCharCode(97 + ci);
            var idAttr = 'q-' + secId + '-' + qi + (ci !== null ? '-' + ci : '');
            var indent = ci !== null ? 'margin-left:24px;border-left:3px solid #e0e7ff;' : '';
            var ciArg = ci === null ? 'null' : ci;

            var bloomBadge = ct.bloom
                ? '<span class="q-blooms-badge">' + ct.bloom + ' (' + ct.category + ')</span>'
                : '<span class="q-blooms-badge" style="background:#eee;color:#888;">Container</span>';

            var body = '<div class="question-card" id="' + idAttr + '" style="' + indent + '">' +
                '<div class="q-header">' +
                    '<div class="q-num" style="flex:1;">' + num + '.</div>' +
                    '<div class="q-actions">' +
                        (ci === null ? '<button class="btn-q-action" onclick="copyQuestion(\'' + secId + '\',' + qi + ')" title="Copy">⧉</button>' : '') +
                        '<button class="btn-q-action del" onclick="deleteQuestion(\'' + secId + '\',' + qi + ',' + ciArg + ')" title="Delete">🗑</button>' +
                    '</div>' +
                '</div>' +
                '<textarea class="q-text-input" oninput="setQuestionText(\'' + secId + '\',' + qi + ',' + ciArg + ',this.value)" ' +
                    'placeholder="' + (q.type === 'case-analysis' ? 'Scenario text...' : 'Question text...') + '" ' +
                    'style="width:100%;border:1px solid #ddd;border-radius:4px;padding:8px;font-size:12.5px;min-height:44px;margin:6px 0;">' + escapeHtml(q.question_text || '') + '</textarea>';

            if (q.type !== 'case-analysis') {
                body += '<div style="margin:8px 0;">' + renderOptionsEditor(secId, qi, ci, q) + '</div>';
            }

            body += '<div class="q-footer">' +
                    '<span class="q-type-label">' + (ct.label || q.type) + '</span>' +
                    bloomBadge +
                    (q.type !== 'case-analysis' ?
                        '<select onchange="setQuestionTopic(\'' + secId + '\',' + qi + ',' + ciArg + ',this.value)" style="font-size:11px;padding:3px 6px;border:1px solid #ccc;border-radius:4px;">' + topicOptionsHtml(q.topic) + '</select>'
                        : '') +
                    (q.type !== 'case-analysis' ?
                        '<div class="q-pts"><input type="number" value="' + (q.points || 1) + '" min="1" ' +
                            'onchange="updatePts(\'' + secId + '\',' + qi + ',' + ciArg + ',this.value)" style="width:40px;height:24px;text-align:center;border:1px solid #ccc;border-radius:4px;font-size:12px;"> pts</div>'
                        : '') +
                '</div>';

            if (q.type === 'case-analysis') {
                body += '<div style="margin-top:8px;">';
                (q.children || []).forEach(function(child, cidx) { body += buildQuestionHTML(secId, qi, child, cidx); });
                body += '</div>';
                body += '<button class="btn-add-question" style="margin-top:6px;" onclick="openQTypeModal(\'' + secId + '\',' + qi + ')">+ Add Sub-question</button>';
            }

            body += '</div>';
            return body;
        }

        // ── field mutators (all mutate currentExam in place) ──
        function setQuestionText(secId, qi, ci, value) { var q = findQuestion(secId, qi, ci); if (q) q.question_text = value; }
        function setQuestionTopic(secId, qi, ci, value) { var q = findQuestion(secId, qi, ci); if (q) q.topic = value || null; }
        function updatePts(secId, qi, ci, val) { var q = findQuestion(secId, qi, ci); if (q) q.points = parseInt(val) || 1; }
        function setOptionField(secId, qi, ci, field, value) { var q = findQuestion(secId, qi, ci); if (q) { if (!q.options) q.options = {}; q.options[field] = value; } }
        function setOptionListField(secId, qi, ci, field, text) { var q = findQuestion(secId, qi, ci); if (q) { if (!q.options) q.options = {}; q.options[field] = text.split('\n'); } }
        function setMcChoice(secId, qi, ci, idx, value) { var q = findQuestion(secId, qi, ci); if (q) q.options.choices[idx] = value; }
        function setMcCorrect(secId, qi, ci, idx) { var q = findQuestion(secId, qi, ci); if (q) q.options.correct = idx; }
        function addMcChoice(secId, qi, ci) { var q = findQuestion(secId, qi, ci); if (q) { q.options.choices.push(''); renderEB(); } }
        function removeMcChoice(secId, qi, ci, idx) {
            var q = findQuestion(secId, qi, ci);
            if (!q) return;
            q.options.choices.splice(idx, 1);
            if (q.options.correct >= q.options.choices.length) q.options.correct = 0;
            renderEB();
        }

        function deleteQuestion(secId, qi, ci) {
            var sec = findSection(secId);
            if (!sec) return;
            if (ci === null || ci === undefined) {
                sec.questions.splice(qi, 1);
            } else {
                sec.questions[qi].children.splice(ci, 1);
            }
            renderEB();
        }
        function copyQuestion(secId, qi) {
            var sec = findSection(secId);
            if (!sec) return;
            var copy = JSON.parse(JSON.stringify(sec.questions[qi]));
            sec.questions.splice(qi + 1, 0, copy);
            renderEB();
        }

        // Question Type Modal
        function openQTypeModal(secId, qi) {
            qtypeTarget = { secId: secId, qi: (qi === undefined ? null : qi) };
            document.getElementById('qtype-title').textContent = qtypeTarget.qi === null ? 'Choose Question Type' : 'Choose Sub-question Type';
            document.getElementById('qtype-overlay').classList.add('open');
        }
        function closeQTypeModal() { document.getElementById('qtype-overlay').classList.remove('open'); }
        document.getElementById('qtype-overlay').addEventListener('click', function(e) { if (e.target === this) closeQTypeModal(); });

        // ══════════════════════════════
        // AI-ASSISTED QUESTION GENERATION
        // ══════════════════════════════
        let aiGenTarget = null; // section id to insert generated questions into

        function aiGenTopicOptionsHtml() {
            var byAssignment = (currentExam && TOPICS_BY_ASSIGNMENT[currentExam.program_assignment_id]) || {};
            var topics = byAssignment[currentExam.grading_period] || [];
            return topics.map(function(t) {
                return '<option value="' + t.id + '">' + escapeHtml(t.topic) + ' (' + t.hours + ' hrs)</option>';
            }).join('');
        }

        function openAiGenModal(secId) {
            if (!currentExam) return;
            aiGenTarget = secId;
            document.getElementById('ai-gen-topic').innerHTML = '<option value="" disabled selected>Select topic</option>' + aiGenTopicOptionsHtml();
            document.getElementById('ai-gen-error').style.display = 'none';
            document.getElementById('ai-gen-overlay').classList.add('open');
        }
        function closeAiGenModal() { document.getElementById('ai-gen-overlay').classList.remove('open'); }
        document.getElementById('ai-gen-overlay').addEventListener('click', function(e) { if (e.target === this) closeAiGenModal(); });

        async function submitAiGen() {
            var topicId = document.getElementById('ai-gen-topic').value;
            var type = document.getElementById('ai-gen-type').value;
            var count = parseInt(document.getElementById('ai-gen-count').value) || 3;
            var errBox = document.getElementById('ai-gen-error');
            errBox.style.display = 'none';

            if (!topicId) {
                errBox.textContent = 'Please select a topic.';
                errBox.style.display = 'block';
                return;
            }

            var btn = document.getElementById('ai-gen-submit');
            btn.disabled = true;
            btn.textContent = 'Generating…';

            try {
                var result = await api('/faculty/exam-generator/generate-questions', {
                    method: 'POST',
                    body: { course_topic_id: topicId, type: type, count: count }
                });
                var sec = findSection(aiGenTarget);
                if (sec) {
                    result.questions.forEach(function(q) {
                        sec.questions.push({
                            type: q.type, topic: q.topic, question_text: q.question_text,
                            points: q.points, options: q.options, children: []
                        });
                    });
                    renderEB();
                }
                closeAiGenModal();
            } catch (e) {
                errBox.textContent = e.message;
                errBox.style.display = 'block';
            } finally {
                btn.disabled = false;
                btn.textContent = 'Generate';
            }
        }

        function addQuestion(type) {
            closeQTypeModal();
            var sec = findSection(qtypeTarget.secId);
            if (!sec) return;

            if (qtypeTarget.qi !== null && type === 'case-analysis') {
                alert('A Case Analysis sub-question cannot itself be a Case Analysis.');
                return;
            }

            var newQ = { type: type, topic: null, question_text: '', points: 1, options: defaultOptionsFor(type), children: [] };

            if (qtypeTarget.qi === null) {
                sec.questions.push(newQ);
            } else {
                var parent = sec.questions[qtypeTarget.qi];
                if (!parent.children) parent.children = [];
                parent.children.push(newQ);
            }
            renderEB();
        }

        // ══════════════════════════════
        // SAVE / PREVIEW / FINALIZE
        // ══════════════════════════════
        function serializeQuestion(q) {
            return {
                type: q.type,
                topic: q.topic || null,
                question_text: q.question_text || '',
                points: q.points || 1,
                options: q.options || null,
                children: (q.children || []).map(serializeQuestion)
            };
        }

        async function saveExam() {
            if (!currentExam) return;
            var body = {
                title: document.getElementById('eb-title-input').value,
                duration_minutes: currentExam.duration_minutes || null,
                sections: currentExam.sections.map(function(sec) {
                    return {
                        title: sec.title,
                        instructions: sec.instructions,
                        questions: (sec.questions || []).map(serializeQuestion)
                    };
                })
            };

            try {
                currentExam = await api('/faculty/exam-generator/' + currentExam.id, { method: 'PUT', body: body });
                currentExam.sections.forEach(function(sec) {
                    (sec.questions || []).forEach(function(q) { if (!q.children) q.children = []; });
                });
                renderEB();
                await refreshTosProgress();
                alert('Exam saved.');
            } catch (e) {
                alert('Save failed: ' + e.message);
            }
        }

        function toRoman(n) { return ['I','II','III','IV','V','VI','VII','VIII'][n - 1] || n; }

        function previewOptionsHtml(q) {
            var o = q.options || {};
            if (q.type === 'mc-single') {
                return '<div style="margin-top:4px;">' + (o.choices || []).map(function(c, i) { return '<div>' + String.fromCharCode(97 + i) + '. ' + escapeHtml(c) + '</div>'; }).join('') + '</div>';
            }
            if (q.type === 'true-false' || q.type === 'modified-true-false') {
                return '<div style="margin-top:4px;">_____ (True / False)</div>';
            }
            if (q.type === 'matching') {
                var left = o.left || [], right = o.right || [];
                return '<div style="display:flex;gap:20px;margin-top:4px;"><div>' +
                    left.map(function(l, i) { return (i + 1) + '. ' + escapeHtml(l); }).join('<br>') +
                    '</div><div>' + right.map(function(r, i) { return String.fromCharCode(97 + i) + '. ' + escapeHtml(r); }).join('<br>') + '</div></div>';
            }
            return '';
        }

        function previewExam() {
            if (!currentExam || currentExam.sections.length === 0) {
                alert('No sections to preview. Add sections and questions first.');
                return;
            }
            var html = '<div style="max-width:700px;margin:0 auto;font-family:Arial, sans-serif;">' +
                '<div style="text-align:center;margin-bottom:20px;">' +
                    '<div style="font-weight:700;font-size:16px;">' + escapeHtml(currentExam.program_assignment.course.title) + '</div>' +
                    '<div style="font-size:13px;color:#555;">' + escapeHtml(currentExam.title) + ' — ' + currentExam.grading_period + ' Examination</div>' +
                    '<div style="font-size:11px;color:#888;">Name: _______________________  Score: _______</div>' +
                '</div>';

            currentExam.sections.forEach(function(sec, si) {
                html += '<div style="margin-bottom:20px;">' +
                    '<div style="font-weight:700;font-size:13.5px;margin-bottom:4px;">Test ' + toRoman(si + 1) + ' — ' + escapeHtml(sec.title) + '</div>' +
                    (sec.instructions ? '<div style="font-style:italic;font-size:11.5px;color:#666;margin-bottom:8px;">' + escapeHtml(sec.instructions) + '</div>' : '') +
                    '<ol style="font-size:12.5px;padding-left:20px;">';
                (sec.questions || []).forEach(function(q) {
                    html += '<li style="margin-bottom:8px;">' + escapeHtml(q.question_text || '(no question text)') +
                        previewOptionsHtml(q) +
                        (q.children && q.children.length ? '<ol type="a" style="margin-top:6px;">' + q.children.map(function(c) {
                            return '<li style="margin-bottom:6px;">' + escapeHtml(c.question_text || '') + previewOptionsHtml(c) + '</li>';
                        }).join('') + '</ol>' : '') +
                    '</li>';
                });
                html += '</ol></div>';
            });
            html += '</div>';

            document.getElementById('preview-body').innerHTML = html;
            document.getElementById('preview-overlay').classList.add('open');
        }
        function closePreview() { document.getElementById('preview-overlay').classList.remove('open'); }
        document.getElementById('preview-overlay').addEventListener('click', function(e) { if (e.target === this) closePreview(); });

        async function finalizeExam() {
            if (!currentExam) return;
            if (!confirm('Finalize this exam? It can no longer be edited afterward.')) return;
            try {
                await fetch('/faculty/exam-generator/' + currentExam.id + '/finalize', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
                });
                await loadExam(currentExam.id);
            } catch (e) {
                alert(e.message);
            }
        }

        // ══════════════════════════════
        // LIVE TOS PROGRESS (actual vs. target)
        // ══════════════════════════════
        async function refreshTosProgress() {
            var container = document.getElementById('eb-tos-progress');
            if (!currentExam || !currentExam.target_items) { container.innerHTML = ''; return; }

            var actual, target;
            try {
                actual = await api('/faculty/exam-generator/' + currentExam.id + '/tos');
                target = await api('/faculty/exam-generator/tos-target', {
                    method: 'POST',
                    body: {
                        program_assignment_id: currentExam.program_assignment_id,
                        grading_period: currentExam.grading_period,
                        total_items: currentExam.target_items
                    }
                });
            } catch (e) {
                container.innerHTML = '';
                return;
            }

            var rows = (target.topics || []).map(function(t) {
                var a = actual.topics[t.topic] || { items: 0 };
                return '<div style="display:flex;justify-content:space-between;padding:4px 0;border-bottom:1px solid #f0f0f0;font-size:11.5px;">' +
                    '<span>' + escapeHtml(t.topic) + '</span>' +
                    '<span style="color:' + (a.items >= t.target_items ? '#16a34a' : '#888') + ';">' + a.items + ' / ' + t.target_items + ' items</span>' +
                '</div>';
            }).join('');

            container.innerHTML = '<div class="obe-section" style="margin-bottom:12px;">' +
                '<div class="obe-header"><span class="obe-title">TOS Progress — ' + actual.total_items + ' / ' + currentExam.target_items + ' total items</span></div>' +
                rows +
            '</div>';
        }

        // ══════════════════════════════
        // ITEM BANK
        // ══════════════════════════════
        async function loadItemBank() {
            var courseId = document.getElementById('bank-course-select').value;
            var container = document.getElementById('bank-items-container');
            if (!courseId) return;

            container.innerHTML = '<div class="tab-empty">Loading…</div>';
            var items;
            try {
                items = await api('/faculty/exam-generator/item-bank?course_id=' + courseId);
            } catch (e) {
                container.innerHTML = '<div class="tab-empty" style="color:#ef4444;">' + escapeHtml(e.message) + '</div>';
                return;
            }

            if (items.length === 0) {
                container.innerHTML = '<div class="tab-empty"><div class="empty-icon">🗃️</div>No reusable items yet for this subject. Add questions in the Exam Builder first.</div>';
                return;
            }

            container.innerHTML = items.map(function(item) {
                var ct = QUESTION_TYPES[item.type] || {};
                return '<div class="question-card" style="margin-bottom:10px;">' +
                    '<div class="q-header"><div class="q-num" style="flex:1;">' + escapeHtml(item.question_text || '(Case Analysis scenario)') + '</div>' +
                    '<button class="btn-q-action" onclick="reuseBankItem(' + item.id + ')" title="Add to current exam" ' + (currentExam ? '' : 'disabled') + '>+ Add</button></div>' +
                    '<div class="q-footer"><span class="q-type-label">' + (ct.label || item.type) + '</span>' +
                    (ct.bloom ? '<span class="q-blooms-badge">' + ct.bloom + '</span>' : '') +
                    (item.topic ? '<span class="q-type-label">' + escapeHtml(item.topic) + '</span>' : '') + '</div>' +
                '</div>';
            }).join('');
        }

        async function reuseBankItem(questionId) {
            if (!currentExam || currentExam.sections.length === 0) {
                alert('Open an exam with at least one section in the Exam Builder first.');
                return;
            }
            var targetSectionId = currentExam.sections[0].id;
            if (currentExam.sections.length > 1) {
                var choice = prompt('Add to which section?\n' + currentExam.sections.map(function(s, i) { return (i + 1) + '. ' + s.title; }).join('\n'), '1');
                var idx = parseInt(choice) - 1;
                if (isNaN(idx) || !currentExam.sections[idx]) return;
                targetSectionId = currentExam.sections[idx].id;
            }

            if (String(targetSectionId).indexOf('new-') === 0) {
                alert('Save the exam first so this section exists on the server, then try again.');
                return;
            }

            try {
                await api('/faculty/exam-generator/item-bank/' + questionId + '/reuse', {
                    method: 'POST',
                    body: { exam_section_id: targetSectionId }
                });
                await loadExam(currentExam.id);
                alert('Item added.');
            } catch (e) {
                alert(e.message);
            }
        }

        function handleLogout() {
            window.location.href = '{{ url("/login") }}';
        }
    </script>

</body>
</html>