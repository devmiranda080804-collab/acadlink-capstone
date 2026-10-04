<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Repository – CBMA System</title>
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
        .nav-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 16px; height: 16px; padding: 0 4px; margin-left: auto; background: #ef4444; color: #fff; font-size: 10px; font-weight: 700; border-radius: 999px; }
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
        .alert-error { background: #fee2e2; color: #b91c1c; padding: 12px 16px; margin-bottom: 16px; border: 1px solid #fca5a5; border-radius: 8px; font-size: 13px; }

        .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 24px; }
        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 3px; }
        .page-sub { font-size: 11.5px; color: #888; }
        .btn-upload { display: flex; align-items: center; gap: 6px; background: #0f2557; color: #fff; border: none; border-radius: 6px; font-size: 12.5px; font-weight: 600; padding: 9px 18px; cursor: pointer; white-space: nowrap; }
        .btn-upload:hover { background: #1a3a7a; }

        .repo-layout { display: grid; grid-template-columns: 260px 1fr; gap: 20px; align-items: start; }

        /* Folder tree */
        .folder-panel { background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; padding: 18px; position: sticky; top: 0; }
        .folder-panel-title { font-size: 13px; font-weight: 700; color: #1a1a2e; margin-bottom: 14px; }
        .folder-all { display: flex; align-items: center; justify-content: space-between; gap: 7px; padding: 8px; border-radius: 6px; cursor: pointer; font-size: 12.5px; font-weight: 600; color: #1a1a2e; margin-bottom: 8px; border: 1px solid #e4e4e4; }
        .folder-all:hover { background: #f0f4ff; }
        .folder-all.active { background: #eef2ff; border-color: #c7d2fe; color: #0f2557; }
        .folder-year { margin-bottom: 6px; }
        .folder-year-head { display: flex; align-items: center; gap: 7px; padding: 7px 8px; border-radius: 6px; cursor: pointer; font-size: 12.5px; font-weight: 600; color: #1a1a2e; user-select: none; }
        .folder-year-head:hover { background: #f0f4ff; }
        .folder-year-head svg { width: 14px; height: 14px; color: #d97706; flex-shrink: 0; }
        .folder-year-head .caret { width: 10px; height: 10px; color: #999; transition: transform 0.15s; }
        .folder-year.open .caret { transform: rotate(90deg); }
        .folder-year-head .count { margin-left: auto; font-size: 10.5px; font-weight: 600; color: #888; }
        .folder-sems { display: none; padding-left: 22px; margin-top: 2px; }
        .folder-year.open .folder-sems { display: block; }
        .folder-sem { display: flex; align-items: center; gap: 7px; padding: 6px 8px; border-radius: 6px; cursor: pointer; font-size: 12px; color: #555; user-select: none; }
        .folder-sem:hover { background: #f0f4ff; color: #0f2557; }
        .folder-sem.active { background: #eef2ff; color: #0f2557; font-weight: 600; }
        .folder-sem svg { width: 13px; height: 13px; color: #d97706; flex-shrink: 0; }
        .folder-sem .count { margin-left: auto; font-size: 11px; color: #888; }
        .folder-sem.is-empty { color: #aaa; }
        .folder-sem.is-empty .count { color: #bbb; }

        /* File list */
        .files-panel { background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; overflow: hidden; }
        .files-breadcrumb { display: flex; align-items: center; gap: 8px; padding: 13px 18px; border-bottom: 1px solid #f0f0f0; background: #fafafa; font-size: 12.5px; color: #666; }
        .files-breadcrumb svg { width: 14px; height: 14px; color: #d97706; }
        .files-breadcrumb .sep { color: #ccc; }
        .files-breadcrumb .crumb-current { font-weight: 700; color: #1a1a2e; }

        .repo-toolbar { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; padding: 12px 18px; border-bottom: 1px solid #f0f0f0; }
        .repo-search { flex: 1; min-width: 180px; padding: 7px 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 12.5px; outline: none; }
        .repo-search:focus { border-color: #0f2557; }
        .repo-filter { padding: 7px 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 12px; color: #333; background: #fff; outline: none; }
        .repo-filter:focus { border-color: #0f2557; }

        .files-table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
        .files-table thead tr { background: #fff; border-bottom: 1px solid #eee; }
        .files-table th { padding: 11px 18px; text-align: left; font-size: 11.5px; font-weight: 700; color: #666; }
        .files-table td { padding: 12px 18px; border-bottom: 1px solid #f5f5f5; color: #333; vertical-align: middle; }
        .files-table tbody tr:hover { background: #fafbff; }

        .doc-name { display: flex; align-items: center; gap: 8px; }
        .doc-name .ficon { font-size: 15px; }
        .doc-name .dtitle { font-weight: 600; color: #1a1a2e; }
        .folder-tag { display: inline-block; font-size: 11px; color: #555; background: #f5f6fa; border: 1px solid #e4e4e4; padding: 2px 8px; border-radius: 4px; white-space: nowrap; }
        .source-badge { font-size: 10px; font-weight: 700; padding: 3px 9px; border-radius: 10px; }
        .source-template { background: #dbeafe; color: #1e40af; }
        .source-material { background: #d1fae5; color: #065f46; }
        .source-upload { background: #ede9fe; color: #6d28d9; }
        .program-tag { display: inline-block; font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 10px; background: #f0f4ff; color: #0f2557; }

        .action-buttons { display: flex; gap: 6px; }
        .btn-view { display: inline-flex; align-items: center; gap: 4px; background: #fff; color: #444; border: 1px solid #d0d0d0; font-size: 11px; font-weight: 600; padding: 6px 12px; border-radius: 5px; text-decoration: none; }
        .btn-view:hover { background: #f5f5f5; }
        .btn-del { background: none; border: none; color: #ccc; cursor: pointer; padding: 5px 8px; border-radius: 4px; }
        .btn-del:hover { color: #ef4444; background: #fee2e2; }
        .action-buttons svg { width: 12px; height: 12px; }

        .files-empty { text-align: center; padding: 50px 20px; color: #bbb; font-size: 12.5px; }

        .repo-pager { display: flex; justify-content: space-between; align-items: center; gap: 10px; padding: 12px 18px; border-top: 1px solid #f0f0f0; font-size: 12px; color: #666; flex-wrap: wrap; }
        .repo-pager-btns { display: flex; gap: 8px; align-items: center; }
        .repo-pager button { background: #fff; border: 1px solid #d0d0d0; color: #444; font-size: 12px; font-weight: 600; padding: 6px 12px; border-radius: 5px; cursor: pointer; }
        .repo-pager button:disabled { opacity: 0.45; cursor: default; }
        .repo-pager button:not(:disabled):hover { background: #f5f5f5; }

        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 999; align-items: center; justify-content: center; }
        .modal-overlay.open { display: flex; }
        .modal { background: #fff; border-radius: 10px; padding: 24px 26px; width: 460px; max-width: 95vw; box-shadow: 0 8px 32px rgba(0,0,0,0.25); }
        .modal-title { font-size: 15px; font-weight: 700; color: #1a1a2e; margin-bottom: 18px; }
        .modal-field { margin-bottom: 13px; }
        .modal-field label { display: block; font-size: 11.5px; font-weight: 700; color: #333; margin-bottom: 4px; }
        .modal-field input[type=text], .modal-field input[type=file], .modal-field select { width: 100%; padding: 8px 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 12.5px; color: #333; outline: none; }
        .modal-field input:focus, .modal-field select:focus { border-color: #0f2557; }
        .modal-field select { appearance: none; -webkit-appearance: none; background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%23666' d='M5 7L0 2h10z'/%3E%3C/svg%3E") no-repeat right 10px center; cursor: pointer; }
        .modal-hint { font-size: 10.5px; color: #999; margin-top: 4px; }
        .modal-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .modal-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 18px; }
        .btn-cancel { background: #fff; border: 1px solid #ccc; color: #444; font-size: 12.5px; font-weight: 600; padding: 8px 18px; border-radius: 5px; cursor: pointer; }
        .btn-cancel:hover { background: #f5f5f5; }
        .btn-save { background: #0f2557; color: #fff; border: none; font-size: 12.5px; font-weight: 600; padding: 8px 20px; border-radius: 5px; cursor: pointer; }
        .btn-save:hover { background: #1a3a7a; }

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
            <li class="{{ request()->is('secretary/dashboard') ? 'active' : '' }}"><a href="{{ url('/secretary/dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>Dashboard</a></li>
            @endif
            @if($navPermissions['document-repository'] ?? true)
            <li class="{{ request()->is('secretary/document-repository*') ? 'active' : '' }}"><a href="{{ url('/secretary/document-repository') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>Document Repository</a></li>
            @endif
            @if($navPermissions['template-distribution'] ?? true)
            <li class="{{ request()->is('secretary/template-distribution*') ? 'active' : '' }}"><a href="{{ url('/secretary/template-distribution') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>Template Distribution</a></li>
            @endif
            @if($navPermissions['course-filing'] ?? true)
            <li class="{{ request()->is('secretary/course-filing*') ? 'active' : '' }}"><a href="{{ url('/secretary/course-filing') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>Course Filing</a></li>
            @endif
            @if($navPermissions['account-management'] ?? true)
            <li class="{{ request()->is('secretary/account-management*') ? 'active' : '' }}"><a href="{{ url('/secretary/account-management') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>Account Management</a></li>
            @endif
            @if($navPermissions['announcements'] ?? true)
            <li class="{{ request()->is('secretary/announcements*') ? 'active' : '' }}"><a href="{{ url('/secretary/announcements') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>Announcements
                @if(($unreadAnnouncementsCount ?? 0) > 0)
                    <span class="nav-badge">{{ $unreadAnnouncementsCount }}</span>
                @endif
            </a></li>
            @endif
            @if($navPermissions['calendar'] ?? true)
            <li class="{{ request()->is('secretary/calendar*') ? 'active' : '' }}"><a href="{{ url('/secretary/calendar') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Calendar of Activities</a></li>
            @endif
        </ul>
        <div class="sidebar-logout">
            <a href="#" onclick="openLogoutModal()">
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
                <span class="role-badge">Secretary</span>
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
            @if($errors->any())
                <div class="alert-error">{{ $errors->first() }}</div>
            @endif

            <div class="page-header">
                <div>
                    <div class="page-title">Document Repository</div>
                    <div class="page-sub">Centralized file management system</div>
                </div>
                <button class="btn-upload" onclick="openUploadModal()">
                    <svg style="width:14px;height:14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    Upload Document
                </button>
            </div>

            @if($totalCount === 0)
                <div class="files-panel"><div class="files-empty">📁 No documents in the repository yet. Upload a file or approve templates for them to appear here.</div></div>
            @else
                <div class="repo-layout">
                    {{-- Folder tree --}}
                    <div class="folder-panel">
                        <div class="folder-panel-title">Folders</div>
                        <div class="folder-all active" data-folder="all" onclick="selectFolder(this)">
                            <span>All Documents</span>
                            <span class="count">{{ $totalCount }}</span>
                        </div>
                        @foreach($tree as $sy => $sems)
                            @php $yearTotal = collect($sems)->sum(fn($files) => count($files)); @endphp
                            <div class="folder-year {{ $loop->first ? 'open' : '' }}">
                                <div class="folder-year-head" onclick="toggleYear(this)">
                                    <svg class="caret" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="9 18 15 12 9 6"/></svg>
                                    <svg viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M10 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
                                    {{ $sy }}
                                    <span class="count">{{ $yearTotal }}</span>
                                </div>
                                <div class="folder-sems">
                                    @foreach($sems as $sem => $files)
                                        <div class="folder-sem {{ count($files) === 0 ? 'is-empty' : '' }}"
                                             data-folder="{{ Str::slug($sy . '-' . $sem) }}"
                                             data-label="{{ $sy }} · {{ $sem }}"
                                             onclick="selectFolder(this)">
                                            <svg viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M10 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
                                            {{ $sem }}
                                            <span class="count">{{ count($files) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Files --}}
                    <div class="files-panel">
                        <div class="files-breadcrumb">
                            <svg viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M10 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
                            <span class="crumb-current" id="crumb-label">All Documents</span>
                        </div>

                        <div class="repo-toolbar">
                            <input type="text" class="repo-search" id="repo-search" placeholder="Search by title, type, or uploader..." oninput="renderRepo(true)">
                            <select class="repo-filter" id="repo-source" onchange="renderRepo(true)">
                                <option value="">All sources</option>
                                <option value="template">Template</option>
                                <option value="material">Material</option>
                                <option value="upload">Uploaded</option>
                            </select>
                            <select class="repo-filter" id="repo-program" onchange="renderRepo(true)">
                                <option value="">All programs</option>
                                <option value="General">General</option>
                                @foreach(\App\Support\Programs::options() as $code => $label)
                                    <option value="{{ $code }}">{{ $code }}</option>
                                @endforeach
                            </select>
                        </div>

                        <table class="files-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Folder</th>
                                    <th>Source</th>
                                    <th>Type</th>
                                    <th>Program</th>
                                    <th>Uploaded By</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="repo-rows">
                                @foreach($tree as $sy => $sems)
                                    @foreach($sems as $sem => $files)
                                        @foreach($files as $doc)
                                            <tr class="doc-row"
                                                data-folder="{{ Str::slug($sy . '-' . $sem) }}"
                                                data-search="{{ strtolower($doc['title'] . ' ' . $doc['type'] . ' ' . $doc['uploader']) }}"
                                                data-program="{{ $doc['program'] }}"
                                                data-source="{{ $doc['source'] }}">
                                                <td>
                                                    <div class="doc-name">
                                                        <span class="ficon">@if($doc['file_type'] == 'pdf') 📄 @elseif(in_array($doc['file_type'], ['xls','xlsx'])) 📊 @else 📝 @endif</span>
                                                        <span class="dtitle">{{ $doc['title'] }}</span>
                                                    </div>
                                                </td>
                                                <td><span class="folder-tag">{{ $sy }} · {{ $sem }}</span></td>
                                                <td>
                                                    @if($doc['source'] == 'template')
                                                        <span class="source-badge source-template">Template</span>
                                                    @elseif($doc['source'] == 'material')
                                                        <span class="source-badge source-material">Material</span>
                                                    @else
                                                        <span class="source-badge source-upload">Uploaded</span>
                                                    @endif
                                                </td>
                                                <td>{{ $doc['type'] }}</td>
                                                <td><span class="program-tag" title="{{ \App\Support\Programs::label($doc['program']) }}">{{ $doc['program'] }}</span></td>
                                                <td>{{ $doc['uploader'] }}</td>
                                                <td>{{ $doc['date']->format('M d, Y') }}</td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <a class="btn-view" href="{{ $doc['file_url'] }}" target="_blank">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                                            View
                                                        </a>
                                                        @if($doc['can_delete'])
                                                            <form method="POST" action="{{ url('/secretary/document-repository/' . $doc['id']) }}" onsubmit="return confirm('Remove this document?')" style="display:inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn-del" title="Delete">
                                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>

                        <div class="files-empty" id="repo-empty" style="display:none;">📂 No documents match this folder or search.</div>

                        <div class="repo-pager">
                            <span id="repo-count"></span>
                            <div class="repo-pager-btns">
                                <button type="button" id="repo-prev" onclick="changePage(-1)">← Prev</button>
                                <span id="repo-page-label"></span>
                                <button type="button" id="repo-next" onclick="changePage(1)">Next →</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Upload Modal --}}
    <div class="modal-overlay" id="upload-overlay">
        <div class="modal">
            <form method="POST" action="{{ url('/secretary/document-repository') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-title">Upload Document</div>
                <div class="modal-field">
                    <label>Title <span style="color:#ef4444">*</span></label>
                    <input type="text" name="title" placeholder="e.g. Faculty Meeting Memo" required>
                </div>
                <div class="modal-row">
                    <div class="modal-field">
                        <label>Program</label>
                        <select name="program">
                            <option value="">General (All)</option>
                            @foreach(\App\Support\Programs::options() as $code => $label)
                                <option value="{{ $code }}">{{ $code }} — {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-field">
                        <label>Type <span style="color:#ef4444">*</span></label>
                        <select name="doc_type" required>
                            @foreach(\App\Models\TemplateDocument::TYPES as $key => $meta)
                                <option value="{{ $key }}">{{ $meta['label'] }}</option>
                            @endforeach
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="modal-field">
                    <label>File <span style="color:#ef4444">*</span></label>
                    <input type="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx" required>
                    <div class="modal-hint">Allowed: PDF, Word, Excel · Max 20MB · Automatically sorted by school year and semester based on today's date.</div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeUploadModal()">Cancel</button>
                    <button type="submit" class="btn-save">Upload</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        var repo = { folder: 'all', page: 1, perPage: 10 };

        function toggleYear(el) {
            el.parentElement.classList.toggle('open');
        }

        function selectFolder(el) {
            document.querySelectorAll('.folder-all, .folder-sem').forEach(f => f.classList.remove('active'));
            el.classList.add('active');
            repo.folder = el.dataset.folder;
            document.getElementById('crumb-label').textContent = el.dataset.label || 'All Documents';
            renderRepo(true);
        }

        function changePage(delta) {
            repo.page += delta;
            renderRepo(false);
        }

        function renderRepo(resetPage) {
            if (resetPage) repo.page = 1;

            const q = document.getElementById('repo-search').value.trim().toLowerCase();
            const source = document.getElementById('repo-source').value;
            const program = document.getElementById('repo-program').value;

            const rows = Array.from(document.querySelectorAll('#repo-rows tr.doc-row'));
            const matched = rows.filter(r =>
                (repo.folder === 'all' || r.dataset.folder === repo.folder) &&
                (!q || r.dataset.search.includes(q)) &&
                (!source || r.dataset.source === source) &&
                (!program || r.dataset.program === program)
            );

            const pages = Math.max(1, Math.ceil(matched.length / repo.perPage));
            if (repo.page > pages) repo.page = pages;
            if (repo.page < 1) repo.page = 1;

            const start = (repo.page - 1) * repo.perPage;
            const visible = new Set(matched.slice(start, start + repo.perPage));

            rows.forEach(r => { r.style.display = visible.has(r) ? '' : 'none'; });

            document.getElementById('repo-empty').style.display = matched.length ? 'none' : 'block';
            document.getElementById('repo-count').textContent = matched.length
                ? 'Showing ' + (start + 1) + '–' + (start + visible.size) + ' of ' + matched.length
                : '0 documents';
            document.getElementById('repo-page-label').textContent = 'Page ' + repo.page + ' of ' + pages;
            document.getElementById('repo-prev').disabled = repo.page <= 1;
            document.getElementById('repo-next').disabled = repo.page >= pages;
        }

        function openUploadModal() { document.getElementById('upload-overlay').classList.add('open'); }
        function closeUploadModal() { document.getElementById('upload-overlay').classList.remove('open'); }
        document.getElementById('upload-overlay').addEventListener('click', function(e) { if (e.target === this) closeUploadModal(); });

        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('repo-rows')) renderRepo(true);
        });
    </script>


    <div id="logout-confirm-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:9999;align-items:center;justify-content:center;">
        <div style="background:#fff;border-radius:10px;padding:24px 26px;width:360px;max-width:92vw;box-shadow:0 8px 32px rgba(0,0,0,0.25);font-family:Arial,sans-serif;">
            <div style="font-size:15px;font-weight:700;color:#1a1a2e;margin-bottom:8px;">Log out?</div>
            <div style="font-size:13px;color:#555;margin-bottom:20px;">Are you sure you want to log out of your account?</div>
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <button type="button" onclick="document.getElementById('logout-confirm-overlay').style.display='none';" style="background:#fff;border:1px solid #ccc;color:#444;font-size:12.5px;font-weight:600;padding:8px 18px;border-radius:5px;cursor:pointer;font-family:Arial,sans-serif;">Cancel</button>
            <button type="button" onclick="document.getElementById('logout-form').submit();" style="background:#ef4444;color:#fff;border:none;font-size:12.5px;font-weight:600;padding:8px 18px;border-radius:5px;cursor:pointer;font-family:Arial,sans-serif;">Log Out</button>
            </div>
        </div>
    </div>
    <script>
        function openLogoutModal() { document.getElementById('logout-confirm-overlay').style.display = 'flex'; }
        document.getElementById('logout-confirm-overlay').addEventListener('click', function(e) { if (e.target === this) this.style.display = 'none'; });
    </script>

</body>
</html>
