<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Repository – CBMA System</title>
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

        .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 24px; }
        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 3px; }
        .page-sub { font-size: 11.5px; color: #888; }

        .btn-upload {
            display: flex; align-items: center; gap: 6px;
            background: #0f2557; color: #fff;
            border: none; border-radius: 6px;
            font-size: 12.5px; font-weight: 600;
            padding: 9px 18px; cursor: pointer;
            transition: background 0.15s;
        }
        .btn-upload:hover { background: #1a3a7a; }

        /* ── Two column layout ── */
        .repo-layout {
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 16px;
            align-items: start;
        }

        /* Left: Folder Structure */
        .folder-panel {
            background: #fff;
            border: 1px solid #e4e4e4;
            border-radius: 8px;
            padding: 16px;
        }

        .folder-panel-title {
            font-size: 13px; font-weight: 700; color: #1a1a2e;
            margin-bottom: 14px;
        }

        .folder-year {
            margin-bottom: 4px;
        }

        .folder-year-label {
            display: flex; align-items: center; gap: 6px;
            font-size: 12.5px; color: #333;
            padding: 5px 4px;
            cursor: pointer;
            border-radius: 4px;
            transition: background 0.12s;
            user-select: none;
        }

        .folder-year-label:hover { background: #f0f4ff; color: #0f2557; }
        .folder-year-label.active { color: #0f2557; font-weight: 700; }

        .folder-icon { font-size: 13px; }

        .semester-list { margin-left: 20px; margin-top: 2px; }

        .semester-item {
            display: flex; align-items: center; gap: 6px;
            font-size: 12px; color: #555;
            padding: 4px 6px;
            cursor: pointer;
            border-radius: 4px;
            transition: background 0.12s;
            user-select: none;
        }

        .semester-item:hover { background: #f0f4ff; color: #0f2557; }
        .semester-item.active { background: #e8eeff; color: #0f2557; font-weight: 700; }

        .arrow { font-size: 9px; color: #aaa; }

        /* Right: File table */
        .files-panel {
            background: #fff;
            border: 1px solid #e4e4e4;
            border-radius: 8px;
            overflow: hidden;
        }

        .files-header {
            display: flex; align-items: center; gap: 8px;
            padding: 12px 18px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 12.5px; color: #555;
        }

        .files-header .folder-crumb { color: #0f2557; font-weight: 600; }
        .files-header .sep { color: #aaa; }

        .files-table {
            width: 100%; border-collapse: collapse; font-size: 12.5px;
        }

        .files-table thead tr { background: #fafafa; border-bottom: 1px solid #eeeeee; }
        .files-table th { padding: 10px 18px; text-align: left; font-size: 11.5px; font-weight: 700; color: #555; }
        .files-table td { padding: 12px 18px; border-bottom: 1px solid #f5f5f5; color: #333; vertical-align: middle; }
        .files-table tbody tr:last-child td { border-bottom: none; }
        .files-table tbody tr:hover { background: #fafbff; }

        .file-name-cell { display: flex; align-items: center; gap: 7px; }
        .file-type-icon { font-size: 14px; }

        .btn-download {
            background: none; border: none;
            color: #0f2557; font-size: 16px;
            cursor: pointer; padding: 3px 6px;
            border-radius: 4px;
            transition: background 0.15s;
        }
        .btn-download:hover { background: #f0f4ff; }

        .table-empty { text-align: center; padding: 36px; color: #bbb; font-size: 12.5px; }

        /* ═══════════════════ UPLOAD MODAL ═══════════════════ */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 999; align-items: center; justify-content: center; }
        .modal-overlay.open { display: flex; }
        .modal { background: #fff; border-radius: 10px; padding: 24px 26px; width: 440px; max-width: 95vw; box-shadow: 0 8px 32px rgba(0,0,0,0.25); }
        .modal-title { font-size: 15px; font-weight: 700; color: #1a1a2e; margin-bottom: 18px; }
        .modal-field { margin-bottom: 13px; }
        .modal-field label { display: block; font-size: 11.5px; font-weight: 700; color: #333; margin-bottom: 4px; }
        .modal-field input, .modal-field select {
            width: 100%; height: 34px; padding: 0 10px;
            border: 1px solid #ccc; border-radius: 5px;
            font-size: 12.5px; color: #333; outline: none; background: #fff;
            transition: border-color 0.15s;
        }
        .modal-field input:focus, .modal-field select:focus { border-color: #0f2557; }
        .modal-field select { appearance: none; -webkit-appearance: none; background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%23666' d='M5 7L0 2h10z'/%3E%3C/svg%3E") no-repeat right 10px center; cursor: pointer; }
        .modal-field input.invalid { border-color: #ef4444; }
        .modal-error { display: none; background: #fee2e2; border: 1px solid #fca5a5; color: #b91c1c; font-size: 11px; padding: 6px 10px; border-radius: 4px; margin-bottom: 12px; }
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

    {{-- ════════════ SIDEBAR ════════════ --}}
    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="{{ asset('images/cbma-logo.png') }}" alt="CBMA Logo">
            <span class="brand">CBMA</span>
            <span class="brand-sub">Academic Coordination</span>
        </div>

        <ul class="nav-list">
            <li><a href="{{ url('/secretary/dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>Dashboard</a></li>
            <li class="active"><a href="{{ url('/secretary/document-repository') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>Document Repository</a></li>
            <li><a href="{{ url('/secretary/template-distribution') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>Template Distribution</a></li>
            <li><a href="{{ url('/secretary/course-filing') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>Course Filing</a></li>
            <li><a href="{{ url('/secretary/account-management') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>Account Management</a></li>
            <li><a href="{{ url('/secretary/announcements') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>Announcements</a></li>
        </ul>

        <div class="sidebar-logout">
            <a href="#" onclick="handleLogout()">
                <svg style="width:14px;height:14px;flex-shrink:0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Log out
            </a>
        </div>
    </aside>

    {{-- ════════════ MAIN ════════════ --}}
    <div class="main">

        <div class="topnav">
            <span class="label">Current role</span>
            <div class="topnav-right">
                <span class="role-badge">Secretary</span>
                <div class="user-info">
                    <div class="user-text">
                        <div class="user-name">—</div>
                        <div class="user-email">—</div>
                    </div>
                    <div class="user-avatar">—</div>
                </div>
            </div>
        </div>

        <div class="content">

            <div class="page-header">
                <div>
                    <div class="page-title">Document Repository</div>
                    <div class="page-sub">Centralized file management system</div>
                </div>
                <button class="btn-upload" type="button" onclick="openUploadModal()">
                    ⬆ Upload Document
                </button>
            </div>

            <div class="repo-layout">

                {{-- Left: Folder Structure --}}
                <div class="folder-panel">
                    <div class="folder-panel-title">Folder Structure</div>

                    <div class="folder-year" id="folder-2025-2026">
                        <div class="folder-year-label active" onclick="toggleYear('2025-2026', this)">
                            <span class="folder-icon">📁</span> 2025-2026
                        </div>
                        <div class="semester-list" id="sems-2025-2026">
                            <div class="semester-item" onclick="selectFolder('2025-2026','First Semester', this)">
                                <span class="arrow">▶</span>
                                <span class="folder-icon">📁</span> First Semester
                            </div>
                            <div class="semester-item active" onclick="selectFolder('2025-2026','Second Semester', this)">
                                <span class="arrow">▶</span>
                                <span class="folder-icon">📁</span> Second Semester
                            </div>
                        </div>
                    </div>

                    <div class="folder-year" id="folder-2024-2025">
                        <div class="folder-year-label" onclick="toggleYear('2024-2025', this)">
                            <span class="folder-icon">📁</span> 2024-2025
                        </div>
                        <div class="semester-list" id="sems-2024-2025" style="display:none;">
                            <div class="semester-item" onclick="selectFolder('2024-2025','First Semester', this)">
                                <span class="arrow">▶</span>
                                <span class="folder-icon">📁</span> First Semester
                            </div>
                            <div class="semester-item" onclick="selectFolder('2024-2025','Second Semester', this)">
                                <span class="arrow">▶</span>
                                <span class="folder-icon">📁</span> Second Semester
                            </div>
                        </div>
                    </div>

                    <div class="folder-year" id="folder-2023-2024">
                        <div class="folder-year-label" onclick="toggleYear('2023-2024', this)">
                            <span class="folder-icon">📁</span> 2023-2024
                        </div>
                        <div class="semester-list" id="sems-2023-2024" style="display:none;">
                            <div class="semester-item" onclick="selectFolder('2023-2024','First Semester', this)">
                                <span class="arrow">▶</span>
                                <span class="folder-icon">📁</span> First Semester
                            </div>
                            <div class="semester-item" onclick="selectFolder('2023-2024','Second Semester', this)">
                                <span class="arrow">▶</span>
                                <span class="folder-icon">📁</span> Second Semester
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Files table --}}
                <div class="files-panel">
                    <div class="files-header">
                        <span class="folder-icon">📁</span>
                        <span class="folder-crumb" id="crumb-year">2025-2026</span>
                        <span class="sep">▶</span>
                        <span class="folder-crumb" id="crumb-sem">Second Semester</span>
                    </div>
                    <table class="files-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Uploaded By</th>
                                <th>Date</th>
                                <th>Version</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="files-tbody">
                            <tr><td colspan="6" class="table-empty">No files in this folder.</td></tr>
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>

    {{-- ════════════ UPLOAD MODAL ════════════ --}}
    <div class="modal-overlay" id="modal-overlay">
        <div class="modal">
            <div class="modal-title">Upload Document</div>
            <div class="modal-error" id="modal-error"></div>

            <div class="modal-field">
                <label>File Name <span style="color:#ef4444;">*</span></label>
                <input type="text" id="f-name" placeholder="e.g. CS101_Syllabus.pdf">
            </div>

            <div class="modal-row">
                <div class="modal-field">
                    <label>Type <span style="color:#ef4444;">*</span></label>
                    <select id="f-type">
                        <option value="" disabled selected>Select type</option>
                        <option>PDF</option>
                        <option>Word</option>
                        <option>Excel</option>
                        <option>PowerPoint</option>
                        <option>Other</option>
                    </select>
                </div>
                <div class="modal-field">
                    <label>Version</label>
                    <input type="text" id="f-version" placeholder="e.g. v1.0">
                </div>
            </div>

            <div class="modal-field">
                <label>Uploaded By <span style="color:#ef4444;">*</span></label>
                <input type="text" id="f-by" placeholder="e.g. Dr. Sarah Johnson">
            </div>

            <div class="modal-row">
                <div class="modal-field">
                    <label>Academic Year</label>
                    <select id="f-year">
                        <option value="2025-2026">2025-2026</option>
                        <option value="2024-2025">2024-2025</option>
                        <option value="2023-2024">2023-2024</option>
                    </select>
                </div>
                <div class="modal-field">
                    <label>Semester</label>
                    <select id="f-sem">
                        <option value="First Semester">First Semester</option>
                        <option value="Second Semester" selected>Second Semester</option>
                    </select>
                </div>
            </div>

            <div class="modal-actions">
                <button class="btn-cancel" type="button" onclick="closeModal()">Cancel</button>
                <button class="btn-save" type="button" onclick="uploadDocument()">Upload</button>
            </div>
        </div>
    </div>

    <script>
        var currentYear = '2025-2026';
        var currentSem  = 'Second Semester';

        // In-memory file storage per folder
        var fileStore = {};

        var TYPE_ICONS = { PDF: '📄', Word: '📝', Excel: '📊', PowerPoint: '📊', Other: '📎' };

        function getKey(year, sem) { return year + '||' + sem; }

        function renderFiles() {
            var tbody = document.getElementById('files-tbody');
            var key   = getKey(currentYear, currentSem);
            var files = fileStore[key] || [];

            document.getElementById('crumb-year').textContent = currentYear;
            document.getElementById('crumb-sem').textContent  = currentSem;

            if (files.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="table-empty">No files in this folder.</td></tr>';
                return;
            }

            tbody.innerHTML = '';
            files.forEach(function(f, idx) {
                var tr = document.createElement('tr');
                tr.innerHTML =
                    '<td><div class="file-name-cell"><span class="file-type-icon">' + (TYPE_ICONS[f.type] || '📎') + '</span>' + f.name + '</div></td>' +
                    '<td>' + f.type + '</td>' +
                    '<td>' + f.by + '</td>' +
                    '<td>' + f.date + '</td>' +
                    '<td>' + (f.version || '—') + '</td>' +
                    '<td><button class="btn-download" title="Download" onclick="alert(\'Download: \' + \'' + f.name + '\')">⬇</button></td>';
                tbody.appendChild(tr);
            });
        }

        function toggleYear(year, el) {
            var sems = document.getElementById('sems-' + year);
            var isOpen = sems.style.display !== 'none';
            sems.style.display = isOpen ? 'none' : 'block';
        }

        function selectFolder(year, sem, el) {
            document.querySelectorAll('.semester-item').forEach(function(i) { i.classList.remove('active'); });
            el.classList.add('active');
            currentYear = year;
            currentSem  = sem;
            renderFiles();
        }

        function openUploadModal() {
            document.getElementById('f-name').value = '';
            document.getElementById('f-type').selectedIndex = 0;
            document.getElementById('f-version').value = '';
            document.getElementById('f-by').value = '';
            document.getElementById('f-year').value = currentYear;
            document.getElementById('f-sem').value  = currentSem;
            document.getElementById('f-name').classList.remove('invalid');
            document.getElementById('f-by').classList.remove('invalid');
            document.getElementById('modal-error').style.display = 'none';
            document.getElementById('modal-overlay').classList.add('open');
        }

        function closeModal() {
            document.getElementById('modal-overlay').classList.remove('open');
        }

        document.getElementById('modal-overlay').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        function uploadDocument() {
            var name    = document.getElementById('f-name').value.trim();
            var type    = document.getElementById('f-type').value;
            var version = document.getElementById('f-version').value.trim();
            var by      = document.getElementById('f-by').value.trim();
            var year    = document.getElementById('f-year').value;
            var sem     = document.getElementById('f-sem').value;
            var errDiv  = document.getElementById('modal-error');

            document.getElementById('f-name').classList.remove('invalid');
            document.getElementById('f-by').classList.remove('invalid');
            errDiv.style.display = 'none';

            if (!name || !type || !by) {
                errDiv.textContent = 'Please fill in all required fields.';
                errDiv.style.display = 'block';
                if (!name) document.getElementById('f-name').classList.add('invalid');
                if (!by)   document.getElementById('f-by').classList.add('invalid');
                return;
            }

            var today = new Date();
            var date  = today.getFullYear() + '-' +
                        String(today.getMonth() + 1).padStart(2,'0') + '-' +
                        String(today.getDate()).padStart(2,'0');

            var key = getKey(year, sem);
            if (!fileStore[key]) fileStore[key] = [];
            fileStore[key].push({ name: name, type: type, version: version || 'v1.0', by: by, date: date });

            // Switch to the folder the file was uploaded to
            currentYear = year;
            currentSem  = sem;
            closeModal();
            renderFiles();
        }

        function handleLogout() {
            window.location.href = '{{ url("/login") }}';
        }

        // Init
        renderFiles();
    </script>

</body>
</html>