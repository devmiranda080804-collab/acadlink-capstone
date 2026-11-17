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
        .alert-success { background: #dcfce7; color: #166534; padding: 12px 16px; margin-bottom: 16px; border: 1px solid #bbf7d0; border-radius: 8px; font-size: 13px; }
        .alert-error { background: #fee2e2; color: #b91c1c; padding: 12px 16px; margin-bottom: 16px; border: 1px solid #fca5a5; border-radius: 8px; font-size: 13px; }
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
        .modal-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; }
        .field-note { font-size: 10.5px; color: #888; margin-top: 4px; line-height: 1.4; }
        @media (max-width: 520px) { .modal-row-3 { grid-template-columns: 1fr 1fr; } }
        .modal-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 18px; }
        .btn-cancel { background: #fff; border: 1px solid #ccc; color: #444; font-size: 12.5px; font-weight: 600; padding: 8px 18px; border-radius: 5px; cursor: pointer; }
        .btn-save { background: #0f2557; color: #fff; border: none; font-size: 12.5px; font-weight: 600; padding: 8px 20px; border-radius: 5px; cursor: pointer; }
        .btn-danger { background: #ef4444; color: #fff; border: none; font-size: 12.5px; font-weight: 600; padding: 8px 20px; border-radius: 5px; cursor: pointer; }
        .btn-danger:hover { background: #dc2626; }


        svg { display: inline-block; vertical-align: middle; }
        .topic-key-points { margin-top:4px; font-size:11.5px; color:#555; }
        .topic-key-points summary { cursor:pointer; color:#1d4ed8; }
        .topic-key-points ul { margin:4px 0 0 14px; padding:0; list-style: "- "; }
        .topic-key-points li { margin:2px 0; }
        .cc-eyebrow { font-family: 'Plus Jakarta Sans', Arial, sans-serif; font-size: 12px; font-weight: 800; letter-spacing: 2px; color: #1b3d7a; text-transform: uppercase; }
        .cc-title { font-family: 'Plus Jakarta Sans', Arial, sans-serif; font-size: 28px; font-weight: 800; letter-spacing: -.4px; margin: 4px 0 6px; color: #14213d; }
        .cc-sub { font-size: 14px; color: #475467; margin-bottom: 18px; }
        .course-select-box { border-radius: 16px !important; border: 1px solid #e7eaf1 !important; box-shadow: 0 1px 2px rgba(15, 37, 87, .04), 0 6px 18px rgba(15, 37, 87, .05) !important; }
        .cc-layout { display: grid; grid-template-columns: 260px minmax(0, 1fr); gap: 24px; align-items: start; margin-top: 18px; }
        .cc-ic { width: 19px; height: 19px; flex-shrink: 0; }
        .cc-nav.sub-tabs { display: flex; flex-direction: column; gap: 4px; border: 0 !important; margin: 0 !important; padding: 0 !important; position: sticky; top: 0; background: none !important; }
        .cc-nav .sub-tab { display: flex !important; align-items: center; gap: 12px; padding: 12px 14px !important; border: 1px solid transparent !important; border-radius: 12px !important;
            background: none !important; color: #344054 !important; cursor: pointer; margin: 0 !important; transition: background-color .18s ease, border-color .18s ease, box-shadow .18s ease; }
        .cc-nav .sub-tab:hover { background: #fff !important; border-color: #e7eaf1 !important; }
        .cc-nav .sub-tab.active { background: #fff !important; border-color: #e7eaf1 !important; color: #0f2557 !important; box-shadow: 0 4px 14px rgba(15, 37, 87, .07); }
        .cc-nav .sub-tab > span { flex: 1; min-width: 0; }
        .cc-nav .sub-tab strong { display: block; font-size: 14.5px; font-weight: 700; }
        .cc-nav .sub-tab small { display: block; font-size: 12px; color: #667085; font-weight: 500; margin-top: 2px; }
        .cc-nav .sub-tab em { font-style: normal; min-width: 24px; height: 22px; padding: 0 7px; border-radius: 11px; background: #eef2fa; color: #0f2557; font-size: 11.5px; font-weight: 800;
            display: inline-flex; align-items: center; justify-content: center; }
        .cc-nav .sub-tab:focus-visible { outline: 2px solid #2563eb; outline-offset: 2px; }
        .cc-main .tab-content.active { animation: cc-in .25s ease-out; }
        @keyframes cc-in { from { opacity: 0; } to { opacity: 1; } }
        .cc-status { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; background: #fff; border: 1px solid #e7eaf1; border-radius: 16px; padding: 18px 22px; margin-bottom: 16px;
            box-shadow: 0 1px 2px rgba(15, 37, 87, .04), 0 6px 18px rgba(15, 37, 87, .05); }
        .cc-status-icon { width: 46px; height: 46px; border-radius: 12px; background: #eef2fa; color: #0f2557; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .cc-status-icon .cc-ic { width: 23px; height: 23px; }
        .cc-status-title { font-size: 17px; font-weight: 800; color: #14213d; }
        .cc-status-sub { font-size: 13px; color: #667085; margin-top: 2px; }
        .cc-facts { display: flex; flex-wrap: wrap; gap: 6px 22px; width: 100%; padding-top: 12px; border-top: 1px dashed #e7eaf1; font-size: 12.5px; color: #667085; }
        .cc-facts strong { color: #14213d; }
        @media (max-width: 900px) { .cc-layout { grid-template-columns: 1fr; } .cc-nav.sub-tabs { position: static; flex-direction: row; flex-wrap: wrap; } }
    </style>
</head>
<body>

    @include('partials.sidebar')

    <div class="main">
        <div class="topnav">
            <div class="topnav-right" style="margin-left:auto;">
                @include('partials.topnav-user')
            </div>
        </div>

        <div class="content">
            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert-error">{{ $errors->first() }}</div>
            @endif

            @php
                $ccMaterials = $selectedCourse ? $materials->flatten(1)->count() : 0;
                $ccTopics = $selectedCourse ? $courseTopics->flatten(1) : collect();
            @endphp
            <div class="cc-eyebrow">Teaching</div>
            <div class="cc-title">Course Coordination</div>
            <div class="cc-sub">Access the master course folder, outcomes, topics and shared documents of the courses you teach.</div>

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

            <div class="cc-layout">
            <nav class="cc-nav sub-tabs" aria-label="Course sections">
                <span class="sub-tab active" role="button" tabindex="0" onclick="switchSubTab('master-folder', this)">{!! \App\Support\SidebarMenu::icon('folderCheck', 'cc-ic') !!}<span><strong>Master Folder</strong><small>Official course files</small></span>@if($selectedCourse)<em>{{ $ccMaterials }}</em>@endif</span>
                <span class="sub-tab" role="button" tabindex="0" onclick="switchSubTab('outcomes', this)">{!! \App\Support\SidebarMenu::icon('checkCircle', 'cc-ic') !!}<span><strong>Outcomes (OBE)</strong><small>Course and program outcomes</small></span>@if($selectedCourse)<em>{{ $courseOutcomes->count() }}</em>@endif</span>
                <span class="sub-tab" role="button" tabindex="0" onclick="switchSubTab('topics', this)">{!! \App\Support\SidebarMenu::icon('clock', 'cc-ic') !!}<span><strong>Topics &amp; Hours (OBTL)</strong><small>Topics, key points and modules</small></span>@if($selectedCourse)<em>{{ $ccTopics->count() }}</em>@endif</span>
                <span class="sub-tab" role="button" tabindex="0" onclick="switchSubTab('collaboration', this)">{!! \App\Support\SidebarMenu::icon('users', 'cc-ic') !!}<span><strong>Collaboration</strong><small>Shared documents</small></span></span>
            </nav>

            <div class="cc-main">
                <div class="cc-status">
                    <span class="cc-status-icon">{!! \App\Support\SidebarMenu::icon('cap', 'cc-ic') !!}</span>
                    <div>
                        @if($selectedCourse)
                            <div class="cc-status-title">{{ $selectedCourse->code }} — {{ $selectedCourse->title }}</div>
                            <div class="cc-status-sub">Shared with every instructor of this course.</div>
                        @else
                            <div class="cc-status-title">No course selected</div>
                            <div class="cc-status-sub">Pick a course above to see its folder, outcomes, topics and shared documents.</div>
                        @endif
                    </div>
                    @if($selectedCourse)
                        <div class="cc-facts">
                            <span>Materials: <strong>{{ $ccMaterials }}</strong></span>
                            <span>Course outcomes: <strong>{{ $courseOutcomes->count() }}</strong></span>
                            <span>Topics: <strong>{{ $ccTopics->count() }}</strong></span>
                            <span>Hours: <strong>{{ $ccTopics->sum('hours') }}</strong></span>
                        </div>
                    @endif
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
                                    <div class="file-actions">
                                        <a class="btn-view" href="{{ \App\Support\FileLinks::view('material', $material->id) }}">View</a>
                                        <a class="btn-view" href="{{ \App\Support\FileLinks::download('material', $material->id) }}">Download</a>
                                    </div>
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

            {{-- ═══ TOPICS & HOURS (OBTL) ═══ --}}
            <div class="tab-content" id="tab-topics">
                @if(!$selectedCourse)
                    <div class="folder-panel"><div class="folder-empty">🕒 Please select a course above to manage its Topics & Hours.</div></div>
                @else
                    <div class="folder-panel" style="margin-bottom: 14px;">
                        <div class="folder-panel-header">
                            <span class="folder-icon">📄</span>
                            <div>
                                <div class="folder-title">Official OBTL Document</div>
                                <div class="folder-sub">{{ $selectedCourse->code }} — the actual prepared & signed OBTL file, shared with every instructor of this course</div>
                            </div>
                            <button class="btn-new-doc" style="margin-left:auto;" onclick="openObtlUploadModal()">+ Upload OBTL</button>
                        </div>

                        @forelse($materials->get('obtl', collect()) as $material)
                            <div class="file-row">
                                <div class="file-left">
                                    <span class="file-icon">
                                        @switch($material->file_type)
                                            @case('pdf') 📄 @break
                                            @case('doc') @case('docx') 📝 @break
                                            @default 📎
                                        @endswitch
                                    </span>
                                    <div>
                                        <div class="file-name">{{ $material->title }}</div>
                                        <div class="file-meta">{{ strtoupper($material->file_type) }} • {{ $material->version }} • {{ $material->readable_size }} • Uploaded {{ $material->created_at->format('M d, Y') }} by {{ $material->uploader->name }}</div>
                                    </div>
                                </div>
                                <div class="file-actions">
                                    <a class="btn-view" href="{{ \App\Support\FileLinks::view('material', $material->id) }}">View</a>
                                    <a class="btn-view" href="{{ \App\Support\FileLinks::download('material', $material->id) }}">Download</a>
                                    <form method="POST" action="{{ url('/faculty/course-coordination/materials/' . $material->id) }}" id="remove-material-form-{{ $material->id }}">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button" class="btn-del" title="Delete" onclick="openConfirm('Delete this OBTL document?', 'remove-material-form-{{ $material->id }}')">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="folder-empty">No OBTL document uploaded yet.</div>
                        @endforelse
                    </div>

                    @foreach(['Prelim', 'Midterm', 'Final'] as $period)
                        @php $periodTopics = $courseTopics->get($period, collect()); @endphp
                        <div class="folder-panel" style="margin-bottom: 14px;">
                            <div class="folder-panel-header">
                                <span class="folder-icon">🕒</span>
                                <div>
                                    <div class="folder-title">{{ $period }} — {{ $selectedCourse->code }}</div>
                                    <div class="folder-sub">{{ $periodTopics->sum('hours') }} hrs allocated · shared with every instructor of this course · feeds the Assessment Generator automatically</div>
                                </div>
                                <button class="btn-new-doc" style="margin-left:auto;" onclick="openTopicModal('{{ $period }}')">+ Add Topic</button>
                            </div>

                            @forelse($periodTopics as $topic)
                                <div class="file-row">
                                    <div class="file-left">
                                        <div>
                                            <div class="file-name">{{ $topic->weeks_label ? $topic->weeks_label . ' — ' : '' }}{{ $topic->topic }}</div>
                                            <div class="file-meta">
                                                {{ $topic->hours }} hrs
                                                @if($topic->module_path) · <span style="color:#16a34a;">✓ module uploaded (AI-ready)</span>
                                                @elseif($topic->notes) · <span style="color:#16a34a;">✓ AI-ready</span>
                                                @else · <span style="color:#bbb;">no notes/module yet</span>
                                                @endif
                                            </div>
                                            @if($keyPoints = $topic->keyPointList())
                                                <details class="topic-key-points">
                                                    <summary>{{ count($keyPoints) }} key {{ count($keyPoints) === 1 ? 'point' : 'points' }}</summary>
                                                    <ul>
                                                        @foreach($keyPoints as $point)
                                                            <li>{{ $point }}</li>
                                                        @endforeach
                                                    </ul>
                                                </details>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="file-actions">
                                        @if($topic->module_path)
                                            <a class="btn-view" href="{{ \App\Support\FileLinks::view('topic', $topic->id) }}">View</a>
                                            <a class="btn-view" href="{{ \App\Support\FileLinks::download('topic', $topic->id) }}">Download</a>
                                        @endif
                                        <button type="button" class="btn-view" onclick='openEditTopicModal(@json($topic))'>Edit</button>
                                        <form method="POST" action="{{ url('/faculty/course-coordination/course-topics/' . $topic->id) }}" id="remove-topic-form-{{ $topic->id }}">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <button type="button" class="btn-del" title="Delete" onclick="openConfirm('Remove this topic?', 'remove-topic-form-{{ $topic->id }}')">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="folder-empty">No topics defined yet for {{ $period }}.</div>
                            @endforelse
                        </div>
                    @endforeach
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
            </div>{{-- .cc-main --}}
            </div>{{-- .cc-layout --}}

        </div>
    </div>

    <div class="modal-overlay" id="confirm-overlay">
        <div class="modal" style="width:380px;">
            <div class="modal-title">Please Confirm</div>
            <div class="modal-hint" id="confirm-text">—</div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeConfirm()">Cancel</button>
                <button type="button" class="btn-danger" onclick="runConfirm()">Remove</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="topic-overlay">
        <div class="modal">
            <form method="POST" action="{{ url('/faculty/course-coordination/course-topics') }}" enctype="multipart/form-data" id="topic-form">
                @csrf
                <input type="hidden" name="course_id" value="{{ $selectedCourse->id ?? '' }}">
                <input type="hidden" name="grading_period" id="topic-grading-period" value="">
                <div class="modal-title" id="topic-modal-title">Add Topic</div>
                <div class="modal-hint" id="topic-modal-sub">—</div>
                <div class="modal-field">
                    <label>Topic</label>
                    <input type="text" name="topic" id="topic-field-topic" required>
                </div>
                <div class="modal-row-3">
                    <div class="modal-field" style="grid-column: span 2;">
                        <label>Week</label>
                        <input type="text" name="week" id="topic-field-week" inputmode="numeric" autocomplete="off" placeholder="e.g. 3, or 1-2" required
                            pattern="\s*\d{1,2}\s*(?:(?:-|–|—|to)\s*\d{1,2}\s*)?" title="A week like 3, or a range like 1-2" oninput="computeTopicHours()">
                        <div class="field-note">One week (3), or a range for a topic over several weeks (1-2).</div>
                    </div>
                    <div class="modal-field">
                        <label>Hours per week</label>
                        <input type="number" id="topic-field-hours-per-week" min="1" max="40" step="1" value="3" oninput="computeTopicHours()">
                    </div>
                </div>
                <div class="modal-field">
                    <label>Hours</label>
                    <input type="number" name="hours" id="topic-field-hours" min="1" required>
                    <div class="field-note" id="topic-hours-note">Computed from the week above. You can change it.</div>
                </div>
                <div class="modal-field">
                    <label>Module <span id="topic-field-module-hint" style="font-size:10px;color:#999;">(PDF or Word — the AI reads it to suggest key points and to write exam questions)</span></label>
                    <div id="topic-current-module" style="font-size:11.5px;color:#666;margin-bottom:4px;display:none;"></div>
                    <input type="file" name="module" id="topic-field-module" accept=".pdf,.docx" onchange="suggestKeyPoints()">
                </div>
                <div class="modal-field">
                    <label>Key Points <span style="font-size:10px;color:#999;">(AI suggests these from the module you pick — one point per line, each starting with "-")</span></label>
                    <textarea name="key_points" id="topic-field-key-points" rows="5" placeholder="- Pick a module above and the AI will list its key points here." style="width:100%;padding:8px 10px;border:1px solid #ccc;border-radius:5px;font-size:12.5px;font-family:Arial, sans-serif;resize:vertical;"></textarea>
                    <div class="field-note" id="topic-key-points-status" style="display:none;"></div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeTopicModal()">Cancel</button>
                    <button type="submit" class="btn-save" id="topic-form-submit">Add</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="obtl-upload-overlay">
        <div class="modal">
            <form method="POST" action="{{ url('/faculty/course-coordination/materials') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="course_id" value="{{ $selectedCourse->id ?? '' }}">
                <div class="modal-title">Upload OBTL Document</div>
                @if($officialCourseGuide)
                    <div class="modal-hint" style="margin-top:0;margin-bottom:13px;">
                        📙 Need the official format first? <a href="{{ \App\Support\FileLinks::view('template', $officialCourseGuide->id) }}">View the Course Guide template</a>
                    </div>
                @endif
                <div class="modal-field">
                    <label>Title</label>
                    <input type="text" name="title" required>
                </div>
                <div class="modal-field">
                    <label>Version</label>
                    <input type="text" name="version" placeholder="v1.0">
                </div>
                <div class="modal-field">
                    <label>File</label>
                    <input type="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.zip" required>
                    <div style="font-size:10.5px;color:#999;margin-top:4px;">Allowed: PDF, Word, Excel, ZIP · Max 20MB</div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeObtlUploadModal()">Cancel</button>
                    <button type="submit" class="btn-save">Upload</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="new-doc-overlay">
        <div class="modal">
            <div class="modal-title">New Document</div>
            <div class="modal-field">
                <label>Document Title</label>
                <input type="text" id="new-doc-title">
            </div>
            <div class="modal-hint" style="margin-top:0;">This creates a real Google Doc and shares it with every instructor who has taught this course.</div>
            <div class="modal-actions">
                <button class="btn-cancel" onclick="closeNewDocModal()">Cancel</button>
                <button class="btn-save" onclick="createDocument()">Create</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="doc-detail-overlay">
        <div class="modal" style="width:480px;max-height:80vh;overflow-y:auto;">
            <div class="modal-title" id="doc-detail-title">—</div>
            <div class="modal-hint" style="margin-bottom:16px;">Actual editing happens in Google Docs. This panel is AcadLink's own record of who's been viewing this document and its saved version history.</div>

            <div style="font-size:11.5px;font-weight:700;color:#333;margin-bottom:6px;">Recently active</div>
            <div id="doc-detail-viewers" style="margin-bottom:16px;font-size:12px;color:#666;">Loading...</div>

            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                <div style="font-size:11.5px;font-weight:700;color:#333;">Version history</div>
                <button type="button" class="btn-save" style="padding:5px 12px;font-size:11.5px;" onclick="saveVersionNow()">📸 Save Version Now</button>
            </div>
            <div id="doc-detail-versions" style="font-size:12px;color:#666;">Loading...</div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeDocDetail()">Close</button>
                <a class="btn-save" id="doc-detail-open-link" href="#" target="_blank" rel="noopener" style="text-decoration:none;text-align:center;">Open in Google Docs ↗</a>
            </div>
        </div>
    </div>


    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        const COURSE_ID = {{ $selectedCourse->id ?? 'null' }};
        const BASE = '{{ url('/faculty') }}';
        const MY_ID = {{ auth()->id() }};

        document.querySelectorAll('.cc-nav .sub-tab').forEach(function (tab) {
            tab.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); tab.click(); } });
        });
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
                    <div class="file-left" style="cursor:pointer;" onclick="openDocDetail(${doc.id}, '${escapeHtml(doc.title).replace(/'/g, "\\'")}', '${doc.google_edit_url}')">
                        <span class="file-icon">🤝</span>
                        <div>
                            <div class="file-name">${escapeHtml(doc.title)}</div>
                            <div class="file-meta">Created by ${escapeHtml(creator)} · click for viewers &amp; version history</div>
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

        function deleteDocument(docId, title) {
            openConfirm(`Delete "${title}"? This removes it from Google Drive too — other instructors will lose access.`, null, function() {
                performDeleteDocument(docId);
            });
        }

        async function performDeleteDocument(docId) {
            const res = await fetch(`${BASE}/collab/documents/${docId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
            });
            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                showToast(err.message || 'Could not delete the document.', 'error');
                return;
            }
            loadDocuments();
        }

        function openNewDocModal() {
            document.getElementById('new-doc-title').value = '';
            document.getElementById('new-doc-overlay').classList.add('open');
        }
        function closeNewDocModal() { document.getElementById('new-doc-overlay').classList.remove('open'); }

        let currentDetailDocId = null;

        async function openDocDetail(docId, title, editUrl) {
            currentDetailDocId = docId;
            document.getElementById('doc-detail-title').textContent = title;
            document.getElementById('doc-detail-open-link').href = editUrl;
            document.getElementById('doc-detail-viewers').innerHTML = 'Loading...';
            document.getElementById('doc-detail-versions').innerHTML = 'Loading...';
            document.getElementById('doc-detail-overlay').classList.add('open');
            await refreshDocDetail();
        }

        async function refreshDocDetail() {
            if (!currentDetailDocId) return;
            const res = await fetch(`${BASE}/collab/documents/${currentDetailDocId}`, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) { showToast('Could not load document details.', 'error'); return; }
            const data = await res.json();

            const viewersBox = document.getElementById('doc-detail-viewers');
            if (data.viewers.length === 0) {
                viewersBox.innerHTML = '<span style="color:#bbb;">No one has opened this document yet.</span>';
            } else {
                viewersBox.innerHTML = data.viewers
                    .sort((a, b) => new Date(b.last_opened_at) - new Date(a.last_opened_at))
                    .map(v => `<div style="display:flex;justify-content:space-between;padding:5px 0;">
                        <span>${escapeHtml(v.name)} ${v.active_now ? '<span style="color:#10b981;font-weight:700;">● active</span>' : ''}</span>
                        <span style="color:#999;">${timeAgo(v.last_opened_at)}</span>
                    </div>`).join('');
            }

            const versionsBox = document.getElementById('doc-detail-versions');
            if (data.versions.length === 0) {
                versionsBox.innerHTML = '<span style="color:#bbb;">No saved versions yet.</span>';
            } else {
                versionsBox.innerHTML = data.versions.map(v => `
                    <div style="border-top:1px solid #f0f0f0;padding:8px 0;">
                        <div style="display:flex;justify-content:space-between;font-weight:600;">
                            <span>${escapeHtml(v.editor || 'Unknown')}</span>
                            <span style="color:#999;font-weight:400;">${timeAgo(v.created_at)}</span>
                        </div>
                        <div style="color:#888;margin-top:3px;white-space:pre-wrap;">${escapeHtml(v.preview || '(empty)')}</div>
                    </div>
                `).join('');
            }
        }

        async function saveVersionNow() {
            if (!currentDetailDocId) return;
            const res = await fetch(`${BASE}/collab/documents/${currentDetailDocId}/snapshot`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
            });
            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                showToast(err.message || 'Could not save a version right now.', 'error');
                return;
            }
            const data = await res.json();
            showToast(data.status === 'unchanged' ? 'No changes since the last saved version.' : 'Version saved.', data.status === 'unchanged' ? 'info' : 'success');
            await refreshDocDetail();
        }

        function closeDocDetail() {
            currentDetailDocId = null;
            document.getElementById('doc-detail-overlay').classList.remove('open');
        }
        document.getElementById('doc-detail-overlay').addEventListener('click', function(e) { if (e.target === this) closeDocDetail(); });

        function timeAgo(iso) {
            const diffMs = Date.now() - new Date(iso).getTime();
            const mins = Math.floor(diffMs / 60000);
            if (mins < 1) return 'just now';
            if (mins < 60) return mins + 'm ago';
            const hours = Math.floor(mins / 60);
            if (hours < 24) return hours + 'h ago';
            return Math.floor(hours / 24) + 'd ago';
        }

        // Hours = number of weeks x hours per week. Re-runs whenever the weeks or the
        // hours per week change; the faculty can still type a different total.
        // "3" -> [3, 3]; "1-2" -> [1, 2]; anything else -> null.
        function topicWeekRange() {
            var m = /^\s*(\d+)\s*(?:(?:-|–|—|to)\s*(\d+))?\s*$/i.exec(document.getElementById('topic-field-week').value);
            return m ? [parseInt(m[1], 10), parseInt(m[2] || m[1], 10)] : null;
        }

        function computeTopicHours() {
            var range = topicWeekRange();
            var perWeek = parseInt(document.getElementById('topic-field-hours-per-week').value, 10);
            var note = document.getElementById('topic-hours-note');
            if (!range || !range[0] || !perWeek) {
                note.textContent = 'Computed from the week above. You can change it.';
                return;
            }
            var from = range[0], to = range[1];
            if (to < from) {
                note.textContent = 'In a range of weeks, the second week can\'t be earlier than the first (for example 1-2).';
                return;
            }
            var weeks = to - from + 1;
            document.getElementById('topic-field-hours').value = weeks * perWeek;
            note.textContent = weeks + ' week' + (weeks === 1 ? '' : 's') + ' × ' + perWeek + ' hrs = ' + (weeks * perWeek) + ' hours. You can change it.';
        }

        function resetTopicForm() {
            document.getElementById('topic-field-topic').value = '';
            document.getElementById('topic-field-week').value = '';
            document.getElementById('topic-field-hours-per-week').value = 3;
            document.getElementById('topic-field-hours').value = '';
            document.getElementById('topic-hours-note').textContent = 'Computed from the week above. You can change it.';
            document.getElementById('topic-field-key-points').value = '';
            document.getElementById('topic-field-module').value = '';
            setKeyPointsStatus('', '');
            keyPointsRequest++; keyPointsBusy = false; document.getElementById('topic-field-key-points').readOnly = false;
            document.getElementById('topic-current-module').style.display = 'none';
        }

        // When a module is picked, the AI reads it and fills in the key points.
        // The instructor can edit them before saving.
        var keyPointsRequest = 0, keyPointsBusy = false;

        // Each key point shows on its own line starting with "- ". The dash is only for
        // reading; it is taken off when the topic is saved.
        function dashedPoints(text) {
            return (text || '').split(/\r?\n/).map(function (line) { return line.replace(/^\s*(?:[-*•·]+|\d+[.)])\s*/, '').trim(); })
                .filter(function (line) { return line !== ''; })
                .map(function (line) { return '- ' + line; }).join('\n');
        }
        (function () {
            var box = document.getElementById('topic-field-key-points');
            // Enter starts the next point with "- "; Enter on an empty "- " line ends the list.
            box.addEventListener('keydown', function (e) {
                if (e.key !== 'Enter' || e.shiftKey || box.readOnly) return;
                var start = box.selectionStart, value = box.value;
                var lineStart = value.lastIndexOf('\n', start - 1) + 1;
                var line = value.slice(lineStart, start);
                if (/^-\s*$/.test(line)) {
                    e.preventDefault();
                    box.value = value.slice(0, lineStart) + value.slice(start);
                    box.selectionStart = box.selectionEnd = lineStart;
                    return;
                }
                if (/^-\s/.test(line)) {
                    e.preventDefault();
                    box.value = value.slice(0, start) + '\n- ' + value.slice(box.selectionEnd);
                    box.selectionStart = box.selectionEnd = start + 3;
                }
            });
            // Typing into an empty box starts the first point with "- ".
            box.addEventListener('focus', function () {
                if (box.value.trim() === '' && !box.readOnly) { box.value = '- '; box.selectionStart = box.selectionEnd = 2; }
            });
            box.addEventListener('blur', function () {
                if (/^-\s*$/.test(box.value)) box.value = '';
            });
        })();
        function setKeyPointsStatus(text, tone) {
            var el = document.getElementById('topic-key-points-status');
            el.textContent = text;
            el.style.color = tone === 'error' ? '#c62828' : (tone === 'ok' ? '#16a34a' : '#666');
            el.style.display = text ? 'block' : 'none';
        }
        function suggestKeyPoints() {
            var input = document.getElementById('topic-field-module');
            var file = input.files && input.files[0];
            if (!file) return;
            var request = ++keyPointsRequest;
            var box = document.getElementById('topic-field-key-points');
            var data = new FormData();
            data.append('module', file);
            data.append('topic', document.getElementById('topic-field-topic').value);
            box.readOnly = true;
            keyPointsBusy = true;
            setKeyPointsStatus('AI is reading the module and picking out its key points…', '');
            fetch('{{ url("/faculty/course-coordination/module-key-points") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: data
            }).then(function (r) {
                return r.json().catch(function () { return {}; }).then(function (body) { return { ok: r.ok, body: body }; });
            }).then(function (res) {
                if (request !== keyPointsRequest) return;
                var points = res.body.points || [];
                if (res.ok && points.length) {
                    box.value = dashedPoints(points.join('\n'));
                    setKeyPointsStatus('AI suggested ' + points.length + ' key points from the module. Check them and edit if needed before saving.', 'ok');
                } else {
                    var message = res.body.message || (res.body.errors && Object.values(res.body.errors)[0][0]) || 'No key points could be suggested. You can type them yourself.';
                    setKeyPointsStatus(message, 'error');
                }
            }).catch(function () {
                if (request === keyPointsRequest) setKeyPointsStatus('Could not reach the AI. Check your connection and pick the file again, or type the key points yourself.', 'error');
            }).finally(function () {
                if (request !== keyPointsRequest) return;
                box.readOnly = false;
                keyPointsBusy = false;
            });
        }

        function openTopicModal(period) {
            resetTopicForm();
            document.getElementById('topic-form').action = '{{ url("/faculty/course-coordination/course-topics") }}';
            document.getElementById('topic-grading-period').value = period;
            document.getElementById('topic-modal-title').textContent = 'Add Topic';
            document.getElementById('topic-modal-sub').textContent = 'Grading period: ' + period;
            document.getElementById('topic-form-submit').textContent = 'Add';
            document.getElementById('topic-field-module').required = true;
            document.getElementById('topic-field-module-hint').textContent = '(required — PDF or Word; the AI reads it to suggest key points and to write exam questions)';
            document.getElementById('topic-overlay').classList.add('open');
        }

        function openEditTopicModal(topic) {
            resetTopicForm();
            document.getElementById('topic-form').action = '{{ url("/faculty/course-coordination/course-topics") }}/' + topic.id;
            document.getElementById('topic-grading-period').value = topic.grading_period;
            document.getElementById('topic-modal-title').textContent = 'Edit Topic';
            document.getElementById('topic-modal-sub').textContent = 'Grading period: ' + topic.grading_period;
            document.getElementById('topic-field-topic').value = topic.topic || '';
            // Weeks are stored as "3" or "1-2"; an old free-text value leaves the fields empty.
            var range = /^(\d+)(?:-(\d+))?$/.exec(topic.weeks || '');
            var weekFrom = range ? parseInt(range[1], 10) : '';
            var weekTo = range ? parseInt(range[2] || range[1], 10) : '';
            document.getElementById('topic-field-week').value = range ? (weekTo !== weekFrom ? weekFrom + '-' + weekTo : String(weekFrom)) : '';
            var weekCount = range ? weekTo - weekFrom + 1 : 0;
            document.getElementById('topic-field-hours-per-week').value =
                weekCount && topic.hours % weekCount === 0 ? topic.hours / weekCount : 3;
            document.getElementById('topic-field-hours').value = topic.hours || '';
            document.getElementById('topic-field-key-points').value = dashedPoints(topic.key_points);
            document.getElementById('topic-field-module').required = false;
            document.getElementById('topic-field-module-hint').textContent = '(optional — PDF or Word; upload a new file only if you want to replace the current one)';
            var currentModule = document.getElementById('topic-current-module');
            if (topic.module_file_name) {
                currentModule.textContent = 'Current module: ' + topic.module_file_name + ' (upload a new file to replace it)';
                currentModule.style.display = 'block';
            }
            document.getElementById('topic-form-submit').textContent = 'Save Changes';
            document.getElementById('topic-overlay').classList.add('open');
        }

        document.getElementById('topic-form').addEventListener('submit', function (e) {
            if (keyPointsBusy) { e.preventDefault(); setKeyPointsStatus('Please wait — the AI is still reading the module.', 'error'); }
        });

        function closeTopicModal() { document.getElementById('topic-overlay').classList.remove('open'); }
        document.getElementById('topic-overlay').addEventListener('click', function(e) { if (e.target === this) closeTopicModal(); });

        function openObtlUploadModal() { document.getElementById('obtl-upload-overlay').classList.add('open'); }
        function closeObtlUploadModal() { document.getElementById('obtl-upload-overlay').classList.remove('open'); }
        document.getElementById('obtl-upload-overlay').addEventListener('click', function(e) { if (e.target === this) closeObtlUploadModal(); });

        // Generic delete-confirmation modal, shared by every "Delete/Remove" action on
        // this page — replaces the browser's native confirm() with the app's own modal
        // style. Pass a formId to submit a hidden form, or a callback for AJAX deletes.
        var confirmFormId = null;
        var confirmCallback = null;
        function openConfirm(message, formId, callback) {
            confirmFormId = formId || null;
            confirmCallback = callback || null;
            document.getElementById('confirm-text').textContent = message;
            document.getElementById('confirm-overlay').classList.add('open');
        }
        function closeConfirm() {
            confirmFormId = null;
            confirmCallback = null;
            document.getElementById('confirm-overlay').classList.remove('open');
        }
        function runConfirm() {
            if (confirmFormId) {
                document.getElementById(confirmFormId).submit();
            } else if (confirmCallback) {
                confirmCallback();
            }
            closeConfirm();
        }
        document.getElementById('confirm-overlay').addEventListener('click', function(e) { if (e.target === this) closeConfirm(); });

        async function createDocument() {
            const title = document.getElementById('new-doc-title').value.trim();
            if (!title) { showToast('Please enter a title.', 'error'); return; }
            const res = await fetch(`${BASE}/collab/courses/${COURSE_ID}/documents`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: JSON.stringify({ title: title })
            });
            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                showToast(err.message || 'Could not create the document.', 'error');
                return;
            }
            const doc = await res.json();
            closeNewDocModal();
            await loadDocuments();
            window.open(`https://docs.google.com/document/d/${doc.google_doc_id}/edit`, '_blank');
        }

        // Re-open whichever sub-tab the faculty was on before a form submit redirected
        // back here — without this, every Add/Edit/Delete in Topics & Hours (or the OBTL
        // upload) bounces the page back to Master Folder, losing their place.
        @if(session('active_tab'))
            document.addEventListener('DOMContentLoaded', function() {
                var target = @json(session('active_tab'));
                var tabs = document.querySelectorAll('.sub-tab');
                var order = ['master-folder', 'outcomes', 'topics', 'collaboration'];
                var idx = order.indexOf(target);
                if (idx !== -1 && tabs[idx]) switchSubTab(target, tabs[idx]);
            });
        @endif
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

    @include('partials.system-feedback')
</body>
</html>