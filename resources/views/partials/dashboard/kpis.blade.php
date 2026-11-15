{{--
    KPI cards floating over the hero. A card is a link when its page is allowed for this user.
    Params: $kpis = [[label, value, icon, tone, url|null, permission key|null, footnote], ...]
--}}
@php $dbAllowed = fn ($perm) => $perm === null || (($navPermissions ?? [])[$perm] ?? true); @endphp
<div class="db-kpis">
    @foreach($kpis as [$label, $value, $icon, $tone, $url, $perm, $foot])
        @php $link = $url && $dbAllowed($perm); @endphp
        <{{ $link ? 'a' : 'div' }} class="db-kpi db-rise tone-{{ $tone }}" @if($link) href="{{ url($url) }}" @endif>
            <div class="db-kpi-top">
                <span class="db-kpi-label">{{ $label }}</span>
                <span class="db-kpi-icon">{!! \App\Support\SidebarMenu::icon($icon, 'db-icon') !!}</span>
            </div>
            <div class="db-kpi-value" data-count="{{ (int) $value }}">{{ $value }}</div>
            <div class="db-kpi-foot">
                <span>{{ $foot }}</span>
                @if($link)<span class="go">Open {!! \App\Support\SidebarMenu::icon('arrow', 'db-icon') !!}</span>@endif
            </div>
        </{{ $link ? 'a' : 'div' }}>
    @endforeach
</div>
