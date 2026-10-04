<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Assessment Generator – CBMA System</title>
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

        .tos-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 9px 18px;
            background: #0f2557;
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            white-space: nowrap;
            text-decoration: none;
            transition: background 0.15s;
        }
        .tos-action-btn:hover { background: #1a3a7a; }

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
        .btn-danger { background: #ef4444; color: #fff; border: none; font-size: 12.5px; font-weight: 600; padding: 8px 20px; border-radius: 5px; cursor: pointer; }
        .btn-danger:hover { background: #dc2626; }
        .modal-error { display: none; background: #fee2e2; border: 1px solid #fca5a5; color: #b91c1c; font-size: 11.5px; padding: 9px 11px; border-radius: 5px; margin-bottom: 14px; line-height: 1.4; }
        .modal-warning { display: none; background: #fffbeb; border: 1px solid #fcd34d; color: #92400e; font-size: 11.5px; padding: 9px 11px; border-radius: 5px; margin-bottom: 14px; line-height: 1.4; }
        .tos-view-value {
            width: 100%; padding: 8px 10px; border: 1px solid #e4e4e4; border-radius: 5px;
            font-size: 12.5px; color: #333; background: #f7f7f8;
        }

        /* ═══════════════════ QUESTION TYPE MODAL ═══════════════════ */
        .qtype-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 999;
            align-items: center; justify-content: center;
        }
        .qtype-overlay.open { display: flex; }

        .qtype-modal {
            background: #fff; border-radius: 12px;
            padding: 22px 22px 16px; width: 340px; max-width: 96vw;
            box-shadow: 0 10px 40px rgba(0,0,0,0.25);
        }

        .qtype-title { font-size: 16px; font-weight: 700; color: #1a1a2e; margin-bottom: 14px; }

        .qtype-list {
            display: flex; flex-direction: column;
            border: 1px solid #e4e4e4; border-radius: 8px; overflow: hidden;
            margin-bottom: 18px;
        }

        .qtype-btn {
            display: block; width: 100%;
            border: none; border-bottom: 1px solid #eee;
            padding: 11px 14px; cursor: pointer; background: #fff;
            transition: background 0.15s, color 0.15s;
            text-align: left; font-size: 12.5px; font-weight: 600; color: #1a1a2e;
        }
        .qtype-list .qtype-btn:last-child { border-bottom: none; }

        .qtype-btn:hover { background: #f0f4ff; }
        .qtype-btn:first-child { background: #0f2557; color: #fff; }
        .qtype-btn:first-child:hover { background: #1a3a7a; }

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
                Assessment Generator
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
                @if(($urgentSubmissionsCount ?? 0) > 0)
                    <span class="nav-badge">{{ $urgentSubmissionsCount }}</span>
                @endif
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
                                    <input type="number" id="tos-total-items" min="1">
                                </div>
                                <div class="form-group">
                                    <label>Exam Duration (mins)</label>
                                    <input type="number" id="tos-duration" min="1">
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
                <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:16px;">
                    <div class="form-group" style="max-width:360px;">
                        <label>Filter by Subject</label>
                        <select id="bank-course-select" onchange="loadItemBank()">
                            <option value="" disabled selected>Select subject</option>
                            @foreach($assignments->unique('course_id') as $a)
                                <option value="{{ $a->course_id }}">{{ $a->course->code }} — {{ $a->course->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="max-width:260px;">
                        <label>Filter by Topic</label>
                        <select id="bank-topic-select" onchange="renderBankItems()" disabled>
                            <option value="">All Topics</option>
                        </select>
                    </div>
                    <div class="form-group" style="max-width:220px;">
                        <label>Filter by Cognitive Level</label>
                        <select id="bank-bloom-select" onchange="renderBankItems()" disabled>
                            <option value="">All Levels</option>
                            @foreach(\App\Support\BloomLevels::LEVELS as $level)
                                <option value="{{ $level }}">{{ $level }}</option>
                            @endforeach
                        </select>
                    </div>
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
            <div class="modal-error" id="new-exam-error"></div>
            <div class="modal-warning" id="new-exam-tos-warning">⚠ No TOS generated yet for this subject and grading period. Go to the <strong>TOS Generator</strong> tab and generate it first — the exam is built from it.</div>
            <div class="modal-field">
                <label>Subject <span style="color:#ef4444">*</span></label>
                <select id="ne-subject" onchange="checkTosRequirement()">
                    <option value="" disabled selected>Select subject</option>
                    @foreach($assignments as $a)
                        <option value="{{ $a->id }}">{{ $a->course->code }} — {{ $a->course->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-row">
                <div class="modal-field">
                    <label>Grading Period <span style="color:#ef4444">*</span></label>
                    <select id="ne-period" onchange="checkTosRequirement()">
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
                <button type="button" class="btn-save" id="ne-create-btn" onclick="createExam()">Create</button>
            </div>
        </div>
    </div>

    {{-- ════════════ START EXAM FROM TOS — VIEW-ONLY CONFIRM MODAL ════════════ --}}
    <div class="modal-overlay" id="start-exam-view-overlay">
        <div class="modal">
            <div class="modal-title">Start Exam from TOS</div>
            <div class="modal-error" id="start-exam-error"></div>
            <div class="modal-field">
                <label>Subject</label>
                <div class="tos-view-value" id="sev-subject">—</div>
            </div>
            <div class="modal-row">
                <div class="modal-field">
                    <label>Grading Period</label>
                    <div class="tos-view-value" id="sev-period">—</div>
                </div>
                <div class="modal-field">
                    <label>Duration (mins)</label>
                    <div class="tos-view-value" id="sev-duration">—</div>
                </div>
            </div>
            <div class="modal-field">
                <label>Title</label>
                <div class="tos-view-value" id="sev-title">—</div>
            </div>
            <div class="modal-field">
                <label>Target Items</label>
                <div class="tos-view-value" id="sev-target-items">—</div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeStartExamView()">Cancel</button>
                <button type="button" class="btn-save" onclick="confirmStartExamFromTOS()">Start Exam →</button>
            </div>
        </div>
    </div>

    {{-- ════════════ AI GENERATE QUESTIONS MODAL ════════════ --}}
    <div class="modal-overlay" id="ai-gen-overlay">
        <div class="modal">
            <div class="modal-title">🤖 Generate Questions with AI</div>
            <div class="modal-field">
                <label>Topic <span style="color:#ef4444">*</span></label>
                <select id="ai-gen-topic" onchange="onAiGenTopicChange()">
                    <option value="" disabled selected>Select topic</option>
                </select>
            </div>
            <div id="ai-gen-no-content-warning" style="display:none;background:#fff7ed;border:1px solid #fdba74;color:#9a3412;font-size:11.5px;padding:8px 10px;border-radius:6px;margin-bottom:12px;">
                ⚠️ This topic has no Teaching Notes or uploaded Module yet — the AI has nothing to base questions on. Add one first under Course Coordination → Topics & Hours.
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
                    <label>How many items?</label>
                    <input type="number" id="ai-gen-count" min="1" max="10" value="3">
                </div>
            </div>
            <div style="font-size:10.5px;color:#888;margin:-4px 0 10px;">
                Bloom's Taxonomy level is set automatically, based on where these questions fall in this topic's TOS.
            </div>
            <div id="ai-gen-error" style="display:none;color:#ef4444;font-size:11.5px;margin-bottom:10px;"></div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeAiGenModal()">Cancel</button>
                <button type="button" class="btn-save" id="ai-gen-submit" onclick="submitAiGen()">Generate</button>
            </div>
        </div>
    </div>

    {{-- ════════════ SAVE RESULT MODAL ════════════ --}}
    <div class="modal-overlay" id="save-result-overlay">
        <div class="modal" style="width:360px;">
            <div class="modal-title" id="save-result-title">Exam saved</div>
            <div id="save-result-message" style="font-size:12.5px;color:#444;margin-bottom:18px;"></div>
            <div class="modal-actions">
                <button type="button" class="btn-save" onclick="closeSaveResult()">OK</button>
            </div>
        </div>
    </div>

    {{-- ════════════ FINALIZE CONFIRM MODAL ════════════ --}}
    <div class="modal-overlay" id="finalize-confirm-overlay">
        <div class="modal" style="width:380px;">
            <div class="modal-title">Finalize this exam?</div>
            <div style="font-size:12.5px;color:#444;margin-bottom:18px;">
                This marks the exam as finalized for printing/export. You can still edit it afterward if needed.
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeFinalizeConfirm()">Cancel</button>
                <button type="button" class="btn-save" onclick="confirmFinalize()">Finalize</button>
            </div>
        </div>
    </div>

    {{-- ════════════ DELETE SECTION CONFIRM MODAL ════════════ --}}
    <div class="modal-overlay" id="delete-section-overlay">
        <div class="modal" style="width:380px;">
            <div class="modal-title">Delete Section</div>
            <div style="font-size:12.5px;color:#444;margin-bottom:18px;">
                Delete <strong id="delete-section-title"></strong>? Its questions will be removed too. This only takes effect once you Save.
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeDeleteSectionModal()">Cancel</button>
                <button type="button" class="btn-danger" onclick="confirmDeleteSection()">Delete Section</button>
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
            <div class="qtype-list">
                @foreach(\App\Support\BloomLevels::TYPES as $typeKey => $t)
                    <button class="qtype-btn" onclick="addQuestion('{{ $typeKey }}')">{{ $t['label'] }}</button>
                @endforeach
            </div>
            <button class="btn-qtype-cancel" onclick="closeQTypeModal()">Cancel</button>
        </div>
    </div>

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        const QUESTION_TYPES = @json(\App\Support\BloomLevels::TYPES);
        const TOPICS_BY_ASSIGNMENT = @json($topicsByAssignment);
        const TOS_BY_ASSIGNMENT = @json($tosByAssignment);
        const ASSIGNMENTS = @json($assignments->map(fn($a) => ['id' => $a->id, 'label' => $a->course->code . ' — ' . $a->course->title]));

        let currentExam = null;
        let tosTargetCache = null;
        let tosEditMode = false;
        let qtypeTarget = { secId: null, qi: null };
        // TOS breakdown for the currently-loaded exam's target_items (per-topic, per-Bloom's-
        // level counts) — fetched once per loadExam(), used to resolve each question's
        // displayed Bloom's Level from its position (see assignDisplayBloomLevels()).
        let examTosTargetCache = null;

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

        var TOS_BLOOM_LEVELS = ['Remembering', 'Understanding', 'Applying', 'Analyzing', 'Evaluating', 'Creating'];
        var TOS_CELL_STYLE = 'padding:6px 4px;border:1px solid #e0e0e0;text-align:center;vertical-align:top;font-size:10.5px;line-height:1.4;';

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
            tosEditMode = false;
            document.getElementById('tos-total-hours').textContent = result.total_hours + ' hrs';

            if (!result.topics || result.topics.length === 0) {
                panel.innerHTML =
                    '<div class="tos-panel-title">Generated Table of Specifications</div>' +
                    '<div class="tos-empty"><div class="empty-icon">⚠️</div>No OBTL topics have been added yet for this subject\'s ' + period + ' period. Ask your Program Head to add them under Course Oversight → Topics & Hours.</div>';
                return;
            }

            renderTosResult();
        }

        // Recomputes derived fields (per-topic item total, sequential I./II. item
        // numbering, weight %, grand totals) from raw hours + per-level counts — every
        // level is worth exactly 1 point/item, so "No. of Items" always equals the Total
        // Items you configured (or whatever you edit it to below). Same rule the backend
        // uses in CourseTopic::targetBreakdown(), run client-side so Edit-mode changes
        // reflect immediately without a round trip.
        function recomputeTosBreakdown(topics) {
            var counterI = 1, counterII = 1;
            var totalHours = 0;
            topics.forEach(function(t) { totalHours += t.hours; });

            var totalItems = 0;
            topics.forEach(function(t) {
                var topicItems = 0;
                TOS_BLOOM_LEVELS.forEach(function(l) {
                    var cell = t.levels[l];
                    cell.points_per_item = 1;
                    cell.points = cell.count;
                    if (cell.count > 0) {
                        if (l === 'Creating') {
                            var start = counterII; counterII += cell.count; var end = counterII - 1;
                            cell.range = 'II.' + (cell.count === 1 ? start : start + '-' + end);
                        } else {
                            var start2 = counterI; counterI += cell.count; var end2 = counterI - 1;
                            cell.range = 'I.' + (cell.count === 1 ? start2 : start2 + '-' + end2);
                        }
                    } else {
                        cell.range = null;
                    }
                    topicItems += cell.count;
                });
                t.target_items = topicItems;
                t.topic_points = topicItems;
                t.weight_percent = totalHours ? Math.round((t.hours / totalHours) * 1000) / 10 : 0;
                totalItems += topicItems;
            });

            return { total_hours: totalHours, total_items: totalItems, total_points: totalItems, topics: topics };
        }

        function onTosInputChange(e) {
            if (!e.target.classList || !e.target.classList.contains('tos-edit-input')) return;
            var ti = parseInt(e.target.dataset.ti, 10);
            var level = e.target.dataset.level;
            var field = e.target.dataset.field;
            var val = Math.max(0, parseInt(e.target.value, 10) || 0);
            var topics = tosTargetCache.result.topics;

            if (field === 'hours') {
                topics[ti].hours = val;
            } else if (level) {
                topics[ti].levels[level].count = val;
            }

            tosTargetCache.result = recomputeTosBreakdown(topics);
            document.getElementById('tos-total-hours').textContent = tosTargetCache.result.total_hours + ' hrs';
            renderTosResult();
        }

        function toggleTosEdit() {
            tosEditMode = !tosEditMode;
            renderTosResult();
        }

        function levelCellHtml(cell, editable, ti, level) {
            if (editable) {
                return '<input type="number" min="0" class="tos-edit-input" data-ti="' + ti + '" data-level="' + level + '" value="' + cell.count + '" style="width:38px;text-align:center;font-size:11px;padding:2px;border:1px solid #ccc;border-radius:3px;">' +
                    (cell.count > 0 ? '<div style="font-size:9px;color:#888;margin-top:3px;">' + cell.range + '</div>' : '');
            }
            if (!cell || cell.count === 0) return '';
            return '<div>' + cell.range + '</div><div>(' + cell.count + ')</div>';
        }

        function renderTosResult() {
            var panel = document.getElementById('tos-result-panel');
            var result = tosTargetCache.result;
            var editable = tosEditMode;

            var totalsByLevel = {};
            TOS_BLOOM_LEVELS.forEach(function(l) { totalsByLevel[l] = 0; });

            var rows = result.topics.map(function(t, ti) {
                var levelCells = TOS_BLOOM_LEVELS.map(function(l) {
                    totalsByLevel[l] += t.levels[l].count;
                    return '<td style="' + TOS_CELL_STYLE + '">' + levelCellHtml(t.levels[l], editable, ti, l) + '</td>';
                }).join('');

                var hoursCell = editable
                    ? '<input type="number" min="0" class="tos-edit-input" data-ti="' + ti + '" data-field="hours" value="' + t.hours + '" style="width:44px;text-align:center;font-size:11px;padding:2px;border:1px solid #ccc;border-radius:3px;">'
                    : t.hours;

                return '<tr>' +
                    '<td style="padding:8px;border:1px solid #e0e0e0;">' + escapeHtml(t.topic) + '</td>' +
                    levelCells +
                    '<td style="padding:8px;border:1px solid #e0e0e0;text-align:center;">' + hoursCell + '</td>' +
                    '<td style="padding:8px;border:1px solid #e0e0e0;text-align:center;">' + t.weight_percent + '%</td>' +
                    '<td style="padding:8px;border:1px solid #e0e0e0;text-align:center;font-weight:700;">' + t.target_items + '</td>' +
                '</tr>';
            }).join('');

            var levelHeaders = TOS_BLOOM_LEVELS.map(function(l) {
                return '<th style="padding:8px;border:1px solid #e0e0e0;font-size:10.5px;">' + l + '</th>';
            }).join('');
            var levelTotals = TOS_BLOOM_LEVELS.map(function(l) {
                return '<td style="padding:8px;border:1px solid #e0e0e0;text-align:center;">' + totalsByLevel[l] + '</td>';
            }).join('');

            panel.innerHTML =
                '<div class="tos-panel-title">Generated Table of Specifications' + (editable ? ' <span style="font-weight:400;font-size:11px;color:#f59e0b;">(editing — click a cell to change it)</span>' : '') + '</div>' +
                '<div style="overflow-x:auto;"><table style="width:100%;border-collapse:collapse;font-size:11.5px;">' +
                '<thead><tr style="background:#f5f5f5;">' +
                '<th style="padding:8px;border:1px solid #e0e0e0;text-align:left;">Topics</th>' +
                levelHeaders +
                '<th style="padding:8px;border:1px solid #e0e0e0;">No. of<br>Hours</th>' +
                '<th style="padding:8px;border:1px solid #e0e0e0;">%</th>' +
                '<th style="padding:8px;border:1px solid #e0e0e0;">No. of<br>Items</th>' +
                '</tr></thead><tbody>' + rows + '</tbody>' +
                '<tfoot><tr style="background:#f5f5f5;font-weight:700;">' +
                '<td style="padding:8px;border:1px solid #e0e0e0;">TOTAL</td>' +
                levelTotals +
                '<td style="padding:8px;border:1px solid #e0e0e0;text-align:center;">' + result.total_hours + '</td>' +
                '<td style="padding:8px;border:1px solid #e0e0e0;text-align:center;">100%</td>' +
                '<td style="padding:8px;border:1px solid #e0e0e0;text-align:center;">' + result.total_items + '</td>' +
                '</tr></tfoot></table></div>' +
                '<div style="margin-top:14px;display:flex;gap:8px;justify-content:flex-end;">' +
                '<button class="tos-action-btn" type="button" onclick="toggleTosEdit()">' + (editable ? '💾 Done Editing' : '✏ Edit') + '</button>' +
                '<button class="tos-action-btn" type="button" onclick="downloadTos()">⬇ Download TOS</button>' +
                '<button class="tos-action-btn" type="button" onclick="startExamFromTOS()">Start Exam from this TOS →</button>' +
                '</div>';

            panel.onchange = onTosInputChange;
        }

        async function downloadTos() {
            if (!tosTargetCache) return;
            try {
                var res = await fetch('/faculty/exam-generator/tos-download', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({
                        program_assignment_id: tosTargetCache.assignmentId,
                        grading_period: tosTargetCache.period,
                        breakdown: JSON.stringify(tosTargetCache.result)
                    })
                });
                if (!res.ok) {
                    var err = await res.json().catch(function () { return {}; });
                    throw new Error(err.message || 'Download failed.');
                }
                var blob = await res.blob();
                var url = URL.createObjectURL(blob);
                var cd = res.headers.get('Content-Disposition') || '';
                var match = cd.match(/filename="?([^"]+)"?/);
                var a = document.createElement('a');
                a.href = url;
                a.download = match ? match[1] : 'TOS.docx';
                document.body.appendChild(a);
                a.click();
                a.remove();
                URL.revokeObjectURL(url);
            } catch (e) {
                alert(e.message);
            }
        }

        // Faculty already filled in Subject/Period/Duration/Target Items back in the TOS
        // Configuration panel — re-asking for them here (and losing Duration, which this
        // modal never pre-filled) was the reported bug. This is now a view-only recap of
        // what was already entered, with a single confirm action.
        var startExamViewData = null;

        function startExamFromTOS() {
            if (!tosTargetCache) return;
            var a = ASSIGNMENTS.find(function(x) { return String(x.id) === String(tosTargetCache.assignmentId); });
            var duration = document.getElementById('tos-duration').value || null;
            var targetItems = tosTargetCache.result.total_items;
            var title = tosTargetCache.period + ' Examination' + (a ? ' — ' + a.label : '');

            startExamViewData = {
                program_assignment_id: tosTargetCache.assignmentId,
                grading_period: tosTargetCache.period,
                title: title,
                duration_minutes: duration,
                target_items: targetItems
            };

            document.getElementById('sev-subject').textContent = a ? a.label : '—';
            document.getElementById('sev-period').textContent = tosTargetCache.period;
            document.getElementById('sev-duration').textContent = duration ? (duration + ' mins') : '— (not set in TOS Configuration)';
            document.getElementById('sev-title').textContent = title;
            document.getElementById('sev-target-items').textContent = targetItems;

            openStartExamView();
        }

        function openStartExamView() {
            document.getElementById('start-exam-error').style.display = 'none';
            document.getElementById('start-exam-view-overlay').classList.add('open');
        }
        function closeStartExamView() { document.getElementById('start-exam-view-overlay').classList.remove('open'); }
        document.getElementById('start-exam-view-overlay').addEventListener('click', function(e) { if (e.target === this) closeStartExamView(); });

        function confirmStartExamFromTOS() {
            if (!startExamViewData) return;
            submitCreateExam(startExamViewData, closeStartExamView, 'start-exam-error');
        }

        // ══════════════════════════════
        // NEW EXAM MODAL
        // ══════════════════════════════
        function openNewExamModal() {
            document.getElementById('new-exam-error').style.display = 'none';
            document.getElementById('new-exam-overlay').classList.add('open');
            checkTosRequirement();
        }
        function closeNewExamModal() { document.getElementById('new-exam-overlay').classList.remove('open'); }
        document.getElementById('new-exam-overlay').addEventListener('click', function(e) { if (e.target === this) closeNewExamModal(); });

        // Warns upfront, before submit, when the selected subject + grading period
        // combo has no TOS generated yet — mirrors the server-side guard in store()
        // so the faculty member isn't surprised by an error only after clicking Create.
        function checkTosRequirement() {
            var subjectId = document.getElementById('ne-subject').value;
            var period = document.getElementById('ne-period').value;
            var warning = document.getElementById('new-exam-tos-warning');
            var createBtn = document.getElementById('ne-create-btn');

            var periodsWithTos = subjectId ? (TOS_BY_ASSIGNMENT[subjectId] || []) : null;
            var hasTos = !subjectId || periodsWithTos.includes(period);

            warning.style.display = hasTos ? 'none' : 'block';
            createBtn.disabled = !hasTos;
            createBtn.style.opacity = hasTos ? '1' : '0.5';
            createBtn.style.cursor = hasTos ? 'pointer' : 'not-allowed';
        }

        async function createExam() {
            var body = {
                program_assignment_id: document.getElementById('ne-subject').value,
                grading_period: document.getElementById('ne-period').value,
                title: document.getElementById('ne-title').value,
                duration_minutes: document.getElementById('ne-duration').value || null,
                target_items: document.getElementById('ne-target-items').value || null
            };
            await submitCreateExam(body, closeNewExamModal, 'new-exam-error');
        }

        async function submitCreateExam(body, closeFn, errorElId) {
            var errorEl = errorElId ? document.getElementById(errorElId) : null;
            if (errorEl) errorEl.style.display = 'none';

            if (!body.program_assignment_id || !body.title) {
                if (errorEl) { errorEl.textContent = 'Subject and Title are required.'; errorEl.style.display = 'block'; }
                else alert('Subject and Title are required.');
                return;
            }

            try {
                var exam = await api('/faculty/exam-generator', { method: 'POST', body: body });
                if (closeFn) closeFn();
                // The server may return an already-existing draft instead of
                // creating a new one (same subject + grading period) — only
                // add a dropdown option if one for this exam isn't there yet.
                var select = document.getElementById('eb-exam-select');
                if (!select.querySelector('option[value="' + exam.id + '"]')) {
                    var opt = document.createElement('option');
                    opt.value = exam.id;
                    opt.textContent = exam.program_assignment.course.code + ' — ' + exam.title + ' (' + exam.grading_period + ')';
                    select.appendChild(opt);
                }
                select.value = exam.id;
                switchTab('exam-builder');
                await loadExam(exam.id);
            } catch (e) {
                if (errorEl) { errorEl.textContent = e.message; errorEl.style.display = 'block'; }
                else alert(e.message);
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
            var finalized = currentExam.status === 'finalized';
            document.getElementById('eb-type-label').textContent = currentExam.grading_period + (finalized ? ' 🔒 Finalized' : '');
            // Finalizing marks the exam as ready for printing/export — it no longer locks
            // out editing, so faculty can still fix a typo or add a question afterward.
            document.getElementById('btn-save-exam').disabled = false;
            document.getElementById('btn-preview-exam').disabled = false;
            document.getElementById('btn-finalize-exam').disabled = finalized;
            document.getElementById('btn-add-section').disabled = false;
            document.getElementById('eb-no-exam-notice').style.display = 'none';

            await loadExamTosTarget();
            renderEB();
            await refreshTosProgress();
        }

        // Fetches the TOS breakdown for this exam's target_items once per load — reused by
        // both assignDisplayBloomLevels() (question badges) and refreshTosProgress() (the
        // sidebar panel), instead of each fetching it separately.
        async function loadExamTosTarget() {
            examTosTargetCache = null;
            if (!currentExam || !currentExam.target_items) return;
            try {
                examTosTargetCache = await api('/faculty/exam-generator/tos-target', {
                    method: 'POST',
                    body: {
                        program_assignment_id: currentExam.program_assignment_id,
                        grading_period: currentExam.grading_period,
                        total_items: currentExam.target_items
                    }
                });
            } catch (e) {
                examTosTargetCache = null;
            }
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
        var pendingDeleteSectionId = null;
        function deleteSection(secId) {
            var sec = findSection(secId);
            pendingDeleteSectionId = secId;
            document.getElementById('delete-section-title').textContent = sec ? sec.title : 'this section';
            document.getElementById('delete-section-overlay').classList.add('open');
        }
        function closeDeleteSectionModal() {
            pendingDeleteSectionId = null;
            document.getElementById('delete-section-overlay').classList.remove('open');
        }
        function confirmDeleteSection() {
            if (!pendingDeleteSectionId) return;
            currentExam.sections = currentExam.sections.filter(function(s) { return String(s.id) !== String(pendingDeleteSectionId); });
            closeDeleteSectionModal();
            renderEB();
        }
        document.getElementById('delete-section-overlay').addEventListener('click', function(e) {
            if (e.target === this) closeDeleteSectionModal();
        });
        function updateSectionTitle(secId, val) {
            var sec = findSection(secId);
            if (sec) { sec.title = val; renderSectionsBar(); }
        }
        function updateSectionInstructions(secId, val) {
            var sec = findSection(secId);
            if (sec) sec.instructions = val;
        }

        function renderEB(opts) {
            if (!currentExam) return;
            renderSectionsBar();
            renderSectionsContent(opts);
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

        // Groups & orders each section's questions by Topic (OBTL order for this grading
        // period), then by Bloom's Taxonomy level (Remembering → Creating) within each
        // topic — so the displayed/saved item numbers (1, 2, 3...) line up with the TOS's
        // own "I.1-5 Remembering, I.6-10 Understanding, ..." ranges, per the instructor's
        // requirement that the exam itself stay aligned to the TOS and to Bloom's levels.
        // A question's best-known Bloom's Level before this render's position-resolution
        // runs: a manual override (this session) beats the level persisted from the last
        // save, which beats the type's own natural default. Used only to decide sort order
        // — assignDisplayBloomLevels() below still does the actual, authoritative
        // position-based resolution afterward, so an override "sticks" only if the topic's
        // quota for that level actually has room for it.
        function effectiveBloomHint(q) {
            return q.bloom_level || (QUESTION_TYPES[q.type] || {}).bloom || null;
        }

        function tosAlignQuestions(sec) {
            if (!currentExam) return;
            var byAssignment = TOPICS_BY_ASSIGNMENT[currentExam.program_assignment_id] || {};
            var topics = byAssignment[currentExam.grading_period] || [];
            var topicOrder = {};
            topics.forEach(function(t, i) { topicOrder[t.topic] = i; });

            function sortKey(q) {
                var bloom = effectiveBloomHint(q);
                var topic = q.topic;
                // Case Analysis carries no topic/bloom of its own — inherit from its
                // children so the container sorts near the level range it belongs to.
                if (q.type === 'case-analysis' && q.children && q.children.length) {
                    if (!topic) topic = q.children[0].topic;
                    var levels = q.children
                        .map(function(c) { return TOS_BLOOM_LEVELS.indexOf(effectiveBloomHint(c)); })
                        .filter(function(i) { return i !== -1; });
                    bloom = levels.length ? TOS_BLOOM_LEVELS[Math.min.apply(null, levels)] : null;
                }
                return {
                    topicIdx: (topic && topicOrder.hasOwnProperty(topic)) ? topicOrder[topic] : topics.length,
                    levelIdx: bloom ? TOS_BLOOM_LEVELS.indexOf(bloom) : TOS_BLOOM_LEVELS.length
                };
            }

            sec.questions = sec.questions
                .map(function(q, i) { return { q: q, i: i, key: sortKey(q) }; })
                .sort(function(a, b) {
                    if (a.key.topicIdx !== b.key.topicIdx) return a.key.topicIdx - b.key.topicIdx;
                    if (a.key.levelIdx !== b.key.levelIdx) return a.key.levelIdx - b.key.levelIdx;
                    return a.i - b.i;
                })
                .map(function(x) { return x.q; });
        }

        // Resolves each question's displayed Bloom's Level. If the question already has a
        // known bloom_level — because the faculty picked one via the dropdown, it came
        // back already-classified from AI generation, or it was loaded from a previous
        // save — that value wins and stays put (a manual pick must never silently revert,
        // it's what makes the dropdown actually usable). Only a genuinely fresh question
        // (never classified, no override) falls back to the TOS's own position-based
        // default. resolveLevel() below is still called for every question regardless, to
        // keep its per-topic position counter in sync with the server's — see
        // CourseTopic::resolveBloomLevelForPosition(), which does the same thing on save.
        function assignDisplayBloomLevels() {
            var levelsByTopic = {};
            if (examTosTargetCache && examTosTargetCache.topics) {
                examTosTargetCache.topics.forEach(function(t) { levelsByTopic[t.topic] = t.levels; });
            }
            var topicPositions = {};

            function resolveLevel(topic) {
                var levels = levelsByTopic[topic];
                if (!levels) return null;
                topicPositions[topic] = (topicPositions[topic] || 0) + 1;
                var position = topicPositions[topic];
                var cursor = 0, lastLevel = null;
                for (var i = 0; i < TOS_BLOOM_LEVELS.length; i++) {
                    var l = TOS_BLOOM_LEVELS[i];
                    var count = levels[l].count;
                    if (count > 0) lastLevel = l;
                    if (position <= cursor + count) return l;
                    cursor += count;
                }
                return lastLevel;
            }

            function walk(q) {
                if (q.type === 'case-analysis') {
                    q._displayBloom = null;
                    (q.children || []).forEach(walk);
                    return;
                }
                var positional = q.topic ? resolveLevel(q.topic) : null;
                q._displayBloom = q.bloom_level || positional || (QUESTION_TYPES[q.type] || {}).bloom || null;
            }

            currentExam.sections.forEach(function(sec) {
                (sec.questions || []).forEach(walk);
            });
        }

        function renderSectionsContent(opts) {
            opts = opts || {};
            var container = document.getElementById('eb-sections-content');
            container.innerHTML = '';
            // Finalized exams stay fully editable (see loadExam()) — this is kept as an
            // empty string, not removed outright, so every '+ disabled' concatenation below
            // still resolves cleanly without touching each call site individually.
            var disabled = '';

            // Skipped right after a manual Bloom's Level pick (see
            // setQuestionBloomOverride()) — re-sorting immediately would relocate the very
            // question the faculty just edited, making it look like a DIFFERENT (whichever
            // one slides into its old slot) question changed instead. The edited question
            // still gets swept into its TOS-aligned position the next time something
            // structural happens (add/delete/copy a question, change a topic, generate more,
            // or reload).
            if (!opts.skipSort) {
                currentExam.sections.forEach(function(sec) { tosAlignQuestions(sec); });
            }
            assignDisplayBloomLevels();

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

            // Bloom's Level defaults to whatever this question's position within its topic
            // resolves to against the TOS breakdown (assignDisplayBloomLevels()) until the
            // faculty picks one here — a pick sticks permanently (saved as-is, never
            // silently recomputed) and also moves the question to sort among its topic's
            // other questions at that level.
            var displayBloom = q.type === 'case-analysis' ? null : (q._displayBloom || ct.bloom || null);
            var bloomBadge = displayBloom
                ? '<select onchange="setQuestionBloomOverride(\'' + secId + '\',' + qi + ',' + ciArg + ',this.value)" ' +
                    'title="Auto-assigned from the TOS by default — override if needed" ' +
                    'style="font-size:11px;padding:3px 6px;border:1px solid #ccc;border-radius:4px;font-weight:600;color:#0f2557;">' +
                    TOS_BLOOM_LEVELS.map(function(l) {
                        return '<option value="' + l + '"' + (l === displayBloom ? ' selected' : '') + '>' + l + '</option>';
                    }).join('') +
                  '</select>'
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
        function setQuestionTopic(secId, qi, ci, value) { var q = findQuestion(secId, qi, ci); if (q) { q.topic = value || null; renderEB(); } }
        // Manually setting a question's Bloom's Level pins it — it's saved as-is (see
        // serializeQuestion()) and won't be silently recomputed on later renders/saves,
        // though it still shifts where the question sorts among its topic's others.
        function setQuestionBloomOverride(secId, qi, ci, value) { var q = findQuestion(secId, qi, ci); if (q) { q.bloom_level = value || null; renderEB({ skipSort: true }); } }
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
                return '<option value="' + t.id + '" data-has-content="' + (t.has_content ? '1' : '0') + '">' +
                    escapeHtml(t.topic) + ' (' + t.hours + ' hrs)' + (t.has_content ? '' : ' — no notes/module yet') +
                '</option>';
            }).join('');
        }

        // Warns immediately (instead of only after a failed submit) when the selected
        // topic has no Teaching Notes or Module — the AI would have nothing to read.
        function onAiGenTopicChange() {
            var select = document.getElementById('ai-gen-topic');
            var opt = select.options[select.selectedIndex];
            var hasContent = opt ? opt.dataset.hasContent === '1' : true;
            document.getElementById('ai-gen-no-content-warning').style.display = hasContent ? 'none' : 'block';
            document.getElementById('ai-gen-submit').disabled = !hasContent;
        }

        function openAiGenModal(secId) {
            if (!currentExam) return;
            aiGenTarget = secId;
            document.getElementById('ai-gen-topic').innerHTML = '<option value="" disabled selected>Select topic</option>' + aiGenTopicOptionsHtml();
            document.getElementById('ai-gen-error').style.display = 'none';
            document.getElementById('ai-gen-no-content-warning').style.display = 'none';
            document.getElementById('ai-gen-submit').disabled = false;
            document.getElementById('ai-gen-overlay').classList.add('open');
        }
        function closeAiGenModal() { document.getElementById('ai-gen-overlay').classList.remove('open'); }
        document.getElementById('ai-gen-overlay').addEventListener('click', function(e) { if (e.target === this) closeAiGenModal(); });

        // How many questions this exam already has under a topic — the AI batch continues
        // from that position so the TOS/Bloom's-level split (server-side) picks up where
        // the existing questions left off instead of always starting back at Remembering.
        function countExistingForTopic(topicName) {
            var count = 0;
            function walk(q) {
                if (q.type !== 'case-analysis' && q.topic === topicName) count++;
                (q.children || []).forEach(walk);
            }
            currentExam.sections.forEach(function(sec) { (sec.questions || []).forEach(walk); });
            return count;
        }

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

            var byAssignment = TOPICS_BY_ASSIGNMENT[currentExam.program_assignment_id] || {};
            var topics = byAssignment[currentExam.grading_period] || [];
            var topicRow = topics.find(function(t) { return String(t.id) === String(topicId); });
            var existingCount = topicRow ? countExistingForTopic(topicRow.topic) : 0;

            var btn = document.getElementById('ai-gen-submit');
            btn.disabled = true;
            btn.textContent = 'Generating…';

            try {
                var result = await api('/faculty/exam-generator/generate-questions', {
                    method: 'POST',
                    body: {
                        course_topic_id: topicId,
                        type: type,
                        count: count,
                        existing_count: existingCount,
                        target_items: currentExam.target_items || null
                    }
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
                // Whatever Bloom's Level is currently known for this question (from a
                // manual pick, AI generation, or a previous save) — sent so it's preserved
                // instead of silently re-derived. A genuinely new/never-classified question
                // has none yet, so the server auto-resolves it from the TOS the first time.
                bloom_level: q.bloom_level || null,
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
                openSaveResult(true, 'Exam saved successfully.');
            } catch (e) {
                openSaveResult(false, 'Save failed: ' + e.message);
            }
        }

        function openSaveResult(success, message) {
            document.getElementById('save-result-title').textContent = success ? '✓ Exam saved' : '⚠️ Save failed';
            document.getElementById('save-result-message').textContent = message;
            document.getElementById('save-result-overlay').classList.add('open');
        }
        function closeSaveResult() { document.getElementById('save-result-overlay').classList.remove('open'); }
        document.getElementById('save-result-overlay').addEventListener('click', function(e) { if (e.target === this) closeSaveResult(); });

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

        function finalizeExam() {
            if (!currentExam) return;
            document.getElementById('finalize-confirm-overlay').classList.add('open');
        }
        function closeFinalizeConfirm() { document.getElementById('finalize-confirm-overlay').classList.remove('open'); }
        document.getElementById('finalize-confirm-overlay').addEventListener('click', function(e) { if (e.target === this) closeFinalizeConfirm(); });

        async function confirmFinalize() {
            closeFinalizeConfirm();
            if (!currentExam) return;
            try {
                await fetch('/faculty/exam-generator/' + currentExam.id + '/finalize', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
                });
                await loadExam(currentExam.id);
            } catch (e) {
                openSaveResult(false, e.message);
            }
        }

        // ══════════════════════════════
        // LIVE TOS PROGRESS (actual vs. target)
        // ══════════════════════════════
        async function refreshTosProgress() {
            var container = document.getElementById('eb-tos-progress');
            if (!currentExam || !currentExam.target_items) { container.innerHTML = ''; return; }

            var target = examTosTargetCache;
            if (!target) { container.innerHTML = ''; return; }

            var actual;
            try {
                actual = await api('/faculty/exam-generator/' + currentExam.id + '/tos');
            } catch (e) {
                container.innerHTML = '';
                return;
            }

            var LEVEL_ABBR = { Remembering: 'Rem', Understanding: 'Und', Applying: 'App', Analyzing: 'Ana', Evaluating: 'Eval', Creating: 'Create' };

            var rows = (target.topics || []).map(function(t) {
                var a = actual.topics[t.topic] || { items: 0, bloom_breakdown: {} };
                var levelBits = TOS_BLOOM_LEVELS.map(function(l) {
                    var need = t.levels[l].count;
                    if (need === 0) return '';
                    var have = (a.bloom_breakdown[l] || {}).count || 0;
                    var ok = have >= need;
                    return '<span style="display:inline-block;margin-right:10px;color:' + (ok ? '#16a34a' : '#c2410c') + ';">' +
                        LEVEL_ABBR[l] + ' ' + have + '/' + need +
                    '</span>';
                }).join('');

                return '<div style="padding:6px 0;border-bottom:1px solid #f0f0f0;">' +
                    '<div style="display:flex;justify-content:space-between;font-size:11.5px;">' +
                        '<span>' + escapeHtml(t.topic) + '</span>' +
                        '<span style="color:' + (a.items >= t.target_items ? '#16a34a' : '#888') + ';">' + a.items + ' / ' + t.target_items + ' items</span>' +
                    '</div>' +
                    '<div style="margin-top:3px;font-size:10.5px;">' + levelBits + '</div>' +
                '</div>';
            }).join('');

            container.innerHTML = '<div class="obe-section" style="margin-bottom:12px;">' +
                '<div class="obe-header"><span class="obe-title">TOS Progress — ' + actual.total_items + ' / ' + currentExam.target_items + ' total items</span></div>' +
                '<div style="font-size:10.5px;color:#888;margin:4px 0 6px;">Questions are auto-ordered per topic, Remembering → Creating, to match the TOS.</div>' +
                rows +
            '</div>';
        }

        // ══════════════════════════════
        // ITEM BANK
        // ══════════════════════════════
        var allBankItems = [];

        async function loadItemBank() {
            var courseId = document.getElementById('bank-course-select').value;
            var container = document.getElementById('bank-items-container');
            var topicSelect = document.getElementById('bank-topic-select');
            var bloomSelect = document.getElementById('bank-bloom-select');
            if (!courseId) return;

            container.innerHTML = '<div class="tab-empty">Loading…</div>';
            topicSelect.disabled = true;
            bloomSelect.disabled = true;
            bloomSelect.value = '';

            try {
                allBankItems = await api('/faculty/exam-generator/item-bank?course_id=' + courseId);
            } catch (e) {
                container.innerHTML = '<div class="tab-empty" style="color:#ef4444;">' + escapeHtml(e.message) + '</div>';
                return;
            }

            // Topic options depend on what's actually in this subject's items —
            // rebuilt every time the subject changes instead of listing every
            // topic across every subject.
            var topics = Array.from(new Set(allBankItems.map(function(i) { return i.topic; }).filter(Boolean))).sort();
            topicSelect.innerHTML = '<option value="">All Topics</option>' +
                topics.map(function(t) { return '<option value="' + escapeAttr(t) + '">' + escapeHtml(t) + '</option>'; }).join('');
            topicSelect.value = '';
            topicSelect.disabled = allBankItems.length === 0;
            bloomSelect.disabled = allBankItems.length === 0;

            renderBankItems();
        }

        function renderBankItems() {
            var container = document.getElementById('bank-items-container');
            var topicFilter = document.getElementById('bank-topic-select').value;
            var bloomFilter = document.getElementById('bank-bloom-select').value;

            if (allBankItems.length === 0) {
                container.innerHTML = '<div class="tab-empty"><div class="empty-icon">🗃️</div>No reusable items yet for this subject. Add questions in the Exam Builder first.</div>';
                return;
            }

            var items = allBankItems.filter(function(item) {
                var matchesTopic = !topicFilter || item.topic === topicFilter;
                var matchesBloom = !bloomFilter || item.bloom_level === bloomFilter;
                return matchesTopic && matchesBloom;
            });

            if (items.length === 0) {
                container.innerHTML = '<div class="tab-empty">No items match this filter.</div>';
                return;
            }

            container.innerHTML = items.map(function(item) {
                var ct = QUESTION_TYPES[item.type] || {};
                var bloomLabel = item.bloom_level || ct.bloom;
                return '<div class="question-card" style="margin-bottom:10px;">' +
                    '<div class="q-header"><div class="q-num" style="flex:1;">' + escapeHtml(item.question_text || '(Case Analysis scenario)') + '</div>' +
                    '<button class="btn-q-action" onclick="reuseBankItem(' + item.id + ')" title="Add to current exam" ' + (currentExam ? '' : 'disabled') + '>+ Add</button></div>' +
                    '<div class="q-footer"><span class="q-type-label">' + (ct.label || item.type) + '</span>' +
                    (bloomLabel ? '<span class="q-blooms-badge">' + bloomLabel + '</span>' : '') +
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