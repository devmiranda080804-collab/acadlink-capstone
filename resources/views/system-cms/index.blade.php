<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>System CMS – CBMA System</title>
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
        .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 24px; }
        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; }
        .page-sub { font-size: 11.5px; color: #888; margin-top: 3px; }
        .alert-error { background: #fee2e2; color: #b91c1c; padding: 12px 16px; margin-bottom: 16px; border: 1px solid #fca5a5; border-radius: 8px; font-size: 13px; }
        
        .btn-save { background: #0f2557; color: #fff; border: none; font-size: 13px; font-weight: 600; padding: 9px 22px; border-radius: 6px; cursor: pointer; }
        .btn-save:hover { background: #1a3a7a; }
        .cms-tabs { display: flex; gap: 6px; border-bottom: 1px solid #e0e0e0; margin-bottom: 20px; }
        .cms-tab { background: none; border: none; border-bottom: 3px solid transparent; padding: 10px 16px; font-size: 13px; font-weight: 600; color: #777; cursor: pointer; font-family: Arial, sans-serif; }
        .cms-tab.active { color: #0f2557; border-bottom-color: #0f2557; }
        .cms-panel { display: none; }
        .cms-panel.active { display: block; }
        .btn-create { background: #0f2557; color: #fff; border: none; border-radius: 6px; font-size: 12.5px; font-weight: 600; padding: 9px 18px; cursor: pointer; }
        .btn-create:hover { background: #1a3a7a; }
        .doc-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px; }
        .doc-card { background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; padding: 16px; }
        .doc-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
        .doc-icon { font-size: 22px; }
        .doc-top-right { display: flex; align-items: center; gap: 6px; }
        .doc-badge { font-size: 10px; font-weight: 700; padding: 3px 9px; border-radius: 10px; background: #f0f0f0; color: #666; }
        .doc-badge.published { background: #d1fae5; color: #065f46; }
        .doc-title { font-size: 14px; font-weight: 700; color: #1a1a2e; margin-bottom: 4px; overflow-wrap: anywhere; }
        .doc-desc { font-size: 11.5px; color: #666; margin-bottom: 8px; line-height: 1.5; }
        .doc-meta { font-size: 10.5px; color: #999; margin-bottom: 14px; line-height: 1.5; }
        .doc-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; }
        .doc-actions form { margin: 0; }
        .btn-doc { display: inline-block; background: #fff; border: 1px solid #d0d0d0; color: #333; font-size: 11.5px; font-weight: 600; padding: 6px 11px; border-radius: 5px; cursor: pointer; text-decoration: none; font-family: Arial, sans-serif; }
        .btn-doc:hover { background: #f5f5f5; }
        .btn-doc.primary { background: #0f2557; border-color: #0f2557; color: #fff; }
        .btn-doc.primary:hover { background: #1a3a7a; }
        .empty-state { grid-column: 1 / -1; background: #fff; border: 1px solid #e4e4e4; border-radius: 8px; padding: 50px 20px; text-align: center; color: #999; font-size: 12.5px; }
        .site-card { background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; padding: 22px 24px; max-width: 760px; margin-bottom: 18px; }
        .site-field { margin-bottom: 14px; }
        .site-field label { display: block; font-size: 12px; font-weight: 700; color: #333; margin-bottom: 5px; }
        .site-field input[type=text], .site-field textarea, .modal-box input[type=text], .modal-box textarea, .modal-box select { width: 100%; padding: 9px 11px; border: 1px solid #ccc; border-radius: 6px; font-size: 13px; font-family: Arial, sans-serif; outline: none; background: #fff; }
        .site-field input[type=text]:focus, .site-field textarea:focus, .modal-box input[type=text]:focus, .modal-box textarea:focus { border-color: #0f2557; }
        .site-hint { font-size: 11px; color: #999; margin-top: 4px; }
        .image-row { display: flex; gap: 16px; align-items: center; padding: 12px 0; border-top: 1px solid #f0f0f0; }
        .image-preview { width: 110px; height: 70px; flex-shrink: 0; background: #f4f5f8 center/contain no-repeat; border: 1px solid #e4e4e4; border-radius: 6px; }
        .image-info { flex: 1; min-width: 0; }
        .image-name { font-size: 12.5px; font-weight: 700; color: #333; margin-bottom: 2px; }
        .image-reset { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; color: #666; margin-top: 5px; }
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 999; align-items: center; justify-content: center; }
        .modal-overlay.open { display: flex; }
        .modal-box { background: #fff; border-radius: 10px; padding: 24px 26px; width: 450px; max-width: 95vw; }
        .modal-heading { font-size: 15px; font-weight: 700; color: #1a1a2e; margin-bottom: 14px; }
        .modal-field { margin-bottom: 13px; }
        .modal-field label { display: block; font-size: 11.5px; font-weight: 700; color: #333; margin-bottom: 4px; }
        .modal-buttons { display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px; }
        .mode-choice { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 14px; }
        .mode-choice label { display: block; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 12.5px; font-weight: 600; color: #444; text-align: center; cursor: pointer; }
        .mode-choice input { display: none; }
        .mode-choice label.selected { border-color: #0f2557; background: #eef2ff; color: #0f2557; }
    </style>
</head>
<body>

    @include('system-cms.partials.sidebar-' . ($isAdmin ? 'admin' : 'secretary'))

    <div class="main">
        <div class="topnav">
            <span class="label">Current role</span>
            <div class="topnav-right">
                <span class="role-badge">{{ $isAdmin ? 'Admin/Dean' : 'Secretary' }}</span>
                @include('partials.notification-bell')
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
            @if($errors->any())
                <div class="alert-error">{{ $errors->first() }}</div>
            @endif

            <div class="page-header">
                <div>
                    <div class="page-title">System CMS</div>
                    <div class="page-sub">Template elements, document layouts, dropdown lists and site content of the whole system.</div>
                </div>
            </div>

            @if($isAdmin)
                <div class="cms-tabs">
                    <button type="button" class="cms-tab active" data-panel="panel-elements" onclick="showPanel(this)">Template Elements</button>
                    <button type="button" class="cms-tab" data-panel="panel-layouts" onclick="showPanel(this)">Document Layouts</button>
                    <button type="button" class="cms-tab" data-panel="panel-lists" onclick="showPanel(this)">Dropdown Lists</button>
                    <button type="button" class="cms-tab" data-panel="panel-site" onclick="showPanel(this)">Site Content</button>
                </div>
            @endif

            <div class="cms-panel active" id="panel-elements">
                @include('system-cms.partials.template-elements')
            </div>

            @if($isAdmin)
                <div class="cms-panel" id="panel-layouts">
                    @include('system-cms.partials.document-layouts')
                </div>

                <div class="cms-panel" id="panel-lists">
                    @include('system-cms.partials.dropdown-lists')
                </div>

                <div class="cms-panel" id="panel-site">
                    <form method="POST" action="{{ url('/admin/system-cms/site') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="site-card">
                            <div class="page-title" style="font-size:14px;margin-bottom:14px;">Wording</div>
                            @foreach(\App\Support\SiteContent::TEXTS as $key => $text)
                                <div class="site-field">
                                    <label for="site-{{ $key }}">{{ $text['label'] }}</label>
                                    @if($text['multiline'] ?? false)
                                        <textarea id="site-{{ $key }}" name="{{ $key }}" rows="3" maxlength="500" required>{{ old($key, \App\Support\SiteContent::text($key)) }}</textarea>
                                    @else
                                        <input type="text" id="site-{{ $key }}" name="{{ $key }}" maxlength="150" value="{{ old($key, \App\Support\SiteContent::text($key)) }}" required>
                                    @endif
                                    <div class="site-hint">{{ $text['hint'] }}</div>
                                </div>
                            @endforeach
                        </div>

                        <div class="site-card">
                            <div class="page-title" style="font-size:14px;margin-bottom:6px;">Images</div>
                            <div class="site-hint" style="margin-bottom:10px;">JPG or PNG, max 5MB. Use a square-ish image for the logos so they aren't stretched.</div>
                            @foreach(\App\Support\SiteContent::IMAGES as $key => $image)
                                <div class="image-row">
                                    <div class="image-preview" style="background-image:url('{{ \App\Support\SiteContent::imageUrl($key) }}')"></div>
                                    <div class="image-info">
                                        <div class="image-name">{{ $image['label'] }}</div>
                                        <div class="site-hint" style="margin:0 0 6px;">{{ $image['hint'] }}</div>
                                        <input type="file" name="image_{{ $key }}" accept="{{ ($image['png_only'] ?? false) ? '.png' : '.jpg,.jpeg,.png' }}">
                                        @if(\App\Support\SiteContent::uploadedImage($key))
                                            <div><label class="image-reset"><input type="checkbox" name="reset_{{ $key }}" value="1"> Go back to the original image</label></div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="submit" class="btn-save">Save Site Content</button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <script>
        function openModal(id) { document.getElementById(id).classList.add('open'); }
        function closeModal(id) { document.getElementById(id).classList.remove('open'); }
        document.querySelectorAll('.modal-overlay').forEach(function (overlay) {
            overlay.addEventListener('click', function (e) { if (e.target === overlay) overlay.classList.remove('open'); });
        });

        function showPanel(tab) {
            document.querySelectorAll('.cms-tab').forEach(function (t) { t.classList.toggle('active', t === tab); });
            document.querySelectorAll('.cms-panel').forEach(function (p) { p.classList.toggle('active', p.id === tab.dataset.panel); });
        }

        @if($isAdmin && session('site_saved'))
            showPanel(document.querySelector('.cms-tab[data-panel="panel-site"]'));
        @endif
        @if($isAdmin && (session('cms_panel') === 'panel-layouts' || collect($errors->keys())->contains(fn ($k) => str_contains($k, '__'))))
            showPanel(document.querySelector('.cms-tab[data-panel="panel-layouts"]'));
        @endif
        @if($isAdmin && (session('cms_panel') === 'panel-lists' || $errors->hasAny(['list', 'key', 'label', 'is_active', 'cms_list'])))
            showPanel(document.querySelector('.cms-tab[data-panel="panel-lists"]'));
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
