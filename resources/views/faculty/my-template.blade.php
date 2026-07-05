<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Templates – CBMA System</title>
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
        .alert-success { background: #dcfce7; color: #166534; padding: 12px 16px; margin-bottom: 16px; border: 1px solid #bbf7d0; border-radius: 8px; font-size: 13px; }
        .alert-error { background: #fee2e2; color: #b91c1c; padding: 12px 16px; margin-bottom: 16px; border: 1px solid #fca5a5; border-radius: 8px; font-size: 13px; }

        .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 20px; }
        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 3px; }
        .page-sub { font-size: 11.5px; color: #888; }
        .btn-create { display: flex; align-items: center; gap: 6px; background: #0f2557; color: #fff; border: none; border-radius: 6px; font-size: 12.5px; font-weight: 600; padding: 9px 18px; cursor: pointer; transition: background 0.15s; }
        .btn-create:hover { background: #1a3a7a; }

        .type-tabs { display: flex; border-bottom: 2px solid #e0e0e0; margin-bottom: 22px; }
        .type-tab { padding: 8px 18px; font-size: 13px; color: #666; text-decoration: none; border-bottom: 2px solid transparent; margin-bottom: -2px; }
        .type-tab:hover { color: #0f2557; }
        .type-tab.active { color: #0f2557; font-weight: 700; border-bottom: 2px solid #0f2557; }

        .template-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px; }
        .template-card { background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; padding: 16px; transition: box-shadow 0.15s; }
        .template-card:hover { box-shadow: 0 3px 12px rgba(0,0,0,0.08); }
        .card-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 12px; }
        .card-icon { font-size: 22px; }
        .card-title { font-size: 14px; font-weight: 700; color: #1a1a2e; margin-bottom: 4px; }
        .card-meta { font-size: 10.5px; color: #aaa; margin-bottom: 14px; }
        .card-actions { display: flex; gap: 6px; }
        .btn-sm { display: flex; align-items: center; gap: 4px; font-size: 11.5px; font-weight: 600; padding: 6px 12px; border-radius: 5px; cursor: pointer; border: 1px solid transparent; text-decoration: none; }
        .btn-edit { background: #0f2557; color: #fff; border: none; }
        .btn-edit:hover { background: #1a3a7a; }
        .btn-view-file { background: #fff; color: #333; border: 1px solid #d0d0d0; }
        .btn-view-file:hover { background: #f5f5f5; }
        .btn-del-sm { background: #fff; color: #999; border: 1px solid #e0e0e0; }
        .btn-del-sm:hover { background: #fee2e2; color: #ef4444; border-color: #fca5a5; }
        .btn-sm svg { width: 12px; height: 12px; }

        .status-badge { font-size: 10px; font-weight: 700; padding: 4px 10px; border-radius: 12px; }
        .status-pending_review   { background: #dbeafe; color: #1e40af; }
        .status-needs_revision   { background: #fee2e2; color: #991b1b; }
        .status-pending_approval { background: #fef9c3; color: #92400e; }
        .status-approved         { background: #d1fae5; color: #065f46; }
        .status-rejected         { background: #f3f4f6; color: #6b7280; }

        .review-note { font-size: 10.5px; color: #b91c1c; background: #fef2f2; border: 1px solid #fecaca; border-radius: 5px; padding: 6px 8px; margin-bottom: 12px; line-height: 1.4; }

        .empty-state { grid-column: 1 / -1; text-align: center; padding: 50px 20px; color: #bbb; font-size: 13px; }

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

            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert-error">{{ $errors->first() }}</div>
            @endif

            <div class="page-header">
                <div>
                    <div class="page-title">My Templates</div>
                    <div class="page-sub">Manage your course documents</div>
                </div>
                <button class="btn-create" onclick="openCreateModal()">+ Create New</button>
            </div>

            {{-- Type tabs --}}
            <div class="type-tabs">
                <a class="type-tab {{ $type == 'syllabus' ? 'active' : '' }}" href="{{ url('/faculty/my-template?type=syllabus') }}">Syllabus</a>
                <a class="type-tab {{ $type == 'lesson_plan' ? 'active' : '' }}" href="{{ url('/faculty/my-template?type=lesson_plan') }}">Lesson Plan</a>
                <a class="type-tab {{ $type == 'course_guide' ? 'active' : '' }}" href="{{ url('/faculty/my-template?type=course_guide') }}">Course Guide</a>
                <a class="type-tab {{ $type == 'module' ? 'active' : '' }}" href="{{ url('/faculty/my-template?type=module') }}">Module</a>
            </div>

            {{-- Templates grid --}}
            <div class="template-grid">
                @forelse($templates as $template)
                    <div class="template-card">
                        <div class="card-top">
                            <span class="card-icon">
                                @if($template->file_type == 'pdf') 📄 @else 📝 @endif
                            </span>
                            <span class="status-badge status-{{ $template->status }}">{{ $template->status_label }}</span>
                        </div>

                        <div class="card-title">{{ $template->title }}</div>
                        <div class="card-meta">
                            {{ strtoupper($template->file_type) }} • {{ $template->readable_size }} • {{ $template->updated_at->format('Y-m-d') }}
                        </div>

                        @if($template->review_note && in_array($template->status, ['needs_revision', 'rejected']))
                            <div class="review-note"><strong>Feedback:</strong> {{ $template->review_note }}</div>
                        @endif

                        <div class="card-actions">
                            <button class="btn-sm btn-edit"
                                onclick="openEditModal('{{ $template->id }}', '{{ addslashes($template->title) }}')">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                Edit
                            </button>
                            <a class="btn-sm btn-view-file" href="{{ Storage::url($template->file_path) }}" target="_blank">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                View
                            </a>
                            <form method="POST" action="{{ url('/faculty/my-template/' . $template->id) }}" onsubmit="return confirm('Delete this template?')" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-sm btn-del-sm">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">Wala ka pang {{ ucwords(str_replace('_', ' ', $type)) }} templates. Click "Create New" para mag-upload.</div>
                @endforelse
            </div>

        </div>
    </div>

    {{-- Create Modal --}}
    <div class="modal-overlay" id="create-overlay">
        <div class="modal">
            <form method="POST" action="{{ url('/faculty/my-template') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-title">Create New Template</div>

                <div class="modal-field">
                    <label>Title <span style="color:#ef4444">*</span></label>
                    <input type="text" name="title" placeholder="e.g. CAE1 Syllabus" required>
                </div>

                <div class="modal-field">
                    <label>Type <span style="color:#ef4444">*</span></label>
                    <select name="type" required>
                        <option value="syllabus" {{ $type == 'syllabus' ? 'selected' : '' }}>Syllabus</option>
                        <option value="lesson_plan" {{ $type == 'lesson_plan' ? 'selected' : '' }}>Lesson Plan</option>
                        <option value="course_guide" {{ $type == 'course_guide' ? 'selected' : '' }}>Course Guide</option>
                        <option value="module" {{ $type == 'module' ? 'selected' : '' }}>Module</option>
                    </select>
                </div>

                <div class="modal-field">
                    <label>File <span style="color:#ef4444">*</span></label>
                    <input type="file" name="file" accept=".pdf,.doc,.docx" required>
                    <div class="modal-hint">Allowed: PDF, Word · Max 20MB · Isu-submit ito para sa review.</div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeCreateModal()">Cancel</button>
                    <button type="submit" class="btn-save">Upload & Submit</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div class="modal-overlay" id="edit-overlay">
        <div class="modal">
            <form id="edit-form" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-title">Edit Template</div>

                <div class="modal-field">
                    <label>Title <span style="color:#ef4444">*</span></label>
                    <input type="text" id="edit-title" name="title" required>
                </div>

                <div class="modal-field">
                    <label>Replace File (optional)</label>
                    <input type="file" name="file" accept=".pdf,.doc,.docx">
                    <div class="modal-hint">Iwanan blangko kung ayaw palitan ang file. Kapag na-reject/needs revision, ang pag-edit ay magre-resubmit para sa review.</div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn-save">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCreateModal() { document.getElementById('create-overlay').classList.add('open'); }
        function closeCreateModal() { document.getElementById('create-overlay').classList.remove('open'); }

        function openEditModal(id, title) {
            document.getElementById('edit-title').value = title;
            document.getElementById('edit-form').action = '{{ url('/faculty/my-template') }}/' + id;
            document.getElementById('edit-overlay').classList.add('open');
        }
        function closeEditModal() { document.getElementById('edit-overlay').classList.remove('open'); }

        document.getElementById('create-overlay').addEventListener('click', function(e) { if (e.target === this) closeCreateModal(); });
        document.getElementById('edit-overlay').addEventListener('click', function(e) { if (e.target === this) closeEditModal(); });
    </script>

</body>
</html>