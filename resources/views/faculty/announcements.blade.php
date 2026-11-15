<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements – CBMA System</title>
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
        .page-header { margin-bottom: 20px; }
        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 3px; }
        .page-sub { font-size: 11.5px; color: #888; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 16px; margin-bottom: 20px; }
        .stat-card { background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; padding: 16px 18px; display: flex; align-items: center; gap: 14px; }
        .stat-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .stat-icon svg { width: 20px; height: 20px; }
        .stat-icon.blue { background: #dbeafe; color: #1d4ed8; }
        .stat-icon.green { background: #d1fae5; color: #059669; }
        .stat-icon.amber { background: #fef3c7; color: #b45309; }
        .stat-info .stat-value { font-size: 22px; font-weight: 700; color: #1a1a2e; line-height: 1.1; }
        .stat-info .stat-label { font-size: 11px; color: #888; margin-top: 2px; }

        .filter-tabs { display: flex; gap: 4px; margin-bottom: 18px; }
        .filter-tab { padding: 7px 16px; font-size: 12px; color: #666; background: #fff; border: 1px solid #e0e0e0; border-radius: 6px; cursor: pointer; user-select: none; }
        .filter-tab:hover { border-color: #0f2557; color: #0f2557; }
        .filter-tab.active { background: #0f2557; color: #fff; border-color: #0f2557; font-weight: 600; }

        .ann-panel { background: #fff; border: 1px solid #e4e4e4; border-radius: 8px; padding: 10px 0; min-height: 200px; }
        .ann-item { display: flex; gap: 14px; padding: 16px 20px; border-bottom: 1px solid #f0f0f0; }
        .ann-item:last-child { border-bottom: none; }
        .ann-item:hover { background: #fafbff; }
        .ann-icon { width: 38px; height: 38px; border-radius: 10px; background: #eef2ff; color: #0f2557; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 17px; }
        .ann-body-wrap { flex: 1; min-width: 0; }
        .ann-item-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 6px; }
        .ann-title { font-size: 13.5px; font-weight: 700; color: #1a1a2e; margin-bottom: 3px; }
        .ann-meta { font-size: 10.5px; color: #aaa; }
        .ann-body { font-size: 12px; color: #555; line-height: 2; margin-top: 6px; white-space: pre-line; }
        .source-badge { font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 10px; background: #f0f4ff; color: #0f2557; margin-left: 6px; }
        .expiry-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 700; padding: 3px 10px; border-radius: 12px; background: #fef3c7; color: #92400e; margin-top: 8px; }
        .expiry-badge svg { width: 10px; height: 10px; }
        .ann-empty { text-align: center; padding: 50px 20px; color: #bbb; font-size: 12.5px; }
        .ann-empty .empty-icon { font-size: 30px; opacity: 0.3; margin-bottom: 8px; }
        svg { display: inline-block; vertical-align: middle; }
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
            <div class="page-header">
                <div class="page-title">Announcements</div>
                <div class="page-sub">Announcements for the <strong>{{ auth()->user()->programs_label }}</strong> program</div>
            </div>

            @php
                $expiringSoonCount = $announcements->filter(fn($a) => $a->is_expiring_soon)->count();
                $permanentCount = $announcements->filter(fn($a) => !$a->expires_at)->count();
            @endphp

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg></div>
                    <div class="stat-info"><div class="stat-value">{{ $announcements->count() }}</div><div class="stat-label">Total Announcements</div></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
                    <div class="stat-info"><div class="stat-value">{{ $permanentCount }}</div><div class="stat-label">No Expiration</div></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon amber"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
                    <div class="stat-info"><div class="stat-value">{{ $expiringSoonCount }}</div><div class="stat-label">Expiring Soon</div></div>
                </div>
            </div>

            <div class="filter-tabs">
                <span class="filter-tab active" onclick="filterAnn('all', this)">All</span>
                <span class="filter-tab" onclick="filterAnn('permanent', this)">No Expiration</span>
                <span class="filter-tab" onclick="filterAnn('soon', this)">Expiring Soon</span>
            </div>

            <div class="ann-panel" id="ann-list">
                @forelse($announcements as $ann)
                    @php
                        $status = $ann->expires_at ? ($ann->is_expiring_soon ? 'soon' : 'active') : 'permanent';
                    @endphp
                    <div class="ann-item" data-status="{{ $status }}">
                        <div class="ann-icon">📢</div>
                        <div class="ann-body-wrap">
                            <div class="ann-item-header">
                                <div>
                                    <div class="ann-title">{{ $ann->title }}</div>
                                    <div class="ann-meta">
                                        Posted by <strong>{{ $ann->user->name }}</strong>
                                        <span class="source-badge">
                                            {{ $ann->user->role === 'program_head' ? 'Program Head' : ucfirst($ann->user->role) }}
                                        </span>
                                        · {{ $ann->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                            <div class="ann-body">{{ $ann->body }}</div>
                            @if($ann->expires_at)
                                <span class="expiry-badge">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    Expires {{ $ann->expires_at->format('M d, Y g:i A') }}
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="ann-empty"><div class="empty-icon">📢</div>No announcements yet for your program.</div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        function filterAnn(status, el) {
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            el.classList.add('active');
            document.querySelectorAll('#ann-list .ann-item').forEach(item => {
                if (!item.dataset.status) return;
                item.style.display = (status === 'all' || item.dataset.status === status) ? '' : 'none';
            });
        }
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