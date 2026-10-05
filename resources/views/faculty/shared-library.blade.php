<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shared Library – CBMA System</title>
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

        .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 20px; }
        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 3px; }
        .page-sub { font-size: 11.5px; color: #888; }
        .btn-share { display: flex; align-items: center; gap: 6px; background: #0f2557; color: #fff; border: none; border-radius: 6px; font-size: 12.5px; font-weight: 600; padding: 9px 18px; cursor: pointer; white-space: nowrap; }
        .btn-share:hover { background: #1a3a7a; }

        .toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 22px; flex-wrap: wrap; }

        .search-box { position: relative; width: 280px; max-width: 100%; }
        .search-box svg { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: #999; pointer-events: none; }
        .search-box input { width: 100%; padding: 9px 10px 9px 30px; border: 1px solid #ddd; border-radius: 6px; font-size: 12.5px; outline: none; font-family: Arial, sans-serif; }
        .search-box input:focus { border-color: #0f2557; }

        {{-- Folder (library-shelf) browsing --}}
        .folder-section { margin-bottom: 26px; }
        .folder-section-title { display: flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 700; color: #1a1a2e; margin-bottom: 12px; }
        .folder-section-count { font-size: 10.5px; font-weight: 600; color: #999; background: #f0f0f0; padding: 2px 8px; border-radius: 10px; }
        .folder-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); gap: 14px; }
        .folder-card { display: flex; align-items: center; gap: 12px; background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; padding: 14px 16px; cursor: pointer; transition: box-shadow 0.15s, transform 0.15s, border-color 0.15s; text-align: left; }
        .folder-card:hover { box-shadow: 0 6px 18px rgba(15,37,87,0.1); transform: translateY(-2px); border-color: #c7d2e8; }
        .folder-card-icon { font-size: 30px; line-height: 1; flex-shrink: 0; }
        .folder-card-label { font-size: 12.5px; font-weight: 700; color: #1a1a2e; line-height: 1.3; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
        .folder-card-count { font-size: 10.5px; color: #999; margin-top: 3px; }

        .breadcrumb { display: flex; align-items: center; gap: 10px; margin-bottom: 18px; }
        .btn-back { display: inline-flex; align-items: center; gap: 5px; background: #fff; border: 1px solid #ddd; color: #444; font-size: 12px; font-weight: 600; padding: 7px 13px; border-radius: 6px; cursor: pointer; }
        .btn-back:hover { border-color: #0f2557; color: #0f2557; }
        .btn-back svg { width: 13px; height: 13px; }
        .breadcrumb-label { font-size: 13.5px; font-weight: 700; color: #1a1a2e; display: flex; align-items: center; gap: 8px; }

        .search-results-label { font-size: 12px; color: #888; margin-bottom: 14px; }
        .search-results-label strong { color: #1a1a2e; }

        .lib-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 16px; }
        .lib-folder { font-size: 11px; color: #0f2557; background: #f0f4ff; border-radius: 4px; padding: 3px 7px; margin-bottom: 8px; align-self: flex-start; }
        .lib-card { background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; padding: 16px; display: flex; flex-direction: column; transition: box-shadow 0.15s, transform 0.15s; }
        .lib-card:hover { box-shadow: 0 6px 18px rgba(15,37,87,0.1); transform: translateY(-2px); }
        .lib-card-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 10px; }
        .lib-icon { font-size: 26px; line-height: 1; }
        .source-badge { font-size: 9.5px; font-weight: 700; padding: 3px 9px; border-radius: 10px; white-space: nowrap; }
        .source-template { background: #dbeafe; color: #1e40af; }
        .source-material { background: #d1fae5; color: #065f46; }
        .source-shared { background: #ede9fe; color: #6d28d9; }
        .lib-title { font-size: 13.5px; font-weight: 700; color: #1a1a2e; margin-bottom: 4px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
        .lib-desc { font-size: 11px; color: #888; line-height: 1.4; margin-bottom: 10px; flex: 1; }
        .lib-meta { font-size: 10.5px; color: #aaa; margin-bottom: 12px; display: flex; align-items: center; gap: 5px; flex-wrap: wrap; }
        .lib-meta .dot { color: #ddd; }
        .lib-actions { display: flex; gap: 6px; }
        .btn-view { flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 4px; background: #0f2557; color: #fff; border: none; font-size: 11.5px; font-weight: 600; padding: 7px 12px; border-radius: 5px; text-decoration: none; }
        .btn-view:hover { background: #1a3a7a; }
        .btn-del { background: #fff; border: 1px solid #e0e0e0; color: #999; cursor: pointer; padding: 7px 10px; border-radius: 5px; }
        .btn-del:hover { color: #ef4444; border-color: #fca5a5; background: #fee2e2; }
        .btn-view svg, .btn-del svg { width: 12px; height: 12px; }

        .empty-state { grid-column: 1 / -1; text-align: center; padding: 60px 20px; color: #bbb; }
        .empty-state svg { width: 40px; height: 40px; color: #ddd; margin-bottom: 10px; }
        .empty-state p { font-size: 13px; }

        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 999; align-items: center; justify-content: center; }
        .modal-overlay.open { display: flex; }
        .modal { background: #fff; border-radius: 10px; padding: 24px 26px; width: 460px; max-width: 95vw; box-shadow: 0 8px 32px rgba(0,0,0,0.25); }
        .modal-title { font-size: 15px; font-weight: 700; color: #1a1a2e; margin-bottom: 6px; }
        .modal-sub { font-size: 11.5px; color: #888; margin-bottom: 16px; }
        .modal-field { margin-bottom: 13px; }
        .modal-field label { display: block; font-size: 11.5px; font-weight: 700; color: #333; margin-bottom: 4px; }
        .modal-field input[type=text], .modal-field input[type=file], .modal-field textarea, .modal-field select { width: 100%; padding: 8px 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 12.5px; color: #333; outline: none; font-family: Arial, sans-serif; }
        .modal-field input:focus, .modal-field textarea:focus, .modal-field select:focus { border-color: #0f2557; }
        .modal-field select { appearance: none; -webkit-appearance: none; background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%23666' d='M5 7L0 2h10z'/%3E%3C/svg%3E") no-repeat right 10px center; cursor: pointer; }
        .modal-field textarea { resize: vertical; min-height: 60px; }
        .modal-hint { font-size: 10.5px; color: #999; margin-top: 4px; }
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
            <li class="{{ request()->is('faculty/dashboard') ? 'active' : '' }}"><a href="{{ url('/faculty/dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>Dashboard</a></li>
            @endif
            @if($navPermissions['my-template'] ?? true)
            <li class="{{ request()->is('faculty/my-template*') ? 'active' : '' }}"><a href="{{ url('/faculty/my-template') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>Templates
                @if(($newTemplatesCount ?? 0) > 0)
                    <span class="nav-badge">{{ $newTemplatesCount }}</span>
                @endif
            </a></li>
            @endif
            @if($navPermissions['exam-generator'] ?? true)
            <li class="{{ request()->is('faculty/exam-generator*') ? 'active' : '' }}"><a href="{{ url('/faculty/exam-generator') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>Assessment Generator</a></li>
            @endif
            @if($navPermissions['shared-library'] ?? true)
            <li class="{{ request()->is('faculty/shared-library*') ? 'active' : '' }}"><a href="{{ url('/faculty/shared-library') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>Shared Library</a></li>
            @endif
            @if($navPermissions['course-coordination'] ?? true)
            <li class="{{ request()->is('faculty/course-coordination*') ? 'active' : '' }}"><a href="{{ url('/faculty/course-coordination') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>Course Coordination</a></li>
            @endif
            @if($navPermissions['analytics'] ?? true)
            <li class="{{ request()->is('faculty/analytics*') ? 'active' : '' }}"><a href="{{ url('/faculty/analytics') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>Analytics</a></li>
            @endif
            @if($navPermissions['calendar'] ?? true)
            <li class="{{ request()->is('faculty/calendar*') ? 'active' : '' }}"><a href="{{ url('/faculty/calendar') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Calendar of Activities</a></li>
            @endif
            @if($navPermissions['announcements'] ?? true)
            <li class="{{ request()->is('faculty/announcements*') ? 'active' : '' }}"><a href="{{ url('/faculty/announcements') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>Announcements
                @if(($unreadAnnouncementsCount ?? 0) > 0)
                    <span class="nav-badge">{{ $unreadAnnouncementsCount }}</span>
                @endif
            </a></li>
            @endif
            @if($navPermissions['submissions'] ?? true)
            <li class="{{ request()->is('faculty/submissions*') ? 'active' : '' }}"><a href="{{ url('/faculty/submissions') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>Submissions and Deadline
                @if(($urgentSubmissionsCount ?? 0) > 0)
                    <span class="nav-badge">{{ $urgentSubmissionsCount }}</span>
                @endif
            </a></li>
            @endif
            @if($navPermissions['cms'] ?? true)
            <li class="{{ request()->is('faculty/cms*') ? 'active' : '' }}"><a href="{{ url('/faculty/cms') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>CMS</a></li>
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
            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert-error">{{ $errors->first() }}</div>
            @endif

            <div class="page-header">
                <div>
                    <div class="page-title">Shared Library</div>
                    <div class="page-sub">Resources for your program: <strong>{{ $myProgram }}</strong></div>
                </div>
                <button class="btn-share" onclick="openShareModal()">
                    <svg style="width:14px;height:14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    Share Resource
                </button>
            </div>

            {{-- Search: bypasses folders and searches every file at once --}}
            <div class="toolbar">
                <div class="search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" id="lib-search" placeholder="Search all files by title..." oninput="onSearchInput(this.value)">
                </div>
            </div>

            {{-- FOLDER HOME: library "shelves" — Templates by type, Course Materials by subject, Faculty Shared by category --}}
            <div id="folder-home">
                @if($items->isEmpty())
                    <div class="empty-state">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
                        <p>Nothing in the library yet for <strong>{{ $myProgram }}</strong>.<br>Share a resource to get started!</p>
                    </div>
                @else
                    @if($recentItems->isNotEmpty())
                        <div class="folder-section">
                            <div class="folder-section-title">
                                Recently Added
                                <span class="folder-section-count">last {{ $recentDays }} days</span>
                            </div>
                            <div class="lib-grid">
                                @foreach($recentItems as $item)
                                    @include('faculty.partials.library-card', ['item' => $item, 'showFolder' => true])
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @foreach(['templates' => 'Official Templates', 'materials' => 'Course Materials', 'shared' => 'Faculty Shared Resources'] as $group => $sectionLabel)
                        @php $groupFolders = $folders->where('group', $group); @endphp
                        @if($groupFolders->isNotEmpty())
                            <div class="folder-section">
                                <div class="folder-section-title">
                                    {{ $sectionLabel }}
                                    <span class="folder-section-count">{{ $groupFolders->sum('count') }} file{{ $groupFolders->sum('count') == 1 ? '' : 's' }}</span>
                                </div>
                                <div class="folder-grid">
                                    @foreach($groupFolders as $folder)
                                        <button type="button" class="folder-card" onclick="openFolder('{{ $folder['key'] }}', @js($folder['label']))">
                                            <span class="folder-card-icon">{{ $folder['icon'] }}</span>
                                            <div>
                                                <div class="folder-card-label">{{ $folder['label'] }}</div>
                                                <div class="folder-card-count">{{ $folder['count'] }} file{{ $folder['count'] == 1 ? '' : 's' }}</div>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>

            {{-- FILE VIEW: shown after opening a folder, or while searching --}}
            <div id="file-view" style="display:none;">
                <div class="breadcrumb">
                    <button type="button" class="btn-back" onclick="backToFolders()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                        Back to Library
                    </button>
                    <span class="breadcrumb-label" id="file-view-title"></span>
                </div>

                <div class="lib-grid" id="lib-grid">
                    @foreach($items as $item)
                        @include('faculty.partials.library-card', ['item' => $item])
                    @endforeach
                </div>
                <div class="empty-state" id="no-search-results" style="display:none;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <p>No files match.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Share Modal --}}
    <div class="modal-overlay" id="share-overlay">
        <div class="modal">
            <form method="POST" action="{{ url('/faculty/shared-library') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-title">Share a Resource</div>
                <div class="modal-sub">This will be visible to all faculty in the <strong>{{ $myProgram }}</strong> program.</div>

                <div class="modal-field">
                    <label>Category <span style="color:#ef4444">*</span></label>
                    <select name="type" required>
                        @foreach(\App\Models\SharedResource::typeOptions() as $typeKey => $typeLabel)
                            <option value="{{ $typeKey }}">{{ $typeLabel }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="modal-field">
                    <label>Title <span style="color:#ef4444">*</span></label>
                    <input type="text" name="title" placeholder="e.g. Sample Activity Sheets" required>
                </div>

                <div class="modal-field">
                    <label>Description</label>
                    <textarea name="description" placeholder="Brief description of the resource..."></textarea>
                </div>

                <div class="modal-field">
                    <label>File <span style="color:#ef4444">*</span></label>
                    <input type="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" required>
                    <div class="modal-hint">Allowed: PDF, Word, Excel, PowerPoint · Max 20MB</div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeShareModal()">Cancel</button>
                    <button type="submit" class="btn-save">Share</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openShareModal() { document.getElementById('share-overlay').classList.add('open'); }
        function closeShareModal() { document.getElementById('share-overlay').classList.remove('open'); }
        document.getElementById('share-overlay').addEventListener('click', function(e) { if (e.target === this) closeShareModal(); });

        var currentFolder = null; // null = no folder open (home or global search)
        var currentFolderLabel = '';

        function openFolder(key, label) {
            currentFolder = key;
            currentFolderLabel = label;
            document.getElementById('lib-search').value = '';
            document.getElementById('folder-home').style.display = 'none';
            document.getElementById('file-view').style.display = '';
            document.getElementById('file-view-title').textContent = label;
            renderFileView();
        }

        function backToFolders() {
            currentFolder = null;
            currentFolderLabel = '';
            document.getElementById('lib-search').value = '';
            document.getElementById('file-view').style.display = 'none';
            document.getElementById('folder-home').style.display = '';
        }

        function onSearchInput(query) {
            query = query.trim();
            if (query === '') {
                // Nothing typed: go back to wherever we were (a folder, or home)
                if (currentFolder) {
                    document.getElementById('file-view-title').textContent = currentFolderLabel;
                    renderFileView();
                } else {
                    document.getElementById('file-view').style.display = 'none';
                    document.getElementById('folder-home').style.display = '';
                }
                return;
            }

            // Searching always looks across the whole library, not just the
            // open folder — the fastest way to find one file without having
            // to guess which folder it's filed under.
            document.getElementById('folder-home').style.display = 'none';
            document.getElementById('file-view').style.display = '';
            document.getElementById('file-view-title').textContent = 'Search results for "' + query + '"';
            renderFileView(query);
        }

        function renderFileView(searchQuery) {
            var cards = document.querySelectorAll('#lib-grid .lib-card');
            var q = (searchQuery || '').toLowerCase();
            var visibleCount = 0;

            cards.forEach(function(card) {
                var matchesFolder = searchQuery ? true : (!currentFolder || card.dataset.folder === currentFolder);
                var matchesSearch = !q || card.dataset.title.includes(q);
                var visible = matchesFolder && matchesSearch;
                card.style.display = visible ? '' : 'none';
                if (visible) visibleCount++;
            });

            var noResults = document.getElementById('no-search-results');
            if (noResults) {
                noResults.style.display = (visibleCount === 0) ? 'flex' : 'none';
                noResults.style.flexDirection = 'column';
                noResults.style.alignItems = 'center';
            }
        }
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