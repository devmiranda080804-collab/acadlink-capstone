<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CMS – CBMA System</title>
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
            display: flex; flex-direction: column;
            flex-shrink: 0; overflow-y: auto;
        }

        .sidebar-logo {
            display: flex; flex-direction: column; align-items: center;
            padding: 22px 16px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-logo img { width: 72px; height: 72px; border-radius: 50%; object-fit: contain; background: #1b3d7a; }
        .sidebar-logo .brand { color: #fff; font-size: 15px; font-weight: 700; margin-top: 8px; }
        .sidebar-logo .brand-sub { color: #a0b4d6; font-size: 10px; margin-top: 2px; }

        .nav-list { list-style: none; padding: 10px 0; flex: 1; }

        .nav-list li a {
            display: flex; align-items: center; gap: 11px;
            padding: 11px 20px;
            color: #c8d6ec; text-decoration: none; font-size: 13px;
            transition: background 0.15s, color 0.15s;
        }

        .nav-list li a:hover { background-color: rgba(255,255,255,0.08); color: #fff; }
        .nav-list li.active a { background-color: rgba(255,255,255,0.08); color: #fff; border-left: 3px solid #fff; }
        .nav-list li a svg { width: 18px; height: 18px; flex-shrink: 0; opacity: 0.85; }
        .nav-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 16px; height: 16px; padding: 0 4px; margin-left: auto; background: #ef4444; color: #fff; font-size: 10px; font-weight: 700; border-radius: 999px; }

        .sidebar-logout { padding: 12px 0; border-top: 1px solid rgba(255,255,255,0.1); }
        .sidebar-logout a {
            display: flex; align-items: center; gap: 11px;
            padding: 11px 20px; color: #c8d6ec; text-decoration: none; font-size: 13px;
            transition: background 0.15s;
        }
        .sidebar-logout a:hover { background-color: rgba(255,255,255,0.08); color: #fff; }

        /* ═══════════════════ MAIN ═══════════════════ */
        .main { flex: 1; display: flex; flex-direction: column; overflow: hidden; background-color: #f5f6fa; }

        .topnav {
            background: #fff; border-bottom: 1px solid #e0e0e0;
            padding: 0 24px; height: 52px;
            display: flex; align-items: center; justify-content: space-between;
            flex-shrink: 0;
        }

        .topnav .label { font-size: 12px; color: #666; }
        .topnav-right { display: flex; align-items: center; gap: 12px; }
        .role-badge { background-color: #0f2557; color: #fff; font-size: 12px; font-weight: 600; padding: 5px 16px; border-radius: 20px; }
        .user-info { display: flex; align-items: center; gap: 8px; }
        .user-text { text-align: right; }
        .user-name { font-size: 12px; font-weight: 600; color: #222; }
        .user-email { font-size: 10px; color: #888; }
        .user-avatar { width: 34px; height: 34px; border-radius: 50%; background-color: #0f2557; color: #fff; font-size: 12px; font-weight: 700; display: flex; align-items: center; justify-content: center; }

        /* ═══════════════════ CONTENT ═══════════════════ */
        .content { flex: 1; overflow-y: auto; padding: 24px 28px 28px; }

        .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 20px; }
        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 3px; }
        .page-sub { font-size: 11.5px; color: #888; max-width: 480px; }
        .alert-success { background: #dcfce7; color: #166534; padding: 12px 16px; margin-bottom: 16px; border: 1px solid #bbf7d0; border-radius: 8px; font-size: 13px; }
        .alert-error { background: #fee2e2; color: #b91c1c; padding: 12px 16px; margin-bottom: 16px; border: 1px solid #fca5a5; border-radius: 8px; font-size: 13px; }

        .btn-create { display: flex; align-items: center; gap: 6px; background: #0f2557; color: #fff; border: none; border-radius: 6px; font-size: 12.5px; font-weight: 600; padding: 9px 18px; cursor: pointer; transition: background 0.15s; white-space: nowrap; }
        .btn-create:hover { background: #1a3a7a; }

        .module-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px; }
        .module-card { background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; padding: 16px; transition: box-shadow 0.15s; }
        .module-card:hover { box-shadow: 0 3px 12px rgba(0,0,0,0.08); }
        .card-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 12px; }
        .card-icon { font-size: 22px; }
        .card-title { font-size: 14px; font-weight: 700; color: #1a1a2e; margin-bottom: 4px; }
        .card-desc { font-size: 11.5px; color: #666; margin-bottom: 8px; line-height: 1.5; }
        .card-meta { font-size: 10.5px; color: #aaa; margin-bottom: 14px; }
        .card-actions { display: flex; gap: 6px; flex-wrap: wrap; }
        .btn-sm { display: flex; align-items: center; gap: 4px; font-size: 11.5px; font-weight: 600; padding: 6px 12px; border-radius: 5px; cursor: pointer; border: 1px solid transparent; text-decoration: none; }
        .btn-view-file { background: #fff; color: #333; border: 1px solid #d0d0d0; }
        .btn-view-file:hover { background: #f5f5f5; }
        .btn-edit-sm { background: #fff; color: #333; border: 1px solid #d0d0d0; }
        .btn-edit-sm:hover { background: #f5f5f5; }
        .btn-del-sm { background: #fff; color: #999; border: 1px solid #e0e0e0; }
        .btn-del-sm:hover { background: #fee2e2; color: #ef4444; border-color: #fca5a5; }
        .btn-sm svg { width: 12px; height: 12px; }

        .empty-state { grid-column: 1 / -1; text-align: center; padding: 60px 20px; color: #bbb; font-size: 12.5px; background: #fff; border: 1px solid #e4e4e4; border-radius: 8px; }

        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 999; align-items: center; justify-content: center; }
        .modal-overlay.open { display: flex; }
        .modal { background: #fff; border-radius: 10px; padding: 24px 26px; width: 440px; max-width: 95vw; box-shadow: 0 8px 32px rgba(0,0,0,0.25); }
        .modal.modal-wide { width: 960px; }
        .editor-wrap { border-radius: 5px; overflow: hidden; }
        .view-content-body { border: 1px solid #e4e4e4; border-radius: 6px; padding: 16px 18px; max-height: 60vh; overflow-y: auto; font-size: 13px; line-height: 1.6; color: #222; }
        .modal-title { font-size: 15px; font-weight: 700; color: #1a1a2e; margin-bottom: 16px; }
        .modal-field { margin-bottom: 13px; }
        .modal-field label { display: block; font-size: 11.5px; font-weight: 700; color: #333; margin-bottom: 4px; }
        .modal-field input, .modal-field select, .modal-field textarea { width: 100%; padding: 8px 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 12.5px; outline: none; font-family: Arial, sans-serif; }
        .modal-field textarea { resize: vertical; min-height: 60px; }
        .modal-field select { appearance: none; -webkit-appearance: none; background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%23666' d='M5 7L0 2h10z'/%3E%3C/svg%3E") no-repeat right 10px center; cursor: pointer; }
        .modal-error { display: none; background: #fee2e2; border: 1px solid #fca5a5; color: #b91c1c; font-size: 11px; padding: 8px 10px; border-radius: 4px; margin-bottom: 12px; }
        .modal-hint { background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 6px; padding: 10px 12px; font-size: 11.5px; color: #3730a3; }
        .modal-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 18px; }
        .btn-cancel { background: #fff; border: 1px solid #ccc; color: #444; font-size: 12.5px; font-weight: 600; padding: 8px 18px; border-radius: 5px; cursor: pointer; }
        .btn-cancel:hover { background: #f5f5f5; }
        .btn-save { background: #0f2557; color: #fff; border: none; font-size: 12.5px; font-weight: 600; padding: 8px 20px; border-radius: 5px; cursor: pointer; }
        .btn-save:hover { background: #1a3a7a; }

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
                @if(($newTemplatesCount ?? 0) > 0)
                    <span class="nav-badge">{{ $newTemplatesCount }}</span>
                @endif
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
        <a href="#" onclick="openLogoutModal()">
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

            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert-error">{{ $errors->first() }}</div>
            @endif

            <div class="page-header">
                <div>
                    <div class="page-title">CMS — My Content</div>
                    <div class="page-sub">Create, edit, and manage your own instructional content and modules — written and edited directly here, or uploaded as a file.</div>
                </div>
                <button class="btn-create" type="button" onclick="openCreateModal()">+ New Module</button>
            </div>

            <div class="module-grid">
                @forelse($modules as $module)
                    <div class="module-card">
                        <div class="card-top">
                            <span class="card-icon">
                                @if($module->isWritten()) ✍️ @elseif($module->isGoogleDoc()) 📑 @elseif($module->file_type == 'pdf') 📄 @else 📝 @endif
                            </span>
                        </div>

                        <div class="card-title">{{ $module->title }}</div>
                        @if($module->description)
                            <div class="card-desc">{{ $module->description }}</div>
                        @endif
                        <div class="card-meta">
                            @if($module->isWritten())
                                Written in AcadLink
                            @elseif($module->isGoogleDoc())
                                Google Doc
                            @else
                                {{ strtoupper($module->file_type) }} • {{ $module->readable_size }}
                            @endif
                            • {{ $module->created_at->format('Y-m-d') }}
                        </div>

                        <div class="card-actions">
                            @if($module->isWritten())
                                <button type="button" class="btn-sm btn-edit-sm" onclick="openContentEditor({{ $module->id }}, {{ json_encode($module->title) }}, {{ json_encode($module->description) }}, {{ json_encode($module->content) }})">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4z"/></svg>
                                    Open & Edit
                                </button>
                            @elseif($module->isGoogleDoc())
                                <a class="btn-sm btn-view-file" href="{{ $module->google_edit_url }}" target="_blank">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    Open
                                </a>
                                <button type="button" class="btn-sm btn-edit-sm" onclick="openEditModal({{ $module->id }}, {{ json_encode($module->title) }}, {{ json_encode($module->description) }})">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4z"/></svg>
                                    Edit
                                </button>
                            @elseif($module->isEditableDocx())
                                <button type="button" class="btn-sm btn-edit-sm" onclick="openDocxEditor({{ $module->id }}, {{ json_encode($module->title) }})">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4z"/></svg>
                                    Open & Edit
                                </button>
                                <button type="button" class="btn-sm btn-edit-sm" onclick="openEditModal({{ $module->id }}, {{ json_encode($module->title) }}, {{ json_encode($module->description) }})">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4z"/></svg>
                                    Edit
                                </button>
                            @else
                                <a class="btn-sm btn-view-file" href="{{ Storage::url($module->file_path) }}" target="_blank">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    View
                                </a>
                                <button type="button" class="btn-sm btn-edit-sm" onclick="openEditModal({{ $module->id }}, {{ json_encode($module->title) }}, {{ json_encode($module->description) }})">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4z"/></svg>
                                    Edit
                                </button>
                            @endif
                            <form method="POST" action="{{ url('/faculty/cms/' . $module->id) }}" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-sm btn-del-sm" onclick="confirmDeleteModule(this)">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">Wala ka pang content/module. Pindutin ang "+ New Module" para gumawa ng una mo.</div>
                @endforelse
            </div>

        </div>
    </div>

    {{-- Create Modal --}}
    <div class="modal-overlay" id="create-modal-overlay">
        <div class="modal" id="create-modal" >
            <form action="{{ url('/faculty/cms') }}" method="POST" enctype="multipart/form-data" id="create-form">
                @csrf
                <div class="modal-title">New Module</div>
                <div class="modal-error" id="create-modal-error">{{ $errors->first() }}</div>

                <div class="modal-field">
                    <label>Title <span style="color:#ef4444">*</span></label>
                    <input type="text" name="title" placeholder="e.g. Week 1 Learning Module" value="{{ old('title') }}">
                </div>

                <div class="modal-field">
                    <label>Description</label>
                    <textarea name="description" placeholder="Short description (optional)">{{ old('description') }}</textarea>
                </div>

                <div class="modal-field">
                    <label>How do you want to create this? <span style="color:#ef4444">*</span></label>
                    <select name="mode" id="mode-select" onchange="toggleMode()">
                        <option value="write">Write content here (AcadLink editor)</option>
                        <option value="upload_file">Upload a File (PDF/Word)</option>
                    </select>
                </div>

                <div class="modal-field" id="file-field">
                    <label>File <span style="color:#ef4444">*</span></label>
                    <input type="file" name="file" accept=".pdf,.doc,.docx">
                    <div class="modal-hint">For PDF/Word files, we'll try to bring the content in as editable text here — like importing a file into Google Docs. If that's not possible (e.g. older .doc files, or a scanned PDF with no selectable text), it's kept as a plain file instead.</div>
                </div>

                <div class="modal-field" id="write-field">
                    <label>Content <span style="color:#ef4444">*</span></label>
                    <div class="editor-wrap"><textarea id="create-editor"></textarea></div>
                    <input type="hidden" name="content" id="create-content-input">
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeCreateModal()">Cancel</button>
                    <button type="submit" class="btn-save">Create</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div class="modal-overlay" id="edit-modal-overlay">
        <div class="modal">
            <form id="edit-form" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-title">Edit Module</div>

                <div class="modal-field">
                    <label>Title <span style="color:#ef4444">*</span></label>
                    <input type="text" name="title" id="edit-title">
                </div>

                <div class="modal-field">
                    <label>Description</label>
                    <textarea name="description" id="edit-description"></textarea>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn-save">Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Content Editor Modal (written modules — open and edit in one place) --}}
    <div class="modal-overlay" id="content-editor-overlay">
        <div class="modal modal-wide">
            <form id="content-editor-form" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-title">Edit Module</div>

                <div class="modal-field">
                    <label>Title <span style="color:#ef4444">*</span></label>
                    <input type="text" name="title" id="content-editor-title">
                </div>

                <div class="modal-field">
                    <label>Description</label>
                    <textarea name="description" id="content-editor-description"></textarea>
                </div>

                <div class="modal-field">
                    <label>Content</label>
                    <div class="editor-wrap"><textarea id="content-editor"></textarea></div>
                    <input type="hidden" name="content" id="content-editor-input">
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeContentEditor()">Cancel</button>
                    <button type="submit" class="btn-save">Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete confirmation --}}
    <div class="modal-overlay" id="delete-module-overlay">
        <div class="modal" style="width:380px;">
            <div class="modal-title">Delete this module?</div>
            <div style="font-size:13px;color:#555;">This can't be undone. The content and any uploaded file will be removed.</div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeDeleteModule()">Cancel</button>
                <button type="button" onclick="submitDeleteModule()" style="background:#ef4444;color:#fff;border:none;font-size:12.5px;font-weight:600;padding:8px 18px;border-radius:5px;cursor:pointer;">Delete</button>
            </div>
        </div>
    </div>
    <script>
        var pendingDeleteModuleForm = null;
        function confirmDeleteModule(btn) {
            pendingDeleteModuleForm = btn.closest('form');
            document.getElementById('delete-module-overlay').classList.add('open');
        }
        function closeDeleteModule() {
            pendingDeleteModuleForm = null;
            document.getElementById('delete-module-overlay').classList.remove('open');
        }
        function submitDeleteModule() {
            if (pendingDeleteModuleForm) pendingDeleteModuleForm.submit();
        }
        document.getElementById('delete-module-overlay').addEventListener('click', function(e) { if (e.target === this) closeDeleteModule(); });
    </script>

    {{-- Uploaded Word file editor: shows the original file exactly as-is on the left,
         and lets only the wording of each paragraph change on the right. --}}
    <style>
        .docx-editor-modal { max-width: 1200px; width: 96vw; }
        .docx-editor-cols { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 10px; }
        .docx-editor-preview { height: 62vh; overflow: auto; background: #f1f3f7; border: 1px solid #dde1ea; border-radius: 6px; padding: 10px; }
        .docx-editor-text { height: 62vh; overflow: auto; padding-right: 4px; }
        .docx-para-input { width: 100%; box-sizing: border-box; border: 1px solid #d5dbe6; border-radius: 4px; padding: 6px 8px; font: 13px Arial, sans-serif; margin-bottom: 6px; resize: none; overflow: hidden; }
        .docx-editor-status { font-size: 12.5px; color: #666; padding: 10px; }
        .docx-editor-error { display: none; font-size: 12.5px; color: #b91c1c; margin-top: 8px; }
        @media (max-width: 800px) {
            .docx-editor-cols { grid-template-columns: 1fr; }
            .docx-editor-preview, .docx-editor-text { height: auto; max-height: 50vh; }
        }
    </style>

    <div class="modal-overlay" id="docx-editor-overlay">
        <div class="modal docx-editor-modal">
            <div class="modal-title" id="docx-editor-title">Edit Document</div>
            <div class="modal-hint">Ang layout, logo, at formatting ay mananatili gaya ng orihinal. Wording lang ng bawat paragraph ang puwedeng baguhin.</div>
            <div class="docx-editor-cols">
                <div class="docx-editor-preview" id="docx-preview-pane"></div>
                <div class="docx-editor-text" id="docx-text-list"></div>
            </div>
            <div class="docx-editor-error" id="docx-editor-error"></div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeDocxEditor()">Close</button>
                <button type="button" class="btn-save" id="docx-save-btn" onclick="saveDocxText()">Save Changes</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/docx-preview@0.4.1/dist/docx-preview.min.js"></script>
    <script>
        var docxEditingId = null;
        var docxOriginal = {};

        function openDocxEditor(id, title) {
            docxEditingId = id;
            document.getElementById('docx-editor-title').textContent = title;
            document.getElementById('docx-editor-overlay').classList.add('open');
            loadDocxEditor();
        }
        function closeDocxEditor() {
            document.getElementById('docx-editor-overlay').classList.remove('open');
        }
        document.getElementById('docx-editor-overlay').addEventListener('click', function(e) {
            if (e.target === this) closeDocxEditor();
        });

        function showDocxError(message) {
            var el = document.getElementById('docx-editor-error');
            el.textContent = message;
            el.style.display = message ? 'block' : 'none';
        }

        async function loadDocxEditor() {
            var pane = document.getElementById('docx-preview-pane');
            var list = document.getElementById('docx-text-list');
            showDocxError('');
            pane.innerHTML = '<div class="docx-editor-status">Loading document...</div>';
            list.innerHTML = '';

            try {
                var base = '/faculty/cms/' + docxEditingId;
                var responses = await Promise.all([
                    fetch(base + '/docx-file', { credentials: 'same-origin' }),
                    fetch(base + '/docx-text', { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                ]);
                if (!responses[0].ok || !responses[1].ok) {
                    throw new Error('Hindi ma-load ang document.');
                }

                var blob = await responses[0].blob();
                pane.innerHTML = '';
                await docx.renderAsync(blob, pane);

                var data = await responses[1].json();
                docxOriginal = data.paragraphs;
                renderDocxTextList(data.paragraphs);
            } catch (err) {
                pane.innerHTML = '';
                showDocxError(err.message);
            }
        }

        function renderDocxTextList(paragraphs) {
            var list = document.getElementById('docx-text-list');
            var indexes = Object.keys(paragraphs);

            if (indexes.length === 0) {
                list.innerHTML = '<div class="docx-editor-status">Walang text na puwedeng i-edit sa document na ito.</div>';
                return;
            }

            indexes.forEach(function(index) {
                var ta = document.createElement('textarea');
                ta.className = 'docx-para-input';
                ta.dataset.index = index;
                ta.value = paragraphs[index];
                ta.rows = 1;
                ta.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = this.scrollHeight + 'px';
                });
                list.appendChild(ta);
                ta.style.height = ta.scrollHeight + 'px';
            });
        }

        async function saveDocxText() {
            var changed = {};
            document.querySelectorAll('#docx-text-list textarea').forEach(function(ta) {
                var index = ta.dataset.index;
                if (ta.value !== (docxOriginal[index] || '')) {
                    changed[index] = ta.value;
                }
            });

            if (Object.keys(changed).length === 0) {
                closeDocxEditor();
                return;
            }

            var btn = document.getElementById('docx-save-btn');
            btn.disabled = true;
            showDocxError('');

            try {
                var res = await fetch('/faculty/cms/' + docxEditingId + '/docx-text', {
                    method: 'PUT',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ paragraphs: changed })
                });
                if (!res.ok) {
                    throw new Error('Hindi na-save ang mga pagbabago (' + res.status + ').');
                }
                await loadDocxEditor();
            } catch (err) {
                showDocxError(err.message);
            } finally {
                btn.disabled = false;
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js"></script>
    <script>
        // TinyMCE instead of a plain Quill WYSIWYG — it has real table support
        // and richer formatting (fonts, colors, alignment), so an uploaded
        // Word/PDF file's structure (including tables) actually survives once
        // opened here, not just at the storage level.
        var TINY_CONFIG = {
            menubar: false,
            plugins: 'lists table link code fullscreen',
            toolbar: 'undo redo | blocks | bold italic underline | forecolor backcolor | alignleft aligncenter alignright | bullist numlist | table | link | removeformat | fullscreen',
            height: 320,
            branding: false,
            promotion: false,
            content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6; } table { border-collapse: collapse; width: 100%; } td, th { border: 1px solid #999; padding: 6px 8px; vertical-align: top; } img { max-width: 100%; height: auto; }',
        };

        function toggleMode() {
            const isUpload = document.getElementById('mode-select').value === 'upload_file';
            document.getElementById('file-field').style.display = isUpload ? '' : 'none';
            document.getElementById('write-field').style.display = isUpload ? 'none' : '';
            if (!isUpload) {
                var ed = tinymce.get('create-editor');
                if (!ed) {
                    tinymce.init(Object.assign({ selector: '#create-editor' }, TINY_CONFIG));
                } else {
                    ed.setContent('');
                }
            }
        }

        function openCreateModal() {
            document.getElementById('create-modal-overlay').classList.add('open');
            document.getElementById('mode-select').value = 'write';
            toggleMode();
        }
        function closeCreateModal() {
            document.getElementById('create-modal-overlay').classList.remove('open');
        }
        document.getElementById('create-modal-overlay').addEventListener('click', function(e) {
            if (e.target === this) closeCreateModal();
        });
        document.getElementById('create-form').addEventListener('submit', function(e) {
            if (document.getElementById('mode-select').value === 'write') {
                var ed = tinymce.get('create-editor');
                document.getElementById('create-content-input').value = ed ? ed.getContent() : '';
            }
        });

        function openEditModal(id, title, description) {
            document.getElementById('edit-form').action = '{{ url("/faculty/cms") }}/' + id;
            document.getElementById('edit-title').value = title;
            document.getElementById('edit-description').value = description || '';
            document.getElementById('edit-modal-overlay').classList.add('open');
        }
        function closeEditModal() {
            document.getElementById('edit-modal-overlay').classList.remove('open');
        }
        document.getElementById('edit-modal-overlay').addEventListener('click', function(e) {
            if (e.target === this) closeEditModal();
        });

        function openContentEditor(id, title, description, content) {
            document.getElementById('content-editor-form').action = '{{ url("/faculty/cms") }}/' + id;
            document.getElementById('content-editor-title').value = title;
            document.getElementById('content-editor-description').value = description || '';
            document.getElementById('content-editor-overlay').classList.add('open');

            var ed = tinymce.get('content-editor');
            if (ed) {
                ed.setContent(content || '');
            } else {
                tinymce.init(Object.assign({ selector: '#content-editor', height: 560 }, TINY_CONFIG)).then(function(editors) {
                    editors[0].setContent(content || '');
                });
            }
        }
        function closeContentEditor() {
            document.getElementById('content-editor-overlay').classList.remove('open');
        }
        document.getElementById('content-editor-overlay').addEventListener('click', function(e) {
            if (e.target === this) closeContentEditor();
        });
        document.getElementById('content-editor-form').addEventListener('submit', function(e) {
            var ed = tinymce.get('content-editor');
            document.getElementById('content-editor-input').value = ed ? ed.getContent() : '';
        });

        @if($errors->any())
            document.addEventListener('DOMContentLoaded', function () {
                document.getElementById('create-modal-overlay').classList.add('open');
                document.getElementById('create-modal-error').style.display = 'block';
                document.getElementById('mode-select').value = @json(old('mode', 'write'));
                toggleMode();
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

</body>
</html>