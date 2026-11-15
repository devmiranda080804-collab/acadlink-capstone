{{-- The CBMA panel on the left of the sign-in pages: the seal, the system's name and tagline, and the college. --}}
<div class="logo-panel">
    <div class="seal"><img src="{{ \App\Support\SiteContent::imageUrl('login_logo') }}" alt="Logo"></div>
    <div class="brand">
        <div class="brand-name">{{ \App\Support\SiteContent::text('system_name') }}</div>
        <div class="brand-bar" aria-hidden="true"></div>
        <p class="brand-tag">{{ \App\Support\SiteContent::text('system_tagline') }}</p>
    </div>
    <div class="college-line">
        {{ \App\Support\SiteContent::text('college_name') }}
        <span>{{ \App\Support\SiteContent::text('university_name') }}</span>
    </div>
    <span class="stripe" aria-hidden="true"></span>
</div>
