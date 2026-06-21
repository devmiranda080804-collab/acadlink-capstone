<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Management – Secretary – CBMA System</title>
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

        .alert-success { background: #dcfce7; color: #166534; padding: 14px 16px; margin-bottom: 16px; border: 1px solid #bbf7d0; border-radius: 8px; font-size: 13px; line-height: 1.6; }
        .alert-success strong.title { display: block; font-size: 13.5px; margin-bottom: 4px; }

        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; }
        .btn-create { display: flex; align-items: center; gap: 6px; background: #0f2557; color: #fff; border: none; border-radius: 6px; font-size: 12.5px; font-weight: 600; padding: 9px 18px; cursor: pointer; transition: background 0.15s; }
        .btn-create:hover { background: #1a3a7a; }

        .acc-panel { background: #fff; border: 1px solid #e4e4e4; border-radius: 8px; overflow: hidden; }

        .role-tabs { display: flex; border-bottom: 1px solid #e4e4e4; padding: 0 16px; }
        .role-tab { padding: 12px 16px; font-size: 13px; color: #666; cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -1px; transition: color 0.15s; user-select: none; text-decoration: none; }
        .role-tab:hover { color: #0f2557; }
        .role-tab.active { color: #0f2557; font-weight: 700; border-bottom: 2px solid #0f2557; }

        .filters-row { display: flex; align-items: center; gap: 10px; padding: 14px 16px; border-bottom: 1px solid #f0f0f0; flex-wrap: wrap; }
        .filters-row form { margin: 0; }
        .search-wrap { flex: 1; min-width: 220px; position: relative; }
        .search-wrap svg { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: #aaa; pointer-events: none; }
        .search-wrap input { width: 100%; height: 34px; padding: 0 10px 0 32px; border: 1px solid #ccc; border-radius: 5px; font-size: 12px; color: #333; outline: none; background: #fff; transition: border-color 0.15s; }
        .search-wrap input:focus { border-color: #0f2557; }
        .filter-select { height: 34px; padding: 0 28px 0 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 12px; color: #333; background: #fff; outline: none; appearance: none; -webkit-appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%23666' d='M5 7L0 2h10z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 10px center; cursor: pointer; min-width: 130px; }

        .acc-table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
        .acc-table thead tr { background: #fafafa; border-bottom: 1px solid #eeeeee; }
        .acc-table th { padding: 10px 16px; text-align: left; font-size: 11.5px; font-weight: 700; color: #666; }
        .acc-table td { padding: 12px 16px; border-bottom: 1px solid #f5f5f5; color: #333; vertical-align: middle; }
        .acc-table tbody tr:last-child td { border-bottom: none; }
        .acc-table tbody tr:hover { background: #fafbff; }
        .acc-table td.empty-row { text-align: center; color: #999; padding: 28px 16px; }

        .action-btns { display: flex; align-items: center; gap: 4px; }
        .btn-icon { background: none; border: none; cursor: pointer; color: #888; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 6px; transition: color 0.15s, background 0.15s; }
        .btn-icon svg { width: 15px; height: 15px; }
        .btn-icon:hover { color: #0f2557; background: #f0f4ff; }
        .btn-icon.del:hover { color: #ef4444; background: #fee2e2; }

        .pagination { display: flex; align-items: center; justify-content: flex-end; gap: 4px; padding: 14px 16px; border-top: 1px solid #f0f0f0; }
        .page-btn { height: 30px; min-width: 30px; padding: 0 10px; border: 1px solid #ddd; border-radius: 4px; background: #fff; color: #444; font-size: 12px; cursor: pointer; transition: background 0.15s; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        .page-btn:hover { background: #f5f5f5; }
        .page-btn.active { background: #0f2557; color: #fff; border-color: #0f2557; font-weight: 700; }
        .page-btn:disabled { opacity: 0.4; cursor: not-allowed; }

        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 999; align-items: center; justify-content: center; }
        .modal-overlay.open { display: flex; }
        .modal { background: #fff; border-radius: 10px; padding: 24px 26px; width: 460px; max-width: 95vw; box-shadow: 0 8px 32px rgba(0,0,0,0.25); }
        .modal-title { font-size: 15px; font-weight: 700; color: #1a1a2e; margin-bottom: 18px; }
        .modal-field { margin-bottom: 13px; }
        .modal-field label { display: block; font-size: 11.5px; font-weight: 700; color: #333; margin-bottom: 4px; }
        .modal-field input, .modal-field select { width: 100%; height: 34px; padding: 0 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 12.5px; color: #333; outline: none; background: #fff; transition: border-color 0.15s; }
        .modal-field input:focus, .modal-field select:focus { border-color: #0f2557; }
        .modal-field input:disabled { background: #f3f4f6; color: #777; }
        .modal-field select { appearance: none; -webkit-appearance: none; background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%23666' d='M5 7L0 2h10z'/%3E%3C/svg%3E") no-repeat right 10px center; cursor: pointer; }
        .modal-error { display: none; background: #fee2e2; border: 1px solid #fca5a5; color: #b91c1c; font-size: 11px; padding: 8px 10px; border-radius: 4px; margin-bottom: 12px; }
        .modal-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .modal-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 18px; }
        .btn-cancel { background: #fff; border: 1px solid #ccc; color: #444; font-size: 12.5px; font-weight: 600; padding: 8px 18px; border-radius: 5px; cursor: pointer; transition: background 0.15s; }
        .btn-cancel:hover { background: #f5f5f5; }
        .btn-save { background: #0f2557; color: #fff; border: none; font-size: 12.5px; font-weight: 600; padding: 8px 20px; border-radius: 5px; cursor: pointer; transition: background 0.15s; }
        .btn-save:hover { background: #1a3a7a; }
        .btn-danger { background: #ef4444; }
        .btn-danger:hover { background: #dc2626; }
        .delete-modal p { font-size: 13px; color: #444; line-height: 1.5; margin-bottom: 4px; }

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
            <li><a href="{{ url('/secretary/dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>Dashboard</a></li>
            <li><a href="{{ url('/secretary/document-repository') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>Document Repository</a></li>
            <li><a href="{{ url('/secretary/template-distribution') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/></svg>Template Distribution</a></li>
            <li><a href="{{ url('/secretary/course-filing') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>Course Filing</a></li>
            <li class="active"><a href="{{ url('/secretary/account-management') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>Account Management</a></li>
            <li><a href="{{ url('/secretary/announcements') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>Announcements</a></li>
        </ul>

        <div class="sidebar-logout">
            <a href="#" onclick="handleLogout()">
                <svg style="width:14px;height:14px;flex-shrink:0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Log out
            </a>
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
                <div class="alert-success">
                    <strong class="title">{{ session('success') }}</strong>
                    @if(session('employee_id'))
                        Employee ID: <strong>{{ session('employee_id') }}</strong><br>
                        Temporary Password: <strong>{{ session('temp_password') }}</strong>
                    @endif
                </div>
            @endif

            <div class="page-header">
                <div class="page-title">Account Management</div>
                <button class="btn-create" type="button" onclick="openCreateModal()">+ Create Account</button>
            </div>

            <div class="acc-panel">

                {{-- Program tabs (secretary can manage faculty across all programs) --}}
                <div class="role-tabs">
                    <a class="role-tab {{ request('program') == null ? 'active' : '' }}"
                       href="{{ url('/secretary/account-management') }}">ALL</a>
                    <a class="role-tab {{ request('program') == 'FMAD' ? 'active' : '' }}"
                       href="{{ url('/secretary/account-management?program=FMAD') }}">FMAD</a>
                    <a class="role-tab {{ request('program') == 'OFD' ? 'active' : '' }}"
                       href="{{ url('/secretary/account-management?program=OFD') }}">OFD</a>
                    <a class="role-tab {{ request('program') == 'BAD' ? 'active' : '' }}"
                       href="{{ url('/secretary/account-management?program=BAD') }}">BAD</a>
                </div>

                <div class="filters-row">
                    <div class="search-wrap">
                        <form method="GET">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            <input type="hidden" name="program" value="{{ request('program') }}">
                            <input type="hidden" name="academic_year" value="{{ request('academic_year') }}">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Search by name or employee id" onkeyup="this.form.submit()">
                        </form>
                    </div>

                    <form method="GET">
                        <input type="hidden" name="program" value="{{ request('program') }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <select name="academic_year" class="filter-select" onchange="this.form.submit()">
                            <option value="">All Year</option>
                            <option value="2025-2026" {{ request('academic_year') == '2025-2026' ? 'selected' : '' }}>2025-2026</option>
                            <option value="2024-2025" {{ request('academic_year') == '2024-2025' ? 'selected' : '' }}>2024-2025</option>
                        </select>
                    </form>
                </div>

                <table class="acc-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Employee ID</th>
                            <th>Program</th>
                            <th>Email</th>
                            <th>Academic Year</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $account)
                            <tr>
                                <td>{{ $account->name }}</td>
                                <td>{{ $account->employee_id }}</td>
                                <td>{{ $account->program }}</td>
                                <td>{{ $account->email }}</td>
                                <td>{{ $account->academic_year }}</td>
                                <td>
                                    <div class="action-btns">
                                        <button type="button" class="btn-icon" title="Edit"
                                            onclick="openEditModal(
                                                '{{ $account->id }}',
                                                '{{ $account->name }}',
                                                '{{ $account->email }}',
                                                '{{ $account->program }}',
                                                '{{ $account->academic_year }}'
                                            )">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </button>
                                        <button type="button" class="btn-icon del" title="Delete"
                                            onclick="openDeleteModal('{{ $account->id }}', '{{ $account->name }}')">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-row">No faculty accounts found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="pagination">
                    @if ($accounts->onFirstPage())
                        <button class="page-btn" disabled>Previous</button>
                    @else
                        <a href="{{ $accounts->previousPageUrl() }}" class="page-btn">Previous</a>
                    @endif

                    @for ($i = 1; $i <= $accounts->lastPage(); $i++)
                        <a href="{{ $accounts->url($i) }}" class="page-btn {{ $accounts->currentPage() == $i ? 'active' : '' }}">{{ $i }}</a>
                    @endfor

                    @if ($accounts->hasMorePages())
                        <a href="{{ $accounts->nextPageUrl() }}" class="page-btn">Next</a>
                    @else
                        <button class="page-btn" disabled>Next</button>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <div class="modal-overlay" id="modal-overlay">
        <div class="modal">
            <form id="accountForm" action="{{ url('/secretary/account-management') }}" method="POST">
                @csrf
                <div class="modal-title" id="modal-title">Create Account</div>
                <div class="modal-error" id="modal-error">{{ $errors->first() }}</div>

                <div class="modal-row">
                    <div class="modal-field">
                        <label>First Name <span style="color:#ef4444">*</span></label>
                        <input type="text" id="f-firstname" name="first_name" placeholder="First name">
                    </div>
                    <div class="modal-field">
                        <label>Last Name <span style="color:#ef4444">*</span></label>
                        <input type="text" id="f-lastname" name="last_name" placeholder="Last name">
                    </div>
                </div>

                <div class="modal-row">
                    <div class="modal-field">
                        <label>Role</label>
                        <input type="text" value="Faculty" disabled>
                    </div>
                    <div class="modal-field">
                        <label>Program <span style="color:#ef4444">*</span></label>
                        <select id="f-program" name="program">
                            <option value="" disabled selected>Select program</option>
                            <option value="FMAD">FMAD</option>
                            <option value="OFD">OFD</option>
                            <option value="BAD">BAD</option>
                        </select>
                    </div>
                </div>

                <div class="modal-field">
                    <label>Academic Year <span style="color:#ef4444">*</span></label>
                    <select id="f-year" name="academic_year">
                        <option value="2025-2026">2025-2026</option>
                        <option value="2024-2025">2024-2025</option>
                    </select>
                </div>

                <div class="modal-field">
                    <label>Email <span style="color:#ef4444">*</span></label>
                    <input type="email" id="f-email" name="email" placeholder="email@cbma.edu">
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-save">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="delete-overlay">
        <div class="modal delete-modal" style="width:380px;">
            <div class="modal-title">Delete Account</div>
            <p>Are you sure you want to delete <strong id="delete-name"></strong>?</p>
            <p style="color:#888;">This action cannot be undone.</p>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
                <button type="button" class="btn-save btn-danger" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>

    <form id="deleteForm" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function openCreateModal() {
            document.getElementById('modal-title').innerText = 'Create Account';
            document.getElementById('accountForm').action = '{{ url('/secretary/account-management') }}';

            var methodField = document.getElementById('edit-method');
            if (methodField) methodField.remove();

            clearForm();
            document.getElementById('modal-overlay').classList.add('open');
        }

        function clearForm() {
            ['f-firstname', 'f-lastname', 'f-email'].forEach(function (id) {
                document.getElementById(id).value = '';
            });
            document.getElementById('f-program').selectedIndex = 0;
            document.getElementById('f-year').selectedIndex = 0;
            document.getElementById('modal-error').style.display = 'none';
        }

        function closeModal() {
            document.getElementById('modal-overlay').classList.remove('open');
        }

        document.getElementById('modal-overlay').addEventListener('click', function (e) {
            if (e.target === this) closeModal();
        });

        function openEditModal(id, name, email, program, year) {
            document.getElementById('modal-overlay').classList.add('open');
            document.getElementById('modal-title').innerText = 'Edit Account';
            document.getElementById('modal-error').style.display = 'none';

            document.getElementById('f-email').value = email;
            document.getElementById('f-program').value = program;
            document.getElementById('f-year').value = year;

            var parts = name.split(' ');
            document.getElementById('f-firstname').value = parts[0];
            document.getElementById('f-lastname').value = parts.slice(1).join(' ');

            document.getElementById('accountForm').action = '{{ url('/secretary/account-management') }}/' + id;

            var methodField = document.getElementById('edit-method');
            if (!methodField) {
                methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PUT';
                methodField.id = 'edit-method';
                document.getElementById('accountForm').appendChild(methodField);
            }
        }

        function openDeleteModal(id, name) {
            document.getElementById('delete-name').innerText = name;
            document.getElementById('deleteForm').action = '{{ url('/secretary/account-management') }}/' + id;
            document.getElementById('delete-overlay').classList.add('open');
        }

        function closeDeleteModal() {
            document.getElementById('delete-overlay').classList.remove('open');
        }

        function confirmDelete() {
            document.getElementById('deleteForm').submit();
        }

        document.getElementById('delete-overlay').addEventListener('click', function (e) {
            if (e.target === this) closeDeleteModal();
        });

        function handleLogout() {
            window.location.href = '{{ url("/login") }}';
        }

        @if($errors->any())
            document.addEventListener('DOMContentLoaded', function () {
                document.getElementById('modal-overlay').classList.add('open');
                document.getElementById('modal-error').style.display = 'block';
            });
        @endif

        @if(session('success'))
            document.addEventListener('DOMContentLoaded', function () {
                document.getElementById('modal-overlay').classList.remove('open');
                document.getElementById('delete-overlay').classList.remove('open');
            });
        @endif
    </script>

</body>
</html>