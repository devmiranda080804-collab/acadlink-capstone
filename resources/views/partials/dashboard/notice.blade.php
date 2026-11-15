{{-- Unread announcements notice. Params: $unread (collection), $url --}}
@if($unread->isNotEmpty())
    @php $dbUnreadTotal = $unreadAnnouncementsCount ?? $unread->count(); @endphp
    <div class="db-notice db-rise" role="status">
        <div class="db-notice-icon">{!! \App\Support\SidebarMenu::icon('megaphone', 'db-icon') !!}</div>
        <div class="db-notice-text">
            <strong>{{ $dbUnreadTotal }} new announcement{{ $dbUnreadTotal > 1 ? 's' : '' }}</strong>
            {{ $unread->pluck('title')->implode(' · ') }}
        </div>
        <a href="{{ url($url) }}" class="db-notice-link">Read now {!! \App\Support\SidebarMenu::icon('arrow', 'db-icon') !!}</a>
    </div>
@endif
