<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS – CBMA System</title>
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
        .alert-success { background: #dcfce7; color: #166534; padding: 12px 16px; margin-bottom: 18px; border: 1px solid #bbf7d0; border-radius: 8px; font-size: 13px; }

        .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 20px; }
        .page-header-left h1 { font-size: 20px; font-weight: 700; color: #1a1a2e; }
        .page-header-left p { font-size: 12.5px; color: #666; margin-top: 3px; max-width: 560px; }

        .btn-primary { display: flex; align-items: center; gap: 6px; background: #0f2557; color: #fff; border: none; border-radius: 6px; font-size: 12.5px; font-weight: 600; padding: 9px 18px; cursor: pointer; transition: background 0.15s; white-space: nowrap; }
        .btn-primary:hover { background: #1a3a7a; }

        .layout { display: flex; gap: 20px; }

        /* Template type tabs (vertical) */
        .type-sidebar { width: 190px; flex-shrink: 0; }
        .type-tab { display: block; padding: 10px 14px; font-size: 13px; color: #444; text-decoration: none; border-radius: 6px; margin-bottom: 4px; transition: background 0.15s; text-transform: capitalize; }
        .type-tab:hover { background: #e8ecf8; color: #0f2557; }
        .type-tab.active { background: #0f2557; color: #fff; font-weight: 600; }
        .btn-new-type { width: 100%; margin-top: 8px; background: #fff; border: 1px dashed #aaa; color: #666; border-radius: 6px; font-size: 12px; font-weight: 600; padding: 9px 12px; cursor: pointer; transition: background 0.15s; }
        .btn-new-type:hover { background: #eef2ff; border-color: #0f2557; color: #0f2557; }

        /* Elements panel */
        .elements-panel { flex: 1; background: #fff; border: 1px solid #e4e4e4; border-radius: 8px; overflow: hidden; }
        .panel-toolbar { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-bottom: 1px solid #f0f0f0; }
        .panel-toolbar-title { font-size: 14px; font-weight: 700; color: #1a1a2e; text-transform: capitalize; }

        .status-tabs { display: flex; border-bottom: 1px solid #f0f0f0; padding: 0 18px; }
        .status-tab { padding: 10px 14px; font-size: 12.5px; color: #666; cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -1px; text-decoration: none; }
        .status-tab.active { color: #0f2557; font-weight: 700; border-bottom: 2px solid #0f2557; }

        .elem-table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
        .elem-table thead tr { background: #fafafa; border-bottom: 1px solid #eee; }
        .elem-table th { padding: 10px 16px; text-align: left; font-size: 11.5px; font-weight: 700; color: #666; }
        .elem-table td { padding: 12px 16px; border-bottom: 1px solid #f5f5f5; color: #333; vertical-align: middle; }
        .elem-table tbody tr:last-child td { border-bottom: none; }
        .elem-table td.empty-row { text-align: center; color: #999; padding: 32px 16px; }

        .field-type-tag { font-size: 10px; font-weight: 700; padding: 3px 10px; border-radius: 12px; background: #eef2ff; color: #0f2557; text-transform: capitalize; }
        .required-yes { color: #b91c1c; font-weight: 700; font-size: 11px; }
        .required-no { color: #999; font-size: 11px; }
        .elem-instructions { font-size: 11px; color: #999; margin-top: 2px; max-width: 320px; }

        .order-btns { display: flex; flex-direction: column; gap: 1px; }
        .order-btns button { background: none; border: none; cursor: pointer; color: #999; font-size: 10px; line-height: 1; padding: 1px; }
        .order-btns button:hover { color: #0f2557; }

        .action-btns { display: flex; align-items: center; gap: 4px; }
        .btn-icon { background: none; border: none; cursor: pointer; color: #888; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 6px; transition: color 0.15s, background 0.15s; }
        .btn-icon svg { width: 15px; height: 15px; }
        .btn-icon:hover { color: #0f2557; background: #f0f4ff; }

        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 999; align-items: center; justify-content: center; }
        .modal-overlay.open { display: flex; }
        .modal { background: #fff; border-radius: 10px; padding: 24px 26px; width: 460px; max-width: 95vw; box-shadow: 0 8px 32px rgba(0,0,0,0.25); }
        .modal-title { font-size: 15px; font-weight: 700; color: #1a1a2e; margin-bottom: 18px; }
        .modal-field { margin-bottom: 13px; }
        .modal-field label { display: block; font-size: 11.5px; font-weight: 700; color: #333; margin-bottom: 4px; }
        .modal-field input, .modal-field select, .modal-field textarea { width: 100%; padding: 8px 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 12.5px; color: #333; outline: none; font-family: Arial, sans-serif; transition: border-color 0.15s; }
        .modal-field input:focus, .modal-field select:focus, .modal-field textarea:focus { border-color: #0f2557; }
        .modal-field textarea { resize: vertical; min-height: 70px; }
        .modal-field select { appearance: none; -webkit-appearance: none; background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%23666' d='M5 7L0 2h10z'/%3E%3C/svg%3E") no-repeat right 10px center; cursor: pointer; }
        .modal-checkbox { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: #333; }
        .modal-checkbox input { width: 15px; height: 15px; accent-color: #0f2557; cursor: pointer; }
        .modal-error { display: none; background: #fee2e2; border: 1px solid #fca5a5; color: #b91c1c; font-size: 11px; padding: 8px 10px; border-radius: 4px; margin-bottom: 12px; }
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
            <li class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
                <a href="{{ url('/admin/dashboard') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                    Dashboard
                </a>
            </li>
            @endif
            @if($navPermissions['account-management'] ?? true)
            <li class="{{ request()->is('admin/account-management*') ? 'active' : '' }}">
                <a href="{{ url('/admin/account-management') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    Account Management
                </a>
            </li>
            @endif
            @if($navPermissions['roles-permissions'] ?? true)
            <li class="{{ request()->is('admin/roles-permissions*') ? 'active' : '' }}">
                <a href="{{ url('/admin/roles-permissions') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Roles & Permissions
                </a>
            </li>
            @endif
            @if($navPermissions['template-approvals'] ?? true)
            <li class="{{ request()->is('admin/template-approvals*') ? 'active' : '' }}">
                <a href="{{ url('/admin/template-approvals') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                    Templates
                </a>
            </li>
            @endif
            @if($navPermissions['cms'] ?? true)
            <li class="{{ request()->is('admin/cms*') ? 'active' : '' }}">
                <a href="{{ url('/admin/cms') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                    CMS
                </a>
            </li>
            @endif
            @if($navPermissions['program-assignment'] ?? true)
            <li class="{{ request()->is('admin/program-assignment*') ? 'active' : '' }}"><a href="{{ url('/admin/program-assignment') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>Program Assignment</a></li>
            @endif
            @if($navPermissions['audit-logs'] ?? true)
            <li class="{{ request()->is('admin/audit-logs*') ? 'active' : '' }}">
                <a href="{{ url('/admin/audit-logs') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    Audit Logs
                </a>
            </li>
            @endif
            @if($navPermissions['announcements'] ?? true)
            <li class="{{ request()->is('admin/announcements*') ? 'active' : '' }}">
                <a href="{{ url('/admin/announcements') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                    Announcements
                    @if(($unreadAnnouncementsCount ?? 0) > 0)
                        <span class="nav-badge">{{ $unreadAnnouncementsCount }}</span>
                    @endif
                </a>
            </li>
            @endif
            @if($navPermissions['calendar'] ?? true)
            <li class="{{ request()->is('admin/calendar*') ? 'active' : '' }}">
                <a href="{{ url('/admin/calendar') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Calendar of Activities
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

    <div class="main">
        <div class="topnav">
            <span class="label">Current role</span>
            <div class="topnav-right">
                <span class="role-badge">Admin/Dean</span>
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

            <div class="page-header">
                <div class="page-header-left">
                    <h1>Content Management System</h1>
                    <p>Define the sections faculty must fill in for each template type. Changes here apply system-wide immediately — no code changes needed when a template format changes.</p>
                </div>
                <button class="btn-primary" type="button" onclick="openCreateModal()">+ Add Element</button>
            </div>

            <div class="layout">

                {{-- Template type tabs --}}
                <div class="type-sidebar">
                    @foreach($types as $type)
                        <a class="type-tab {{ $activeType === $type ? 'active' : '' }}"
                           href="{{ url('/admin/cms?type=' . urlencode($type) . '&status=' . $status) }}">
                            {{ str_replace('_', ' ', $type) }}
                        </a>
                    @endforeach
                    <button class="btn-new-type" type="button" onclick="addNewType()">+ New Template Type</button>
                </div>

                {{-- Elements panel --}}
                <div class="elements-panel">
                    <div class="panel-toolbar">
                        <div class="panel-toolbar-title">{{ str_replace('_', ' ', $activeType) }} — Elements</div>
                    </div>

                    <div class="status-tabs">
                        <a class="status-tab {{ $status !== 'archived' ? 'active' : '' }}"
                           href="{{ url('/admin/cms?type=' . urlencode($activeType) . '&status=active') }}">Active</a>
                        <a class="status-tab {{ $status === 'archived' ? 'active' : '' }}"
                           href="{{ url('/admin/cms?type=' . urlencode($activeType) . '&status=archived') }}">Archived</a>
                    </div>

                    <table class="elem-table">
                        <thead>
                            <tr>
                                <th style="width:40px;">Order</th>
                                <th>Label</th>
                                <th>Field Type</th>
                                <th>Required</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($elements as $element)
                                <tr>
                                    <td>
                                        @if($status !== 'archived')
                                            <div class="order-btns">
                                                <form method="POST" action="{{ url('/admin/cms/' . $element->id . '/reorder') }}">
                                                    @csrf
                                                    <input type="hidden" name="direction" value="up">
                                                    <button type="submit" title="Move up">▲</button>
                                                </form>
                                                <form method="POST" action="{{ url('/admin/cms/' . $element->id . '/reorder') }}">
                                                    @csrf
                                                    <input type="hidden" name="direction" value="down">
                                                    <button type="submit" title="Move down">▼</button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $element->label }}</div>
                                        @if($element->instructions)
                                            <div class="elem-instructions">{{ $element->instructions }}</div>
                                        @endif
                                    </td>
                                    <td><span class="field-type-tag">{{ str_replace('_', ' ', $element->field_type) }}</span></td>
                                    <td>
                                        @if($element->is_required)
                                            <span class="required-yes">Required</span>
                                        @else
                                            <span class="required-no">Optional</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-btns">
                                            @if($status !== 'archived')
                                                <button type="button" class="btn-icon" title="Edit"
                                                    onclick="openEditModal(
                                                        '{{ $element->id }}',
                                                        '{{ addslashes($element->label) }}',
                                                        '{{ addslashes($element->instructions ?? '') }}',
                                                        '{{ $element->field_type }}',
                                                        {{ $element->is_required ? 'true' : 'false' }}
                                                    )">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                </button>
                                            @endif
                                            <form method="POST" action="{{ url('/admin/cms/' . $element->id . '/toggle-active') }}" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn-icon" title="{{ $status === 'archived' ? 'Restore' : 'Archive' }}">
                                                    @if($status === 'archived')
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                                                    @else
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
                                                    @endif
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="empty-row">
                                        {{ $status === 'archived' ? 'No archived elements.' : 'No elements defined yet for this template type. Click "+ Add Element" to get started.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    {{-- Create / Edit Modal --}}
    <div class="modal-overlay" id="modal-overlay">
        <div class="modal">
            <form id="elementForm" action="{{ url('/admin/cms') }}" method="POST">
                @csrf
                <div class="modal-title" id="modal-title">Add Template Element</div>
                <div class="modal-error" id="modal-error">{{ $errors->first() }}</div>

                <input type="hidden" name="template_type" id="f-template-type" value="{{ $activeType }}">

                <div class="modal-field">
                    <label>Label <span style="color:#ef4444">*</span></label>
                    <input type="text" id="f-label" name="label" placeholder="e.g. Course Description">
                </div>

                <div class="modal-field">
                    <label>Instructions / Guidance <span style="font-size:10px;color:#999;">(optional)</span></label>
                    <textarea id="f-instructions" name="instructions" placeholder="What should faculty write in this section?"></textarea>
                </div>

                <div class="modal-field">
                    <label>Field Type <span style="color:#ef4444">*</span></label>
                    <select id="f-field-type" name="field_type">
                        <option value="text">Text (short)</option>
                        <option value="rich_text">Rich Text (paragraph)</option>
                        <option value="table">Table</option>
                    </select>
                </div>

                <div class="modal-field">
                    <label class="modal-checkbox">
                        <input type="checkbox" id="f-required" name="is_required" value="1" checked>
                        Required section
                    </label>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-save">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCreateModal() {
            document.getElementById('modal-title').innerText = 'Add Template Element';
            document.getElementById('elementForm').action = '{{ url('/admin/cms') }}';

            var methodField = document.getElementById('edit-method');
            if (methodField) methodField.remove();

            document.getElementById('f-template-type').value = '{{ $activeType }}';
            document.getElementById('f-template-type').readOnly = false;
            document.getElementById('f-label').value = '';
            document.getElementById('f-instructions').value = '';
            document.getElementById('f-field-type').value = 'rich_text';
            document.getElementById('f-required').checked = true;
            document.getElementById('modal-error').style.display = 'none';
            document.getElementById('modal-overlay').classList.add('open');
        }

        function openEditModal(id, label, instructions, fieldType, isRequired) {
            document.getElementById('modal-title').innerText = 'Edit Template Element';
            document.getElementById('modal-error').style.display = 'none';

            document.getElementById('f-label').value = label;
            document.getElementById('f-instructions').value = instructions;
            document.getElementById('f-field-type').value = fieldType;
            document.getElementById('f-required').checked = isRequired;

            document.getElementById('elementForm').action = '{{ url('/admin/cms') }}/' + id;

            var methodField = document.getElementById('edit-method');
            if (!methodField) {
                methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PUT';
                methodField.id = 'edit-method';
                document.getElementById('elementForm').appendChild(methodField);
            }

            document.getElementById('modal-overlay').classList.add('open');
        }

        function closeModal() {
            document.getElementById('modal-overlay').classList.remove('open');
        }

        document.getElementById('modal-overlay').addEventListener('click', function (e) {
            if (e.target === this) closeModal();
        });

        function addNewType() {
            var name = prompt('Name of the new template type (e.g. "rubric", "course_outline"):');
            if (!name) return;
            var slug = name.trim().toLowerCase().replace(/\s+/g, '_');
            document.getElementById('f-template-type').value = slug;
            document.getElementById('f-template-type').readOnly = true;
            document.getElementById('modal-title').innerText = 'Add First Element for "' + slug.replace(/_/g, ' ') + '"';
            document.getElementById('elementForm').action = '{{ url('/admin/cms') }}';
            var methodField = document.getElementById('edit-method');
            if (methodField) methodField.remove();
            document.getElementById('f-label').value = '';
            document.getElementById('f-instructions').value = '';
            document.getElementById('f-field-type').value = 'rich_text';
            document.getElementById('f-required').checked = true;
            document.getElementById('modal-error').style.display = 'none';
            document.getElementById('modal-overlay').classList.add('open');
        }

        @if($errors->any())
            document.addEventListener('DOMContentLoaded', function () {
                document.getElementById('modal-overlay').classList.add('open');
                document.getElementById('modal-error').style.display = 'block';
            });
        @endif
    </script>

</body>
</html>
