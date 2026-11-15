{{--
    The dashboard hero: greeting for the time of day, role and term, today's date and quick actions.
    Params: $subtitle (HTML-safe string), $actions = [[label, url, icon, permission key|null, primary?], ...]
--}}
@php
    $dbNow = now('Asia/Manila');
    $dbHour = (int) $dbNow->format('G');
    $dbGreeting = $dbHour < 12 ? 'Good morning' : ($dbHour < 18 ? 'Good afternoon' : 'Good evening');
    $dbFirst = auth()->user()->first_name ?: strtok(auth()->user()->name, ' ');
    $dbRole = \App\Support\SidebarMenu::ROLE_LABELS[auth()->user()->role] ?? '';
    $dbTerm = \App\Support\AcademicTerm::shortLabel(\App\Support\AcademicTerm::currentSemester(), \App\Support\AcademicTerm::currentSchoolYear());
    $dbAllowed = fn ($perm) => $perm === null || (($navPermissions ?? [])[$perm] ?? true);
@endphp
<section class="db-hero db-rise" aria-label="Welcome">
    <div class="db-hero-seal" style="background-image:url('{{ \App\Support\SiteContent::imageUrl('login_logo') }}')"></div>
    <div class="db-hero-inner">
        <div>
            <div class="db-eyebrow">{{ $dbRole }} dashboard <span class="db-chip">{!! \App\Support\SidebarMenu::icon('calendar', 'db-icon') !!} {{ $dbTerm }}</span></div>
            <h1 class="db-greeting">{{ $dbGreeting }}, <span>{{ $dbFirst }}</span>!</h1>
            <div class="db-hero-sub">{!! $subtitle !!}</div>
        </div>
        <div class="db-date" aria-label="Today">
            <div class="day">{{ $dbNow->format('l') }}</div>
            <div class="num">{{ $dbNow->format('j') }}</div>
            <div class="month">{{ $dbNow->format('F Y') }}</div>
        </div>
    </div>
    @php $dbShown = array_filter($actions ?? [], fn ($a) => $dbAllowed($a[3] ?? null)); @endphp
    @if($dbShown)
        <div class="db-actions">
            @foreach($dbShown as $a)
                <a class="db-action {{ !empty($a[4]) ? 'primary' : '' }}" href="{{ url($a[1]) }}">{!! \App\Support\SidebarMenu::icon($a[2], 'db-icon') !!} {{ $a[0] }}</a>
            @endforeach
        </div>
    @endif
</section>
