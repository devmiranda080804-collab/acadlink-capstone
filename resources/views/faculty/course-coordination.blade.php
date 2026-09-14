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
        .file-actions { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
        .btn-del { background: none; border: none; color: #ccc; cursor: pointer; padding: 5px 8px; border-radius: 4px; transition: color 0.15s, background 0.15s; }
        .btn-del:hover { color: #ef4444; background: #fee2e2; }
        .btn-del svg { width: 15px; height: 15px; }
        .folder-empty { text-align: center; padding: 36px; color: #bbb; font-size: 12.5px; }

        .outcome-code { display: inline-block; font-size: 10.5px; font-weight: 700; padding: 2px 9px; border-radius: 10px; background: #eef2ff; color: #0f2557; margin-right: 8px; flex-shrink: 0; }
        .co-card { padding: 13px 18px; border-bottom: 1px solid #f5f5f5; }
        .co-card:last-child { border-bottom: none; }
        .co-activities { font-size: 11px; color: #777; margin-top: 8px; line-height: 1.5; background: #fafbff; border-radius: 5px; padding: 7px 10px; }
        .co-po-list { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; margin-top: 10px; }
        .co-po-label { font-size: 10.5px; color: #999; font-weight: 600; margin-right: 2px; }
        .po-chip { font-size: 10.5px; font-weight: 700; padding: 3px 10px; border-radius: 10px; border: 1px solid #ddd; background: #fff; color: #999; }
        .po-chip.mapped { background: #0f2557; color: #fff; border-color: #0f2557; cursor: default; }

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
        .modal-hint { font-size: 10.5px; color: #999; margin-top: -6px; margin-bottom: 14px; line-height: 1.4; }
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
            <li class="{{ request()->is('faculty/my-template*') ? 'active' : '' }}"><a href="{{ url('/faculty/my-template') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>Templates</a></li>
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
            <li class="{{ request()->is('faculty/announcements*') ? 'active' : '' }}"><a href="{{ url('/faculty/announcements') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>Announcements
                @if(($unreadAnnouncementsCount ?? 0) > 0)
                    <span class="nav-badge">{{ $unreadAnnouncementsCount }}</span>
                @endif
            </a></li>
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
                <label>Select Program</label>
                <div class="course-select-wrap">
                    <form method="GET" id="course-form">
                        <select name="course_id" onchange="document.getElementById('course-form').submit()">
                            <option value="" disabled {{ !$selectedCourse ? 'selected' : '' }}>— Select a program —</option>
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
                <span class="sub-tab" onclick="switchSubTab('outcomes', this)">Outcomes (OBE)</span>
                <span class="sub-tab" onclick="switchSubTab('collaboration', this)">Collaboration</span>
            </div>

            {{-- ═══ MASTER FOLDER ═══ --}}
            <div class="tab-content active" id="tab-master-folder">
                @if(!$selectedCourse)
                    <div class="folder-panel"><div class="folder-empty">📁 Please select a course above to view its master folder.</div></div>
                @else
                    @foreach(\App\Models\CourseMaterial::TYPES as $typeKey => $typeLabel)
                        <div class="folder-panel" style="margin-bottom: 14px;">
                            <div class="folder-panel-header">
                                <span class="folder-icon">📁</span>
                                <div>
                                    <div class="folder-title">{{ $typeLabel }}</div>
                                    <div class="folder-sub">{{ $selectedCourse->code }} — read only, same for every section/instructor</div>
                                </div>
                            </div>
                            @forelse($materials->get($typeKey, collect()) as $material)
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
                                <div class="folder-empty">Nothing uploaded yet.</div>
                            @endforelse
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- ═══ OUTCOMES (OBE) ═══ --}}
            <div class="tab-content" id="tab-outcomes">
                @if(!$selectedCourse)
                    <div class="folder-panel"><div class="folder-empty">🎯 Please select a course above to view its outcomes.</div></div>
                @else
                    <div class="folder-panel">
                        <div class="folder-panel-header">
                            <span class="folder-icon">🎯</span>
                            <div>
                                <div class="folder-title">{{ $selectedCourse->code }} – Course Outcomes</div>
                                <div class="folder-sub">Keep this course's core content consistent across sections/instructors</div>
                            </div>
                        </div>
                        @forelse($courseOutcomes as $co)
                            <div class="co-card">
                                <div>
                                    <span class="outcome-code">{{ $co->code }}</span>
                                    <span class="file-name" style="font-weight:400;">{{ $co->description }}</span>
                                </div>
                                @if($co->sample_activities)
                                    <div class="co-activities"><strong>Sample activities:</strong> {{ $co->sample_activities }}</div>
                                @endif
                                @if($co->programOutcomes->isNotEmpty())
                                    <div class="co-po-list">
                                        <span class="co-po-label">Mapped POs:</span>
                                        @foreach($co->programOutcomes as $po)
                                            <span class="po-chip mapped" title="{{ $po->description }}">{{ $po->code }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="folder-empty">No Course Outcomes have been defined yet for this course.</div>
                        @endforelse
                    </div>
                @endif
            </div>

            {{-- ═══ COLLABORATION ═══ --}}
            <div class="tab-content" id="tab-collaboration">
                @if(!$selectedCourse)
                    <div class="folder-panel"><div class="folder-empty">🤝 Please select a course above to start collaborating.</div></div>
                @elseif(!auth()->user()->google_email)
                    <div class="folder-panel"><div class="folder-empty">🤝 Ask your Program Head, Secretary, or Admin to add your Google email to your account before you can use shared documents.</div></div>
                @else
                    <div class="folder-panel">
                        <div class="folder-panel-header">
                            <span class="folder-icon">🤝</span>
                            <div>
                                <div class="folder-title">{{ $selectedCourse->code }} – Shared Documents</div>
                                <div class="folder-sub">Live-edited together in Google Docs — same content, every instructor of this course</div>
                            </div>
                            <button class="btn-new-doc" style="margin-left:auto;" onclick="openNewDocModal()">+ New Document</button>
                        </div>
                        <div id="docs-list"><div class="docs-empty">Loading...</div></div>
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
            <div class="modal-hint">This creates a real Google Doc and shares it with every instructor who has taught this course.</div>
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
        const MY_ID = {{ auth()->id() }};

        function switchSubTab(tabName, el) {
            document.querySelectorAll('.sub-tab').forEach(t => t.classList.remove('active'));
            el.classList.add('active');
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            document.getElementById('tab-' + tabName).classList.add('active');
            if (tabName === 'collaboration' && COURSE_ID) loadDocuments();
        }

        async function loadDocuments() {
            if (!COURSE_ID) return;
            const list = document.getElementById('docs-list');
            if (!list) return; // Google email not set — panel isn't rendered
            const res = await fetch(`${BASE}/collab/courses/${COURSE_ID}/documents`, { headers: { 'Accept': 'application/json' } });
            const docs = await res.json();
            if (docs.length === 0) { list.innerHTML = '<div class="docs-empty">No shared documents yet.<br>Create a new one.</div>'; return; }
            list.innerHTML = '';
            docs.forEach(doc => {
                const item = document.createElement('div');
                item.className = 'file-row';
                const creator = doc.creator ? doc.creator.name : 'Unknown';
                const deleteBtn = doc.created_by === MY_ID
                    ? `<button type="button" class="btn-del" title="Delete" onclick="deleteDocument(${doc.id}, '${escapeHtml(doc.title).replace(/'/g, "\\'")}')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                       </button>`
                    : '';
                item.innerHTML = `
                    <div class="file-left">
                        <span class="file-icon">🤝</span>
                        <div>
                            <div class="file-name">${escapeHtml(doc.title)}</div>
                            <div class="file-meta">Created by ${escapeHtml(creator)}</div>
                        </div>
                    </div>
                    <div class="file-actions">
                        <a class="btn-view" href="${doc.google_edit_url}" target="_blank" rel="noopener">Open in Google Docs ↗</a>
                        ${deleteBtn}
                    </div>
                `;
                list.appendChild(item);
            });
        }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str == null ? '' : str;
            return div.innerHTML;
        }

        async function deleteDocument(docId, title) {
            if (!confirm(`Delete "${title}"? This removes it from Google Drive too — other instructors will lose access.`)) return;
            const res = await fetch(`${BASE}/collab/documents/${docId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
            });
            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                alert(err.message || 'Could not delete the document.');
                return;
            }
            loadDocuments();
        }

        function openNewDocModal() {
            document.getElementById('new-doc-title').value = '';
            document.getElementById('new-doc-overlay').classList.add('open');
        }
        function closeNewDocModal() { document.getElementById('new-doc-overlay').classList.remove('open'); }

        async function createDocument() {
            const title = document.getElementById('new-doc-title').value.trim();
            if (!title) { alert('Please enter a title.'); return; }
            const res = await fetch(`${BASE}/collab/courses/${COURSE_ID}/documents`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: JSON.stringify({ title: title })
            });
            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                alert(err.message || 'Could not create the document.');
                return;
            }
            const doc = await res.json();
            closeNewDocModal();
            await loadDocuments();
            window.open(`https://docs.google.com/document/d/${doc.google_doc_id}/edit`, '_blank');
        }
    </script>

</body>
</html>