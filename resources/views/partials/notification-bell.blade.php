{{-- The bell in the top bar: the latest notifications for the signed-in user. --}}
@auth
@php
    $bellItems = auth()->user()->notifications()->latest()->take(15)->get();
    $bellUnread = auth()->user()->unreadNotifications()->count();
    $bellIcons = [
        'template' => '📄', 'return' => '↩️', 'check' => '✅', 'calendar' => '📅', 'upload' => '📤',
        'course' => '📚', 'megaphone' => '📢', 'library' => '📂', 'clock' => '⏰', 'account' => '👤', 'bell' => '🔔',
    ];
@endphp
<style>
    .nb-wrap { position: relative; display: flex; align-items: center; margin-right: 4px; }
    .nb-button { position: relative; width: 36px; height: 36px; border-radius: 50%; border: none; background: transparent; color: #0f2557; cursor: pointer; display: flex; align-items: center; justify-content: center; }
    .nb-button:hover, .nb-button[aria-expanded="true"] { background: #eef2ff; }
    .nb-button svg { width: 19px; height: 19px; }
    .nb-count { position: absolute; top: 2px; right: 1px; min-width: 17px; height: 17px; padding: 0 4px; border-radius: 9px; background: #ef4444; color: #fff; font-size: 10px; font-weight: 700; line-height: 17px; text-align: center; font-family: Arial, sans-serif; }
    .nb-panel { display: none; position: absolute; top: 44px; right: 0; width: 360px; max-width: calc(100vw - 24px); background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.16); z-index: 1200; font-family: Arial, sans-serif; overflow: hidden; }
    .nb-panel.open { display: block; }
    .nb-head { display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; border-bottom: 1px solid #f0f0f0; }
    .nb-head strong { font-size: 13.5px; color: #1a1a2e; }
    .nb-read-all { background: none; border: none; color: #1d4ed8; font-size: 11.5px; font-weight: 600; cursor: pointer; padding: 0; }
    .nb-list { max-height: 420px; overflow-y: auto; }
    .nb-item { display: flex; gap: 10px; padding: 11px 14px; border-bottom: 1px solid #f5f5f5; text-decoration: none; color: inherit; }
    .nb-item:hover { background: #f8f9fc; }
    .nb-item.unread { background: #f3f6ff; }
    .nb-item.unread:hover { background: #e9efff; }
    .nb-icon { font-size: 16px; line-height: 20px; flex-shrink: 0; width: 20px; text-align: center; }
    .nb-text { flex: 1; min-width: 0; }
    .nb-title { font-size: 12.5px; font-weight: 700; color: #1a1a2e; line-height: 1.35; overflow-wrap: anywhere; }
    .nb-body { font-size: 11.5px; color: #555; line-height: 1.4; margin-top: 2px; overflow-wrap: anywhere; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .nb-time { font-size: 10.5px; color: #999; margin-top: 3px; }
    .nb-dot { width: 8px; height: 8px; border-radius: 50%; background: #2563eb; flex-shrink: 0; margin-top: 6px; }
    .nb-empty { padding: 34px 16px; text-align: center; font-size: 12.5px; color: #999; }
    @media (max-width: 600px) { .nb-panel { position: fixed; top: 56px; left: 12px; right: 12px; width: auto; max-width: none; } }
</style>
<div class="nb-wrap">
    <button type="button" class="nb-button" id="nb-button" aria-label="Notifications{{ $bellUnread ? ', ' . $bellUnread . ' unread' : '' }}" aria-expanded="false" aria-controls="nb-panel" onclick="toggleNotifications()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        @if($bellUnread)<span class="nb-count" id="nb-count">{{ $bellUnread > 99 ? '99+' : $bellUnread }}</span>@endif
    </button>
    <div class="nb-panel" id="nb-panel" role="dialog" aria-label="Notifications">
        <div class="nb-head">
            <strong>Notifications</strong>
            @if($bellUnread)
                <button type="button" class="nb-read-all" onclick="readAllNotifications(this)">Mark all as read</button>
            @endif
        </div>
        <div class="nb-list">
            @forelse($bellItems as $n)
                <a class="nb-item {{ $n->read_at ? '' : 'unread' }}" href="{{ url('/notifications/' . $n->id) }}">
                    <span class="nb-icon" aria-hidden="true">{{ $bellIcons[$n->data['icon'] ?? 'bell'] ?? '🔔' }}</span>
                    <span class="nb-text">
                        <span class="nb-title" style="display:block;">{{ $n->data['title'] ?? 'Notification' }}</span>
                        @if(!empty($n->data['body']))<span class="nb-body">{{ $n->data['body'] }}</span>@endif
                        <span class="nb-time" style="display:block;" title="{{ \App\Support\DisplayTime::format($n->created_at) }}">{{ $n->created_at->diffForHumans() }}</span>
                    </span>
                    @unless($n->read_at)<span class="nb-dot" aria-label="Unread"></span>@endunless
                </a>
            @empty
                <div class="nb-empty">You're all caught up. New notifications will show here.</div>
            @endforelse
        </div>
    </div>
</div>
<script>
    function toggleNotifications() {
        var panel = document.getElementById('nb-panel');
        var open = !panel.classList.contains('open');
        panel.classList.toggle('open', open);
        document.getElementById('nb-button').setAttribute('aria-expanded', open ? 'true' : 'false');
    }
    document.addEventListener('click', function (e) {
        var wrap = document.querySelector('.nb-wrap');
        if (wrap && !wrap.contains(e.target)) {
            document.getElementById('nb-panel').classList.remove('open');
            document.getElementById('nb-button').setAttribute('aria-expanded', 'false');
        }
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && document.getElementById('nb-panel').classList.contains('open')) {
            toggleNotifications();
            document.getElementById('nb-button').focus();
        }
    });
    async function readAllNotifications(button) {
        button.disabled = true;
        await fetch(@json(url('/notifications/read-all')), { method: 'POST', headers: { Accept: 'application/json', 'X-CSRF-TOKEN': @json(csrf_token()) } }).catch(function () {});
        document.querySelectorAll('.nb-item.unread').forEach(function (item) { item.classList.remove('unread'); });
        document.querySelectorAll('.nb-dot').forEach(function (dot) { dot.remove(); });
        var count = document.getElementById('nb-count');
        if (count) count.remove();
        button.remove();
    }
</script>
@endauth
