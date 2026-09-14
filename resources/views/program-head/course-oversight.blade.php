<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Oversight – CBMA System</title>
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

        .course-select-box { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 16px 18px; margin-bottom: 18px; display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
        .course-select-box label { display: block; font-size: 11.5px; font-weight: 600; color: #444; margin-bottom: 8px; }
        .course-select-wrap { position: relative; width: 100%; max-width: 340px; }
        .course-select-wrap select { width: 100%; height: 36px; padding: 0 32px 0 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 12.5px; color: #333; background: #fff; outline: none; appearance: none; -webkit-appearance: none; cursor: pointer; }
        .course-select-wrap select:focus { border-color: #0f2557; }
        .course-select-wrap::after { content: ''; position: absolute; right: 10px; top: 50%; transform: translateY(-50%); width: 0; height: 0; border-left: 5px solid transparent; border-right: 5px solid transparent; border-top: 5px solid #666; pointer-events: none; }

        .btn-upload { display: flex; align-items: center; gap: 6px; background: #0f2557; color: #fff; border: none; border-radius: 6px; font-size: 12.5px; font-weight: 600; padding: 10px 18px; cursor: pointer; transition: background 0.15s; white-space: nowrap; }
        .btn-upload:hover { background: #1a3a7a; }
        .btn-upload:disabled { opacity: 0.5; cursor: not-allowed; }

        .folder-panel { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; }
        .folder-panel-header { display: flex; align-items: center; gap: 8px; padding: 13px 18px; border-bottom: 1px solid #f0f0f0; background: #fafafa; }
        .folder-panel-header .folder-icon { font-size: 16px; }
        .folder-panel-header .folder-title { font-size: 13px; font-weight: 700; color: #1a1a2e; }
        .folder-panel-header .folder-sub { font-size: 11px; color: #999; margin-top: 1px; }

        .file-row { display: flex; align-items: center; justify-content: space-between; padding: 11px 18px; border-bottom: 1px solid #f5f5f5; transition: background 0.12s; }
        .file-row:last-child { border-bottom: none; }
        .file-row:hover { background: #f9fbff; }
        .file-left { display: flex; align-items: flex-start; gap: 10px; }
        .file-icon { font-size: 18px; margin-top: 1px; flex-shrink: 0; }
        .file-name { font-size: 12.5px; font-weight: 600; color: #1a1a2e; margin-bottom: 2px; }
        .file-meta { font-size: 10.5px; color: #aaa; }
        .file-actions { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
        .btn-view { background: #fff; border: 1px solid #d0d0d0; color: #333; font-size: 11.5px; font-weight: 600; padding: 5px 16px; border-radius: 4px; cursor: pointer; transition: background 0.15s; text-decoration: none; }
        .btn-view:hover { background: #f5f5f5; }
        .btn-del { background: none; border: none; color: #ccc; cursor: pointer; padding: 5px 8px; border-radius: 4px; transition: color 0.15s, background 0.15s; }
        .btn-del:hover { color: #ef4444; background: #fee2e2; }
        .btn-del svg { width: 15px; height: 15px; }
        .folder-empty { text-align: center; padding: 36px; color: #bbb; font-size: 12.5px; }

        .sub-tabs { display: flex; border-bottom: 2px solid #e0e0e0; margin-bottom: 18px; }
        .sub-tab { padding: 7px 18px; font-size: 12.5px; color: #666; cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -2px; user-select: none; }
        .sub-tab:hover { color: #0f2557; }
        .sub-tab.active { color: #0f2557; font-weight: 700; border-bottom: 2px solid #0f2557; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        .outcome-code { display: inline-block; font-size: 10.5px; font-weight: 700; padding: 2px 9px; border-radius: 10px; background: #eef2ff; color: #0f2557; margin-right: 8px; flex-shrink: 0; }
        .co-card { padding: 13px 18px; border-bottom: 1px solid #f5f5f5; }
        .co-card:last-child { border-bottom: none; }
        .co-card-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; }
        .co-activities { font-size: 11px; color: #777; margin-top: 8px; line-height: 1.5; background: #fafbff; border-radius: 5px; padding: 7px 10px; }
        .co-po-list { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; margin-top: 10px; }
        .co-po-label { font-size: 10.5px; color: #999; font-weight: 600; margin-right: 2px; }
        .po-chip { font-size: 10.5px; font-weight: 700; padding: 3px 10px; border-radius: 10px; border: 1px solid #ddd; background: #fff; color: #999; cursor: pointer; transition: all 0.15s; }
        .po-chip:hover { border-color: #0f2557; }
        .po-chip.mapped { background: #0f2557; color: #fff; border-color: #0f2557; }

        /* Modal */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 999; align-items: center; justify-content: center; }
        .modal-overlay.open { display: flex; }
        .modal { background: #fff; border-radius: 10px; padding: 24px 26px; width: 460px; max-width: 95vw; box-shadow: 0 8px 32px rgba(0,0,0,0.25); }
        .modal-title { font-size: 15px; font-weight: 700; color: #1a1a2e; margin-bottom: 18px; }
        .modal-field { margin-bottom: 13px; }
        .modal-field label { display: block; font-size: 11.5px; font-weight: 700; color: #333; margin-bottom: 4px; }
        .modal-field input[type=text], .modal-field input[type=file], .modal-field select { width: 100%; padding: 8px 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 12.5px; color: #333; outline: none; font-family: Arial, sans-serif; }
        .modal-field input:focus, .modal-field select:focus { border-color: #0f2557; }
        .modal-field select { appearance: none; -webkit-appearance: none; background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%23666' d='M5 7L0 2h10z'/%3E%3C/svg%3E") no-repeat right 10px center; cursor: pointer; }
        .modal-error { display: none; background: #fee2e2; border: 1px solid #fca5a5; color: #b91c1c; font-size: 11px; padding: 8px 10px; border-radius: 4px; margin-bottom: 12px; }
        .modal-row { display: grid; grid-template-columns: 2fr 1fr; gap: 12px; }
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
            <li class="{{ request()->is('program-head/dashboard') ? 'active' : '' }}">
                <a href="{{ url('/program-head/dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>Dashboard</a>
            </li>
            @endif
            @if($navPermissions['template-review'] ?? true)
            <li class="{{ request()->is('program-head/template-review*') ? 'active' : '' }}">
                <a href="{{ url('/program-head/template-review') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>Template Distribution</a>
            </li>
            @endif
            @if($navPermissions['course-oversight'] ?? true)
            <li class="{{ request()->is('program-head/course-oversight*') ? 'active' : '' }}">
                <a href="{{ url('/program-head/course-oversight') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>Course Oversight</a>
            </li>
            @endif
            @if($navPermissions['program-assignment'] ?? true)
            <li class="{{ request()->is('program-head/program-assignment*') ? 'active' : '' }}"><a href="{{ url('/program-head/program-assignment') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>Program Assignment</a></li>
            @endif
            @if($navPermissions['submissions'] ?? true)
            <li class="{{ request()->is('program-head/submissions*') ? 'active' : '' }}"><a href="{{ url('/program-head/submissions') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>Submissions and Deadline</a></li>
            @endif
            @if($navPermissions['account-management'] ?? true)
            <li class="{{ request()->is('program-head/account-management*') ? 'active' : '' }}">
                <a href="{{ url('/program-head/account-management') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>Account Management</a>
            </li>
            @endif
            @if($navPermissions['announcements'] ?? true)
            <li class="{{ request()->is('program-head/announcements*') ? 'active' : '' }}">
                <a href="{{ url('/program-head/announcements') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>Announcements
                @if(($unreadAnnouncementsCount ?? 0) > 0)
                    <span class="nav-badge">{{ $unreadAnnouncementsCount }}</span>
                @endif
            </a>
            </li>
            @endif
            @if($navPermissions['calendar'] ?? true)
            <li class="{{ request()->is('program-head/calendar*') ? 'active' : '' }}">
                <a href="{{ url('/program-head/calendar') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Calendar of Activities</a>
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

    <div class="main">
        <div class="topnav">
            <span class="label">Current role</span>
            <div class="topnav-right">
                <span class="role-badge">Program Head</span>
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

            <div class="page-title">Course Oversight</div>
            <div class="page-sub">Manage master course materials for the program: <strong>{{ $myProgram }}</strong></div>

            {{-- Course selector + Upload --}}
            <div class="course-select-box">
                <div style="flex:1;">
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
                <button class="btn-upload" onclick="openUploadModal()" {{ !$selectedCourse ? 'disabled' : '' }}>
                    <svg style="width:14px;height:14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    Upload Material
                </button>
            </div>

            {{-- Sub tabs --}}
            <div class="sub-tabs">
                <span class="sub-tab active" onclick="switchSubTab('materials', this)">Materials</span>
                <span class="sub-tab" onclick="switchSubTab('outcomes', this)">Outcomes (OBE)</span>
            </div>

            {{-- ═══ MATERIALS ═══ --}}
            <div class="tab-content active" id="tab-materials">
            @if(!$selectedCourse)
                <div class="folder-panel">
                    <div class="folder-empty">📁 Please select a course above to manage its materials.</div>
                </div>
            @else
                @foreach(\App\Models\CourseMaterial::TYPES as $typeKey => $typeLabel)
                    <div class="folder-panel" style="margin-bottom: 14px;">
                        <div class="folder-panel-header">
                            <span class="folder-icon">📁</span>
                            <div>
                                <div class="folder-title">{{ $typeLabel }}</div>
                                <div class="folder-sub">{{ $selectedCourse->code }} — visible to all faculty members handling this course</div>
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
                                        <div class="file-meta">
                                            {{ strtoupper($material->file_type) }} • {{ $material->version }} • {{ $material->readable_size }} • {{ $material->created_at->format('M d, Y') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="file-actions">
                                    <a class="btn-view" href="{{ Storage::url($material->file_path) }}" target="_blank">View</a>
                                    <form method="POST" action="{{ url('/program-head/course-oversight/materials/' . $material->id) }}" onsubmit="return confirm('Delete this material?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-del" title="Delete">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                                        </button>
                                    </form>
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

                {{-- Program Outcomes --}}
                <div class="folder-panel" style="margin-bottom: 14px;">
                    <div class="folder-panel-header">
                        <span class="folder-icon">🎯</span>
                        <div>
                            <div class="folder-title">Program Outcomes (PO)</div>
                            <div class="folder-sub">Applies to the whole {{ $myProgram }} program</div>
                        </div>
                        <button class="btn-upload" style="margin-left:auto;" type="button" onclick="openPoModal()">+ Add PO</button>
                    </div>

                    @forelse($programOutcomes as $po)
                        <div class="file-row">
                            <div class="file-left">
                                <span class="outcome-code">{{ $po->code }}</span>
                                <div class="file-name" style="font-weight:400;">{{ $po->description }}</div>
                            </div>
                            <form method="POST" action="{{ url('/program-head/course-oversight/program-outcomes/' . $po->id) }}" onsubmit="return confirm('Remove this Program Outcome?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-del" title="Delete">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="folder-empty">No Program Outcomes defined yet.</div>
                    @endforelse
                </div>

                {{-- Course Outcomes --}}
                @if(!$selectedCourse)
                    <div class="folder-panel">
                        <div class="folder-empty">📁 Select a course above to manage its Course Outcomes and CO–PO mapping.</div>
                    </div>
                @else
                    <div class="folder-panel">
                        <div class="folder-panel-header">
                            <span class="folder-icon">🎯</span>
                            <div>
                                <div class="folder-title">Course Outcomes (CO) — {{ $selectedCourse->code }}</div>
                                <div class="folder-sub">Core academic content faculty should keep consistent across sections</div>
                            </div>
                            <button class="btn-upload" style="margin-left:auto;" type="button" onclick="openCoModal()" {{ $programOutcomes->isEmpty() ? 'disabled title="Add a Program Outcome first"' : '' }}>+ Add CO</button>
                        </div>

                        @forelse($courseOutcomes as $co)
                            <div class="co-card">
                                <div class="co-card-top">
                                    <div>
                                        <span class="outcome-code">{{ $co->code }}</span>
                                        <span class="file-name" style="font-weight:400;">{{ $co->description }}</span>
                                    </div>
                                    <form method="POST" action="{{ url('/program-head/course-oversight/course-outcomes/' . $co->id) }}" onsubmit="return confirm('Remove this Course Outcome?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-del" title="Delete">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                                        </button>
                                    </form>
                                </div>

                                @if($co->sample_activities)
                                    <div class="co-activities"><strong>Sample activities:</strong> {{ $co->sample_activities }}</div>
                                @endif

                                <div class="co-po-list">
                                    <span class="co-po-label">Mapped POs:</span>
                                    @foreach($programOutcomes as $po)
                                        <form method="POST" action="{{ url('/program-head/course-oversight/course-outcomes/' . $co->id . '/mapping/' . $po->id) }}" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="po-chip {{ $co->isMappedTo($po->id) ? 'mapped' : '' }}">{{ $po->code }}</button>
                                        </form>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="folder-empty">No Course Outcomes defined yet for this course.</div>
                        @endforelse
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- Add Program Outcome Modal --}}
    <div class="modal-overlay" id="po-overlay">
        <div class="modal">
            <form method="POST" action="{{ url('/program-head/course-oversight/program-outcomes') }}">
                @csrf
                <div class="modal-title">Add Program Outcome</div>
                <div class="modal-row">
                    <div class="modal-field">
                        <label>Code <span style="color:#ef4444">*</span></label>
                        <input type="text" name="code" placeholder="e.g. PO1" required>
                    </div>
                </div>
                <div class="modal-field">
                    <label>Description <span style="color:#ef4444">*</span></label>
                    <textarea name="description" rows="3" placeholder="e.g. Apply core accounting principles to real-world business problems." required style="width:100%;padding:8px 10px;border:1px solid #ccc;border-radius:5px;font-size:12.5px;font-family:Arial, sans-serif;resize:vertical;"></textarea>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closePoModal()">Cancel</button>
                    <button type="submit" class="btn-save">Add</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Add Course Outcome Modal --}}
    <div class="modal-overlay" id="co-overlay">
        <div class="modal">
            <form method="POST" action="{{ url('/program-head/course-oversight/course-outcomes') }}">
                @csrf
                <input type="hidden" name="course_id" value="{{ $selectedCourse->id ?? '' }}">
                <div class="modal-title">Add Course Outcome</div>
                <div class="modal-row">
                    <div class="modal-field">
                        <label>Code <span style="color:#ef4444">*</span></label>
                        <input type="text" name="code" placeholder="e.g. CO1" required>
                    </div>
                </div>
                <div class="modal-field">
                    <label>Description <span style="color:#ef4444">*</span></label>
                    <textarea name="description" rows="3" placeholder="e.g. Prepare basic financial statements." required style="width:100%;padding:8px 10px;border:1px solid #ccc;border-radius:5px;font-size:12.5px;font-family:Arial, sans-serif;resize:vertical;"></textarea>
                </div>
                <div class="modal-field">
                    <label>Sample Learning Activities <span style="font-size:10px;color:#999;">(optional)</span></label>
                    <textarea name="sample_activities" rows="3" placeholder="e.g. Group case study preparing an income statement from raw ledger data." style="width:100%;padding:8px 10px;border:1px solid #ccc;border-radius:5px;font-size:12.5px;font-family:Arial, sans-serif;resize:vertical;"></textarea>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeCoModal()">Cancel</button>
                    <button type="submit" class="btn-save">Add</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Upload Modal --}}
    <div class="modal-overlay" id="upload-overlay">
        <div class="modal">
            <form method="POST" action="{{ url('/program-head/course-oversight/materials') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="course_id" value="{{ $selectedCourse->id ?? '' }}">
                <div class="modal-title">Upload Course Material</div>

                <div class="modal-field">
                    <label>Category <span style="color:#ef4444">*</span></label>
                    <select name="type" required>
                        @foreach(\App\Models\CourseMaterial::TYPES as $typeKey => $typeLabel)
                            <option value="{{ $typeKey }}">{{ $typeLabel }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="modal-row">
                    <div class="modal-field">
                        <label>Title <span style="color:#ef4444">*</span></label>
                        <input type="text" name="title" placeholder="e.g. Official Syllabus" required>
                    </div>
                    <div class="modal-field">
                        <label>Version</label>
                        <input type="text" name="version" placeholder="v1.0">
                    </div>
                </div>

                <div class="modal-field">
                    <label>File <span style="color:#ef4444">*</span></label>
                    <input type="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.zip" required>
                    <div style="font-size:10.5px;color:#999;margin-top:4px;">Allowed: PDF, Word, Excel, ZIP · Max 20MB</div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeUploadModal()">Cancel</button>
                    <button type="submit" class="btn-save">Upload</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openUploadModal() {
            document.getElementById('upload-overlay').classList.add('open');
        }
        function closeUploadModal() {
            document.getElementById('upload-overlay').classList.remove('open');
        }
        document.getElementById('upload-overlay').addEventListener('click', function(e) {
            if (e.target === this) closeUploadModal();
        });

        function switchSubTab(tabName, el) {
            document.querySelectorAll('.sub-tab').forEach(t => t.classList.remove('active'));
            el.classList.add('active');
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            document.getElementById('tab-' + tabName).classList.add('active');
        }

        function openPoModal() { document.getElementById('po-overlay').classList.add('open'); }
        function closePoModal() { document.getElementById('po-overlay').classList.remove('open'); }
        document.getElementById('po-overlay').addEventListener('click', function(e) { if (e.target === this) closePoModal(); });

        function openCoModal() { document.getElementById('co-overlay').classList.add('open'); }
        function closeCoModal() { document.getElementById('co-overlay').classList.remove('open'); }
        document.getElementById('co-overlay').addEventListener('click', function(e) { if (e.target === this) closeCoModal(); });

        // Re-open the Outcomes tab after a redirect if there was a validation error there
        @if($errors->any())
            document.addEventListener('DOMContentLoaded', function() {
                var outcomesTab = document.querySelectorAll('.sub-tab')[1];
                if (outcomesTab) switchSubTab('outcomes', outcomesTab);
            });
        @endif
    </script>

</body>
</html>