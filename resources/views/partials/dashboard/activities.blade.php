{{-- Upcoming activities as a short timeline. Params: $activities, $url (the role's calendar) --}}
<div class="db-panel db-rise">
    <div class="db-panel-head">
        <span class="db-panel-icon">{!! \App\Support\SidebarMenu::icon('calendar', 'db-icon') !!}</span>
        <span class="db-panel-title">Upcoming Activities</span>
        @if($activities->isNotEmpty())<span class="db-count">{{ $activities->count() }}</span>@endif
        <a class="db-more" href="{{ url($url) }}">Calendar {!! \App\Support\SidebarMenu::icon('arrow', 'db-icon') !!}</a>
    </div>
    <div class="db-timeline">
        @forelse($activities as $activity)
            @php
                $days = (int) now('Asia/Manila')->startOfDay()->diffInDays($activity->activity_date->copy()->startOfDay(), false);
                $when = $days <= 0 ? 'Today' : ($days === 1 ? 'Tomorrow' : 'In ' . $days . ' days');
            @endphp
            <div class="db-event">
                <div class="db-event-date">
                    <div class="m">{{ $activity->activity_date->format('M') }}</div>
                    <div class="d">{{ $activity->activity_date->format('d') }}</div>
                </div>
                <div class="db-row-main">
                    <div class="db-row-title">{{ $activity->title }}</div>
                    <div class="db-row-meta">{!! \App\Support\SidebarMenu::icon('pin', 'db-icon') !!}{{ $activity->location ?? 'No location' }}</div>
                    <span class="db-event-when {{ $days <= 0 ? 'today' : '' }}">{{ $when }}</span>
                </div>
            </div>
        @empty
            <div class="db-empty"><span class="db-empty-icon">{!! \App\Support\SidebarMenu::icon('calendar', 'db-icon') !!}</span>No upcoming activities.</div>
        @endforelse
    </div>
</div>
