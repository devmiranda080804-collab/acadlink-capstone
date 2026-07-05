<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Course Coordination – CBMA System</title>
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
        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 3px; }
        .page-sub { font-size: 11.5px; color: #888; margin-bottom: 18px; }

        .course-select-box { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 16px 18px; }
        .course-select-box label { display: block; font-size: 11.5px; font-weight: 600; color: #444; margin-bottom: 8px; }
        .course-select-wrap { position: relative; width: 100%; max-width: 340px; }
        .course-select-wrap select { width: 100%; height: 34px; padding: 0 32px 0 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 12.5px; color: #333; background: #fff; outline: none; appearance: none; -webkit-appearance: none; cursor: pointer; }
        .course-select-wrap select:focus { border-color: #0f2557; }
        .course-select-wrap::after { content: ''; position: absolute; right: 10px; top: 50%; transform: translateY(-50%); width: 0; height: 0; border-left: 5px solid transparent; border-right: 5px solid transparent; border-top: 5px solid #666; pointer-events: none; }

        .sub-tabs { display: flex; border-bottom: 2px solid #e0e0e0; margin-bottom: 18px; margin-top: 16px; }
        .sub-tab { padding: 7px 18px; font-size: 12.5px; color: #666; cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -2px; user-select: none; }
        .sub-tab:hover { color: #0f2557; }
        .sub-tab.active { color: #0f2557; font-weight: 700; border-bottom: 2px solid #0f2557; }

        .tab-content { display: none; }
        .tab-content.active { display: block; }

        /* Master Folder */
        .folder-panel { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; }
        .folder-panel-header { display: flex; align-items: center; gap: 8px; padding: 13px 18px; border-bottom: 1px solid #f0f0f0; background: #fafafa; }
        .folder-panel-header .folder-icon { font-size: 16px; }
        .folder-panel-header .folder-title { font-size: 13px; font-weight: 700; color: #1a1a2e; }
        .folder-panel-header .folder-sub { font-size: 11px; color: #999; margin-top: 1px; }
        .file-row { display: flex; align-items: center; justify-content: space-between; padding: 11px 18px; border-bottom: 1px solid #f5f5f5; }
        .file-row:last-child { border-bottom: none; }
        .file-row:hover { background: #f9fbff; }
        .file-left { display: flex; align-items: flex-start; gap: 10px; }
        .file-icon { font-size: 18px; margin-top: 1px; flex-shrink: 0; }
        .file-name { font-size: 12.5px; font-weight: 600; color: #1a1a2e; margin-bottom: 2px; }
        .file-meta { font-size: 10.5px; color: #aaa; }
        .btn-view { background: #fff; border: 1px solid #d0d0d0; color: #333; font-size: 11.5px; font-weight: 600; padding: 5px 16px; border-radius: 4px; cursor: pointer; text-decoration: none; }
        .btn-view:hover { background: #f5f5f5; }
        .folder-empty { text-align: center; padding: 36px; color: #bbb; font-size: 12.5px; }

        /* Collaboration */
        .collab-layout { display: grid; grid-template-columns: 240px 1fr 220px; gap: 16px; align-items: start; }

        .docs-panel { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; }
        .docs-header { display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; border-bottom: 1px solid #f0f0f0; background: #fafafa; }
        .docs-header .title { font-size: 12.5px; font-weight: 700; color: #1a1a2e; }
        .btn-new-doc { background: #0f2557; color: #fff; border: none; border-radius: 5px; font-size: 11px; font-weight: 600; padding: 5px 10px; cursor: pointer; }
        .btn-new-doc:hover { background: #1a3a7a; }
        .doc-list-item { padding: 11px 14px; border-bottom: 1px solid #f5f5f5; cursor: pointer; transition: background 0.12s; }
        .doc-list-item:last-child { border-bottom: none; }
        .doc-list-item:hover { background: #f0f4ff; }
        .doc-list-item.active { background: #eef2ff; border-left: 3px solid #0f2557; }
        .doc-list-item .doc-item-title { font-size: 12px; font-weight: 600; color: #1a1a2e; margin-bottom: 2px; }
        .doc-list-item .doc-item-meta { font-size: 10px; color: #aaa; }
        .docs-empty { text-align: center; padding: 30px 14px; color: #bbb; font-size: 11.5px; }

        .editor-panel { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; display: flex; flex-direction: column; min-height: 420px; }
        .editor-toolbar { display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; border-bottom: 1px solid #f0f0f0; background: #fafafa; gap: 8px; flex-wrap: wrap; }
        .editor-title { font-size: 13px; font-weight: 700; color: #1a1a2e; }
        .btn-toolbar { display: flex; align-items: center; gap: 5px; background: #fff; border: 1px solid #d0d0d0; color: #444; font-size: 11px; font-weight: 600; padding: 5px 12px; border-radius: 5px; cursor: pointer; transition: background 0.15s; }
        .btn-toolbar:hover { background: #f0f4ff; border-color: #0f2557; color: #0f2557; }
        .save-status { font-size: 10.5px; color: #999; display: flex; align-items: center; gap: 5px; }
        .save-status.saving { color: #d97706; }
        .save-status.saved { color: #16a34a; }
        .save-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
        .editor-textarea { flex: 1; border: none; outline: none; resize: none; padding: 18px; font-size: 13px; line-height: 1.7; color: #333; font-family: 'Consolas', 'Courier New', monospace; width: 100%; }
        .editor-empty { flex: 1; display: flex; align-items: center; justify-content: center; color: #bbb; font-size: 12.5px; text-align: center; padding: 40px; }

        .side-panel { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; }
        .side-tabs { display: flex; border-bottom: 1px solid #e0e0e0; }
        .side-tab { flex: 1; text-align: center; padding: 9px 4px; font-size: 11px; color: #666; cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -1px; user-select: none; }
        .side-tab:hover { color: #0f2557; }
        .side-tab.active { color: #0f2557; font-weight: 700; border-bottom: 2px solid #0f2557; }
        .side-tab-content { display: none; padding: 14px; }
        .side-tab-content.active { display: block; }

        .presence-item { display: flex; align-items: center; gap: 8px; padding: 6px 0; font-size: 12px; color: #333; }
        .presence-dot { width: 8px; height: 8px; border-radius: 50%; background: #16a34a; flex-shrink: 0; }
        .presence-empty { color: #bbb; font-size: 11.5px; text-align: center; padding: 12px 0; }

        .version-item { border: 1px solid #e8e8e8; border-radius: 6px; padding: 9px 11px; margin-bottom: 8px; }
        .version-item:last-child { margin-bottom: 0; }
        .version-editor { font-size: 11.5px; font-weight: 700; color: #1a1a2e; }
        .version-time { font-size: 10px; color: #aaa; margin: 2px 0 4px; }
        .version-preview { font-size: 10.5px; color: #777; line-height: 1.4; margin-bottom: 6px; }
        .btn-restore { background: #fff; border: 1px solid #0f2557; color: #0f2557; font-size: 10px; font-weight: 600; padding: 3px 10px; border-radius: 4px; cursor: pointer; }
        .btn-restore:hover { background: #0f2557; color: #fff; }
        .side-empty { text-align: center; padding: 20px 10px; color: #bbb; font-size: 11.5px; }

        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 999; align-items: center; justify-content: center; }
        .modal-overlay.open { display: flex; }
        .modal { background: #fff; border-radius: 10px; padding: 24px 26px; width: 420px; max-width: 95vw; box-shadow: 0 8px 32px rgba(0,0,0,0.25); }
        .modal-title { font-size: 15px; font-weight: 700; color: #1a1a2e; margin-bottom: 16px; }
        .modal-field label { display: block; font-size: 11.5px; font-weight: 700; color: #333; margin-bottom: 4px; }
        .modal-field input { width: 100%; padding: 8px 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 12.5px; outline: none; }
        .modal-field input:focus { border-color: #0f2557; }
        .modal-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 18px; }
        .btn-cancel { background: #fff; border: 1px solid #ccc; color: #444; font-size: 12.5px; font-weight: 600; padding: 8px 18px; border-radius: 5px; cursor: pointer; }
        .btn-save { background: #0f2557; color: #fff; border: none; font-size: 12.5px; font-weight: 600; padding: 8px 20px; border-radius: 5px; cursor: pointer; }

        .toast { position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%); background: #0f2557; color: #fff; font-size: 12.5px; padding: 10px 20px; border-radius: 6px; box-shadow: 0 4px 16px rgba(0,0,0,0.25); z-index: 2000; opacity: 0; transition: opacity 0.2s; pointer-events: none; }
        .toast.show { opacity: 1; }

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
            <li class="{{ request()->is('faculty/my-template*') ? 'active' : '' }}"><a href="{{ url('/faculty/my-template') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>My Template</a></li>
            @endif
            @if($navPermissions['exam-generator'] ?? true)
            <li class="{{ request()->is('faculty/exam-generator*') ? 'active' : '' }}"><a href="{{ url('/faculty/exam-generator') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>Exam Generator</a></li>
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
            <li class="{{ request()->is('faculty/announcements*') ? 'active' : '' }}"><a href="{{ url('/faculty/announcements') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>Announcements</a></li>
            @endif
            @if($navPermissions['submissions'] ?? true)
            <li class="{{ request()->is('faculty/submissions*') ? 'active' : '' }}"><a href="{{ url('/faculty/submissions') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>Submissions and Deadline</a></li>
            @endif
            @if($navPermissions['cms'] ?? true)
            <li class="{{ request()->is('faculty/cms*') ? 'active' : '' }}"><a href="{{ url('/faculty/cms') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>CMS</a></li>
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
            <div class="page-title">Course Coordination</div>
            <div class="page-sub">Access master course folder and instructor collaboration</div>

            <div class="course-select-box">
                <label>Select Course</label>
                <div class="course-select-wrap">
                    <form method="GET" id="course-form">
                        <select name="course_id" onchange="document.getElementById('course-form').submit()">
                            <option value="" disabled {{ !$selectedCourse ? 'selected' : '' }}>— Select a course —</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ $selectedCourse && $selectedCourse->id == $course->id ? 'selected' : '' }}>
                                    {{ $course->code }} – {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            <div class="sub-tabs">
                <span class="sub-tab active" onclick="switchSubTab('master-folder', this)">Master Folder</span>
                <span class="sub-tab" onclick="switchSubTab('collaboration', this)">Collaboration</span>
            </div>

            {{-- ═══ MASTER FOLDER ═══ --}}
            <div class="tab-content active" id="tab-master-folder">
                @if(!$selectedCourse)
                    <div class="folder-panel"><div class="folder-empty">📁 Please select a course above to view its master folder.</div></div>
                @else
                    <div class="folder-panel">
                        <div class="folder-panel-header">
                            <span class="folder-icon">📁</span>
                            <div>
                                <div class="folder-title">{{ $selectedCourse->code }} – Official Course Materials</div>
                                <div class="folder-sub">Read only access to master course folder</div>
                            </div>
                        </div>
                        @forelse($materials as $material)
                            <div class="file-row">
                                <div class="file-left">
                                    <span class="file-icon">
                                        @switch($material->file_type)
                                            @case('pdf') 📄 @break
                                            @case('xls') @case('xlsx') 📊 @break
                                            @case('doc') @case('docx') 📝 @break
                                            @case('zip') 📚 @break
                                            @default 📎
                                        @endswitch
                                    </span>
                                    <div>
                                        <div class="file-name">{{ $material->title }}</div>
                                        <div class="file-meta">{{ strtoupper($material->file_type) }} • {{ $material->version }} • {{ $material->readable_size }} • Updated {{ $material->created_at->format('M d, Y') }}</div>
                                    </div>
                                </div>
                                <a class="btn-view" href="{{ Storage::url($material->file_path) }}" target="_blank">View</a>
                            </div>
                        @empty
                            <div class="folder-empty">Wala pang materials na na-upload para sa course na ito.</div>
                        @endforelse
                    </div>
                @endif
            </div>

            {{-- ═══ COLLABORATION ═══ --}}
            <div class="tab-content" id="tab-collaboration">
                @if(!$selectedCourse)
                    <div class="folder-panel"><div class="folder-empty">🤝 Please select a course above to start collaborating.</div></div>
                @else
                    <div class="collab-layout">
                        <div class="docs-panel">
                            <div class="docs-header">
                                <span class="title">Documents</span>
                                <button class="btn-new-doc" onclick="openNewDocModal()">+ New</button>
                            </div>
                            <div id="docs-list"><div class="docs-empty">Loading...</div></div>
                        </div>

                        <div class="editor-panel">
                            <div class="editor-toolbar">
                                <span class="editor-title" id="editor-title">No document selected</span>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <button class="btn-toolbar" id="btn-share" onclick="shareDocument()" style="display:none;">
                                        <svg style="width:12px;height:12px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                                        Share
                                    </button>
                                    <button class="btn-toolbar" id="btn-export" onclick="exportDocument()" style="display:none;">
                                        <svg style="width:12px;height:12px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                        Export
                                    </button>
                                    <span class="save-status" id="save-status"></span>
                                </div>
                            </div>
                            <div class="editor-empty" id="editor-empty">Pumili o gumawa ng document sa kaliwa para magsimula.</div>
                            <textarea class="editor-textarea" id="editor-textarea" style="display:none;" placeholder="Simulan mag-type dito..." oninput="onContentChange()"></textarea>
                        </div>

                        <div class="side-panel">
                            <div class="side-tabs">
                                <div class="side-tab active" onclick="switchSideTab('presence', this)">Editing</div>
                                <div class="side-tab" onclick="switchSideTab('versions', this)">History</div>
                            </div>
                            <div class="side-tab-content active" id="side-presence"><div class="presence-empty">Walang document na bukas.</div></div>
                            <div class="side-tab-content" id="side-versions"><div class="side-empty">Walang document na bukas.</div></div>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <div class="modal-overlay" id="new-doc-overlay">
        <div class="modal">
            <div class="modal-title">New Document</div>
            <div class="modal-field">
                <label>Document Title</label>
                <input type="text" id="new-doc-title" placeholder="e.g. Lesson Plan – Week 1">
            </div>
            <div class="modal-actions">
                <button class="btn-cancel" onclick="closeNewDocModal()">Cancel</button>
                <button class="btn-save" onclick="createDocument()">Create</button>
            </div>
        </div>
    </div>

    <div class="toast" id="toast"></div>

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        const COURSE_ID = {{ $selectedCourse->id ?? 'null' }};
        const BASE = '{{ url('/faculty') }}';

        let currentDocId = null;
        let currentDocTitle = '';
        let saveTimer = null;
        let pollTimer = null;
        let heartbeatTimer = null;
        let lastSavedContent = '';
        let lastKnownUpdatedAt = null;

        function switchSubTab(tabName, el) {
            document.querySelectorAll('.sub-tab').forEach(t => t.classList.remove('active'));
            el.classList.add('active');
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            document.getElementById('tab-' + tabName).classList.add('active');
            if (tabName === 'collaboration' && COURSE_ID) loadDocuments();
            else stopTimers();
        }

        function switchSideTab(tabName, el) {
            document.querySelectorAll('.side-tab').forEach(t => t.classList.remove('active'));
            el.classList.add('active');
            document.querySelectorAll('.side-tab-content').forEach(c => c.classList.remove('active'));
            document.getElementById('side-' + tabName).classList.add('active');
            if (tabName === 'versions' && currentDocId) loadVersions();
        }

        async function loadDocuments() {
            if (!COURSE_ID) return;
            const res = await fetch(`${BASE}/collab/courses/${COURSE_ID}/documents`, { headers: { 'Accept': 'application/json' } });
            const docs = await res.json();
            const list = document.getElementById('docs-list');
            if (docs.length === 0) { list.innerHTML = '<div class="docs-empty">Wala pang documents.<br>Gumawa ng bago.</div>'; return; }
            list.innerHTML = '';
            docs.forEach(doc => {
                const item = document.createElement('div');
                item.className = 'doc-list-item' + (doc.id === currentDocId ? ' active' : '');
                item.onclick = () => openDocument(doc.id);
                const editor = doc.last_editor ? doc.last_editor.name : 'No edits yet';
                item.innerHTML = `<div class="doc-item-title">${escapeHtml(doc.title)}</div><div class="doc-item-meta">Last edit: ${escapeHtml(editor)}</div>`;
                list.appendChild(item);
            });
        }

        async function openDocument(docId) {
            currentDocId = docId;
            stopTimers();
            const res = await fetch(`${BASE}/collab/documents/${docId}`, { headers: { 'Accept': 'application/json' } });
            const doc = await res.json();

            currentDocTitle = doc.title;
            document.getElementById('editor-title').textContent = doc.title;
            document.getElementById('editor-empty').style.display = 'none';
            document.getElementById('btn-share').style.display = 'flex';
            document.getElementById('btn-export').style.display = 'flex';

            const textarea = document.getElementById('editor-textarea');
            textarea.style.display = 'block';
            textarea.value = doc.content || '';
            lastSavedContent = doc.content || '';
            lastKnownUpdatedAt = doc.updated_at;

            setSaveStatus('saved', 'Saved');
            loadDocuments();
            startPolling();
            startHeartbeat();
        }

        function onContentChange() {
            setSaveStatus('saving', 'Saving...');
            clearTimeout(saveTimer);
            saveTimer = setTimeout(() => saveContent(false), 1500);
        }

        async function saveContent(saveVersion) {
            if (!currentDocId) return;
            const content = document.getElementById('editor-textarea').value;
            const res = await fetch(`${BASE}/collab/documents/${currentDocId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: JSON.stringify({ content: content, save_version: saveVersion })
            });
            const data = await res.json();
            lastSavedContent = content;
            lastKnownUpdatedAt = data.updated_at;
            setSaveStatus('saved', 'Saved');
        }

        function startPolling() {
            pollTimer = setInterval(async () => {
                if (!currentDocId) return;
                const textarea = document.getElementById('editor-textarea');
                if (textarea.value !== lastSavedContent) return;
                const res = await fetch(`${BASE}/collab/documents/${currentDocId}`, { headers: { 'Accept': 'application/json' } });
                const doc = await res.json();
                if (doc.updated_at !== lastKnownUpdatedAt && doc.content !== textarea.value) {
                    textarea.value = doc.content || '';
                    lastSavedContent = doc.content || '';
                    lastKnownUpdatedAt = doc.updated_at;
                    setSaveStatus('saved', 'Updated by ' + (doc.last_edited_by || 'someone'));
                }
            }, 3500);
        }

        function startHeartbeat() {
            sendHeartbeat();
            heartbeatTimer = setInterval(sendHeartbeat, 4000);
        }

        async function sendHeartbeat() {
            if (!currentDocId) return;
            const res = await fetch(`${BASE}/collab/documents/${currentDocId}/heartbeat`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
            });
            const data = await res.json();
            renderPresence(data.active);
        }

        function renderPresence(names) {
            const panel = document.getElementById('side-presence');
            if (!names || names.length === 0) { panel.innerHTML = '<div class="presence-empty">Walang ibang nag-eedit.</div>'; return; }
            panel.innerHTML = names.map(n => `<div class="presence-item"><span class="presence-dot"></span>${escapeHtml(n)}</div>`).join('');
        }

        async function loadVersions() {
            if (!currentDocId) return;
            const res = await fetch(`${BASE}/collab/documents/${currentDocId}/versions`, { headers: { 'Accept': 'application/json' } });
            const versions = await res.json();
            const panel = document.getElementById('side-versions');
            if (versions.length === 0) {
                panel.innerHTML = '<div class="side-empty">Wala pang saved versions.</div>' + saveVersionButton();
                return;
            }
            panel.innerHTML = versions.map(v => `
                <div class="version-item">
                    <div class="version-editor">${escapeHtml(v.editor)}</div>
                    <div class="version-time">${escapeHtml(v.created_at)}</div>
                    <div class="version-preview">${escapeHtml(v.preview || '(empty)')}</div>
                    <button class="btn-restore" onclick="restoreVersion(${v.id})">Restore</button>
                </div>`).join('') + saveVersionButton();
        }

        function saveVersionButton() {
            return '<button class="btn-save" style="width:100%;margin-top:8px;" onclick="saveVersionSnapshot()">Save Current as Version</button>';
        }

        async function saveVersionSnapshot() {
            await saveContent(true);
            loadVersions();
            showToast('Version saved!');
        }

        async function restoreVersion(versionId) {
            if (!confirm('I-restore ang version na ito? Mapapalitan ang current content.')) return;
            const res = await fetch(`${BASE}/collab/documents/${currentDocId}/versions/${versionId}/restore`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
            });
            const data = await res.json();
            document.getElementById('editor-textarea').value = data.content || '';
            lastSavedContent = data.content || '';
            setSaveStatus('saved', 'Restored');
            showToast('Version restored!');
        }

        // ── Export as .docx (backend) ──
        function exportDocument() {
            if (!currentDocId) return;
            window.location.href = `${BASE}/collab/documents/${currentDocId}/export`;
            showToast('Preparing your Word document...');
        }

        // ── Share: copy link ──
        function shareDocument() {
            if (!currentDocId) return;
            const link = `${window.location.origin}${BASE}/course-coordination?course_id=${COURSE_ID}&doc=${currentDocId}`;
            navigator.clipboard.writeText(link).then(() => {
                showToast('Link copied! Ibahagi ito sa kapwa faculty.');
            }).catch(() => {
                showToast('Hindi ma-copy ang link. Subukan ulit.');
            });
        }

        function showToast(msg) {
            const toast = document.getElementById('toast');
            toast.textContent = msg;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 2500);
        }

        function setSaveStatus(cls, text) {
            const el = document.getElementById('save-status');
            el.className = 'save-status ' + cls;
            el.innerHTML = '<span class="save-dot"></span>' + text;
        }

        function stopTimers() { clearInterval(pollTimer); clearInterval(heartbeatTimer); }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str == null ? '' : str;
            return div.innerHTML;
        }

        function openNewDocModal() {
            document.getElementById('new-doc-title').value = '';
            document.getElementById('new-doc-overlay').classList.add('open');
        }
        function closeNewDocModal() { document.getElementById('new-doc-overlay').classList.remove('open'); }

        async function createDocument() {
            const title = document.getElementById('new-doc-title').value.trim();
            if (!title) { alert('Maglagay ng title.'); return; }
            const res = await fetch(`${BASE}/collab/courses/${COURSE_ID}/documents`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: JSON.stringify({ title: title })
            });
            const doc = await res.json();
            closeNewDocModal();
            await loadDocuments();
            openDocument(doc.id);
        }

        // Auto-open shared document mula sa ?doc= sa URL
        document.addEventListener('DOMContentLoaded', function() {
            const params = new URLSearchParams(window.location.search);
            const sharedDocId = params.get('doc');
            if (sharedDocId && COURSE_ID) {
                const collabTab = document.querySelectorAll('.sub-tab')[1];
                if (collabTab) {
                    switchSubTab('collaboration', collabTab);
                    setTimeout(() => openDocument(parseInt(sharedDocId)), 400);
                }
            }
        });

        window.addEventListener('beforeunload', function() {
            if (currentDocId && document.getElementById('editor-textarea').value !== lastSavedContent) {
                navigator.sendBeacon(`${BASE}/collab/documents/${currentDocId}`);
            }
        });
    </script>

</body>
</html>