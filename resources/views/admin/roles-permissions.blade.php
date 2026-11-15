<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles & Permissions – CBMA System</title>
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

        .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 24px; }
        .page-header-left h1 { font-size: 20px; font-weight: 700; color: #1a1a2e; }
        .page-header-left p { font-size: 12.5px; color: #666; margin-top: 3px; }

        .btn-save { display: flex; align-items: center; gap: 6px; background: #0f2557; color: #fff; border: none; border-radius: 6px; font-size: 12.5px; font-weight: 600; padding: 9px 18px; cursor: pointer; transition: background 0.15s; }
        .btn-save:hover { background: #1a3a7a; }

        .layout { display: flex; gap: 20px; }

        /* Role tabs (vertical) */
        .role-sidebar { width: 180px; flex-shrink: 0; }
        .role-tab { display: block; padding: 10px 14px; font-size: 13px; color: #444; text-decoration: none; border-radius: 6px; margin-bottom: 4px; transition: background 0.15s; }
        .role-tab:hover { background: #e8ecf8; color: #0f2557; }
        .role-tab.active { background: #0f2557; color: #fff; font-weight: 600; }

        /* Permissions table */
        .perm-panel { flex: 1; background: #fff; border: 1px solid #e4e4e4; border-radius: 8px; overflow: hidden; }
        .perm-table { width: 100%; border-collapse: collapse; }
        .perm-table thead tr { background: #fafafa; border-bottom: 1px solid #eee; }
        .perm-table th { padding: 12px 20px; text-align: left; font-size: 12px; font-weight: 700; color: #666; }
        .perm-table td { padding: 14px 20px; border-bottom: 1px solid #f5f5f5; font-size: 13px; color: #333; }
        .perm-table tbody tr:last-child td { border-bottom: none; }
        .perm-table tbody tr:hover { background: #fafbff; }

        /* Custom checkbox */
        .perm-check { width: 18px; height: 18px; accent-color: #1d4ed8; cursor: pointer; }

        svg { display: inline-block; vertical-align: middle; }

        .perm-check:disabled { opacity: 0.6; cursor: not-allowed; }
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
            @php
                $rpRoles = ['program_head' => 'Program Head', 'secretary' => 'Secretary', 'faculty' => 'Faculty'];
                $rpRoleIcons = ['program_head' => 'userCheck', 'secretary' => 'archive', 'faculty' => 'cap'];
                // How many modules each role can open, for the menu.
                $rpCounts = [];
                foreach ($rpRoles as $roleKey => $roleLabel) {
                    $saved = \Illuminate\Support\Facades\DB::table('role_permissions')->where('role', $roleKey)->pluck('is_enabled', 'module');
                    $mods = array_keys($roleModules[$roleKey] ?? []);
                    $rpCounts[$roleKey] = [count(array_filter($mods, fn ($m) => $m === 'dashboard' || ($saved[$m] ?? true))), count($mods)];
                }
                // Each module's icon, the same as in that role's sidebar.
                $rpIcons = [];
                foreach (\App\Support\SidebarMenu::MENUS[$selectedRole] ?? [] as $items) {
                    foreach ($items as $item) {
                        if ($item[0]) { $rpIcons[$item[0]] = $item[4]; }
                    }
                }
                $rpOn = count(array_filter($permissions, fn ($v, $k) => $k === 'dashboard' || $v, ARRAY_FILTER_USE_BOTH));
                $rpTotal = count($modules);
                $rpLabel = $rpRoles[$selectedRole] ?? ucwords(str_replace('_', ' ', $selectedRole));
            @endphp
            <style>
                .rp { font-family: 'Plus Jakarta Sans', Arial, sans-serif; color: #14213d; }
                .rp .rp-ic { width: 19px; height: 19px; flex-shrink: 0; }
                .rp-eyebrow { font-size: 12px; font-weight: 800; letter-spacing: 2px; color: #1b3d7a; text-transform: uppercase; }
                .rp-title { font-size: 28px; font-weight: 800; letter-spacing: -.4px; margin: 4px 0 6px; }
                .rp-sub { font-size: 14px; color: #475467; }
                .rp-layout { display: grid; grid-template-columns: 250px minmax(0, 1fr); gap: 24px; margin-top: 22px; align-items: start; }
                .rp-nav { display: flex; flex-direction: column; gap: 4px; position: sticky; top: 0; }
                .rp-tab { display: flex; align-items: center; gap: 12px; padding: 12px 14px; border: 1px solid transparent; border-radius: 12px; color: #344054; text-decoration: none;
                    transition: background-color .18s ease, border-color .18s ease, box-shadow .18s ease; }
                .rp-tab:hover { background: #fff; border-color: #e7eaf1; }
                .rp-tab.is-active { background: #fff; border-color: #e7eaf1; color: #0f2557; box-shadow: 0 4px 14px rgba(15, 37, 87, .07); }
                .rp-tab strong { display: block; font-size: 14.5px; font-weight: 700; }
                .rp-tab small { display: block; font-size: 12px; color: #667085; margin-top: 2px; }
                .rp-card { background: #fff; border: 1px solid #e7eaf1; border-radius: 16px; padding: 20px 22px; box-shadow: 0 1px 2px rgba(15, 37, 87, .04), 0 6px 18px rgba(15, 37, 87, .05); margin-bottom: 16px; }
                .rp-status { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
                .rp-status-icon { width: 46px; height: 46px; border-radius: 12px; background: #eef2fa; color: #0f2557; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
                .rp-status-icon .rp-ic { width: 23px; height: 23px; }
                .rp-status-title { font-size: 17px; font-weight: 800; }
                .rp-status-sub { font-size: 13px; color: #667085; margin-top: 2px; }
                .rp-meter { width: 100%; height: 8px; border-radius: 8px; background: #eef2fa; overflow: hidden; margin-top: 14px; }
                .rp-meter span { display: block; height: 100%; background: #0f2557; border-radius: 8px; transition: width .3s ease; }
                .rp-card-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 6px; }
                .rp-card-title { font-size: 16px; font-weight: 800; }
                .rp-card-sub { font-size: 13px; color: #667085; margin-bottom: 10px; }
                .rp-row { display: flex; align-items: center; gap: 14px; padding: 13px 4px; border-top: 1px solid #f1f3f8; }
                .rp-row:first-of-type { border-top: 0; }
                .rp-row-icon { width: 38px; height: 38px; border-radius: 10px; background: #f4f6fb; color: #0f2557; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
                .rp-row-main { flex: 1; min-width: 0; }
                .rp-row-name, .rp-row-state { display: block; }
    .rp-row-name { font-size: 14px; font-weight: 700; }
                .rp-row-state { font-size: 12px; color: #667085; margin-top: 2px; }
                .rp-row-state.off { color: #b45309; }
                /* The checkbox, shown as a switch. */
                .rp .perm-check { appearance: none; -webkit-appearance: none; width: 44px; height: 24px; border-radius: 12px; background: #d0d5dd; position: relative; cursor: pointer;
                    flex-shrink: 0; transition: background-color .2s ease; margin: 0; }
                .rp .perm-check::after { content: ""; position: absolute; top: 3px; left: 3px; width: 18px; height: 18px; border-radius: 50%; background: #fff;
                    box-shadow: 0 1px 3px rgba(0, 0, 0, .25); transition: transform .2s ease; }
                .rp .perm-check:checked { background: #0f2557; }
                .rp .perm-check:checked::after { transform: translateX(20px); }
                .rp .perm-check:disabled { opacity: .55; cursor: not-allowed; }
                .rp .perm-check:focus-visible { outline: 2px solid #2563eb; outline-offset: 2px; }
                .rp-actions { display: flex; justify-content: flex-end; padding-top: 14px; border-top: 1px solid #f1f3f8; margin-top: 4px; }
                .rp-actions .btn-save { padding: 10px 22px; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; }
                @media (max-width: 900px) { .rp-layout { grid-template-columns: 1fr; } .rp-nav { position: static; flex-direction: row; flex-wrap: wrap; } }
            </style>

            <div class="rp">
                @if(session('success'))
                    <div class="alert-success">{{ session('success') }}</div>
                @endif

                <div class="rp-eyebrow">Access control</div>
                <div class="rp-title">Roles &amp; Permissions</div>
                <div class="rp-sub">Choose which pages each role can open. A page that is switched off disappears from that role's sidebar.</div>

                <div class="rp-layout">
                    <nav class="rp-nav" aria-label="Roles">
                        @foreach($rpRoles as $roleKey => $roleLabel)
                            <a href="{{ url('/admin/roles-permissions/' . $roleKey) }}" class="rp-tab {{ $selectedRole === $roleKey ? 'is-active' : '' }}" @if($selectedRole === $roleKey) aria-current="page" @endif>
                                {!! \App\Support\SidebarMenu::icon($rpRoleIcons[$roleKey], 'rp-ic') !!}
                                <span><strong>{{ $roleLabel }}</strong><small>{{ $rpCounts[$roleKey][0] }} of {{ $rpCounts[$roleKey][1] }} pages on</small></span>
                            </a>
                        @endforeach
                    </nav>

                    <div>
                        <div class="rp-card">
                            <div class="rp-status">
                                <span class="rp-status-icon">{!! \App\Support\SidebarMenu::icon('shield', 'rp-ic') !!}</span>
                                <div>
                                    <div class="rp-status-title">{{ $rpLabel }} can open {{ $rpOn }} of {{ $rpTotal }} pages</div>
                                    <div class="rp-status-sub">{{ $rpOn === $rpTotal ? 'Every page is on.' : ($rpTotal - $rpOn) . ' ' . ($rpTotal - $rpOn === 1 ? 'page is' : 'pages are') . ' switched off for this role.' }} The Dashboard is always on.</div>
                                </div>
                                <div class="rp-meter" aria-hidden="true"><span style="width: {{ $rpTotal ? round($rpOn / $rpTotal * 100) : 0 }}%"></span></div>
                            </div>
                        </div>

                        <div class="rp-card">
                            <div class="rp-card-head">
                                <div class="rp-card-title">Pages for {{ $rpLabel }}</div>
                            </div>
                            <div class="rp-card-sub">Switch a page off to hide it from every {{ $rpLabel }} account. Changes apply after you save.</div>

                            <form id="permissions-form" method="POST" action="{{ url('/admin/roles-permissions/' . $selectedRole) }}">
                                @csrf
                                @foreach($modules as $slug => $label)
                                    @php $on = $slug === 'dashboard' || ($permissions[$slug] ?? true); @endphp
                                    <label class="rp-row">
                                        <span class="rp-row-icon">{!! \App\Support\SidebarMenu::icon($rpIcons[$slug] ?? 'files', 'rp-ic') !!}</span>
                                        <span class="rp-row-main">
                                            <span class="rp-row-name">{{ $label }}</span>
                                            <span class="rp-row-state {{ $on ? '' : 'off' }}" data-state>{{ $slug === 'dashboard' ? 'Always on' : ($on ? 'On — shown in the sidebar' : 'Off — hidden from the sidebar') }}</span>
                                        </span>
                                        @if($slug === 'dashboard')
                                            {{-- Dashboard is always checked and cannot be unchecked --}}
                                            <input type="checkbox" class="perm-check" checked disabled title="Dashboard is always enabled" aria-label="{{ $label }}">
                                            <input type="hidden" name="permissions[dashboard]" value="1">
                                        @else
                                            <input type="checkbox" class="perm-check" name="permissions[{{ $slug }}]" aria-label="{{ $label }}" {{ ($permissions[$slug] ?? true) ? 'checked' : '' }}>
                                        @endif
                                    </label>
                                @endforeach

                                <div class="rp-actions">
                                    <button type="submit" class="btn-save">{!! \App\Support\SidebarMenu::icon('checkCircle', 'rp-ic') !!} Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                // The words under each page follow its switch before saving.
                document.querySelectorAll('.rp .perm-check:not(:disabled)').forEach(function (box) {
                    box.addEventListener('change', function () {
                        var state = box.closest('.rp-row').querySelector('[data-state]');
                        state.textContent = box.checked ? 'On — shown in the sidebar' : 'Off — hidden from the sidebar';
                        state.classList.toggle('off', !box.checked);
                    });
                });
            </script>
        </div>
    </div>


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