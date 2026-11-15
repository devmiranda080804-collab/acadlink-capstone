<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard – CBMA System</title>
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
        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 3px; }
        .page-sub { font-size: 12px; color: #888; margin-bottom: 22px; }

        .announcement-banner { display: flex; align-items: center; gap: 10px; background: #eff6ff; border: 1px solid #bfdbfe; color: #1e3a8a; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 13px; }
        .announcement-banner svg { width: 18px; height: 18px; flex-shrink: 0; }
        .announcement-banner-text { flex: 1; }
        .announcement-banner-text strong { font-weight: 700; }
        .announcement-banner-link { color: #1d4ed8; font-weight: 600; text-decoration: none; font-size: 12px; white-space: nowrap; }
        .announcement-banner-link:hover { text-decoration: underline; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 16px; margin-bottom: 22px; }
        .stat-card { background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; padding: 18px 20px; display: flex; align-items: center; gap: 14px; }
        .stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .stat-icon svg { width: 22px; height: 22px; }
        .stat-icon.blue { background: #dbeafe; color: #1d4ed8; }
        .stat-icon.purple { background: #ede9fe; color: #7c3aed; }
        .stat-icon.green { background: #d1fae5; color: #059669; }
        .stat-icon.amber { background: #fef3c7; color: #d97706; }
        .stat-info .stat-value { font-size: 24px; font-weight: 700; color: #1a1a2e; line-height: 1.1; }
        .stat-info .stat-label { font-size: 11.5px; color: #888; margin-top: 2px; }

        .dash-row { display: grid; grid-template-columns: 1.3fr 1fr; gap: 20px; align-items: start; }
        .panel { background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; padding: 20px; }
        .panel-title { font-size: 14px; font-weight: 700; color: #1a1a2e; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between; }
        .panel-title a { font-size: 11.5px; color: #1d4ed8; text-decoration: none; font-weight: 600; }
        .panel-title a:hover { text-decoration: underline; }
        .list-item { display: flex; align-items: center; justify-content: space-between; padding: 11px 0; border-bottom: 1px solid #f5f5f5; }
        .list-item:last-child { border-bottom: none; }
        .li-left { display: flex; align-items: flex-start; gap: 10px; }
        .li-icon { font-size: 16px; margin-top: 1px; }
        .li-title { font-size: 12.5px; font-weight: 600; color: #1a1a2e; }
        .li-meta { font-size: 10.5px; color: #aaa; margin-top: 1px; }
        .li-badge { font-size: 10px; font-weight: 700; padding: 3px 10px; border-radius: 12px; background: #d1fae5; color: #065f46; white-space: nowrap; }
        .event-date-box { background: #dbeafe; color: #1d4ed8; border-radius: 8px; padding: 5px 9px; text-align: center; flex-shrink: 0; min-width: 44px; }
        .event-date-box .m { font-size: 9.5px; font-weight: 700; text-transform: uppercase; }
        .event-date-box .d { font-size: 15px; font-weight: 700; line-height: 1.1; }
        .empty-mini { text-align: center; padding: 24px; color: #bbb; font-size: 12px; }

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
            @include('partials.dashboard.styles')
            <div class="db">
                @include('partials.dashboard.hero', [
                    'subtitle' => 'Template submissions, the Dean’s decisions and faculty accounts at a glance.',
                    'actions' => [
                        ['New template', '/secretary/template-distribution', 'plus', 'template-distribution', true],
                        ['Course Filing', '/secretary/course-filing', 'folderCheck', 'course-filing'],
                        ['Document Repository', '/secretary/document-repository', 'archive', 'document-repository'],
                    ],
                ])
                @include('partials.dashboard.kpis', ['kpis' => [
                    ['Waiting for Dean Approval', $awaitingApproval, 'clock', 'amber', '/secretary/template-distribution', 'template-distribution', 'Submitted templates'],
                    ['Approved by the Dean', $approvedCount, 'checkCircle', 'green', '/secretary/template-distribution', 'template-distribution', 'Ready for faculty'],
                    ['Faculty Members', $facultyCount, 'users', 'blue', '/secretary/account-management', 'account-management', 'All programs'],
                    ['Returned to You', $returnedCount, 'undo', 'red', '/secretary/template-distribution', 'template-distribution', 'Needs your fix'],
                ]])
                @include('partials.dashboard.notice', ['unread' => $unreadAnnouncements, 'url' => '/secretary/announcements'])

                <div class="db-grid">
                    <div class="db-panel db-rise">
                        <div class="db-panel-head">
                            <span class="db-panel-icon">{!! \App\Support\SidebarMenu::icon('inbox', 'db-icon') !!}</span>
                            <span class="db-panel-title">Needs Your Attention</span>
                            @if($pendingDistribution->count() > 0)<span class="db-count">{{ $pendingDistribution->count() }}</span>@endif
                            <a class="db-more" href="{{ url('/secretary/template-distribution') }}">View all {!! \App\Support\SidebarMenu::icon('arrow', 'db-icon') !!}</a>
                        </div>
                        @forelse($pendingDistribution as $template)
                            <div class="db-row">
                                @include('partials.dashboard.file', ['type' => $template->file_type])
                                <div class="db-row-main">
                                    <div class="db-row-title">{{ $template->title }}</div>
                                    <div class="db-row-meta">{{ \App\Models\TemplateDocument::typeLabel($template->type) }}@if($template->isReturned()) · {{ $template->return_reason }}@endif</div>
                                </div>
                                <span class="db-pill {{ $template->isReturned() ? 'back' : 'wait' }}">{{ $template->isReturned() ? 'Returned' : 'Waiting for Dean' }}</span>
                            </div>
                        @empty
                            <div class="db-empty"><span class="db-empty-icon">{!! \App\Support\SidebarMenu::icon('checkCircle', 'db-icon') !!}</span>No templates waiting for the Dean or returned to you.</div>
                        @endforelse
                    </div>

                    @include('partials.dashboard.activities', ['activities' => $upcomingActivities, 'url' => '/secretary/calendar'])
                </div>
            </div>
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