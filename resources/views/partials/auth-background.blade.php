{{--
    CBMA-centered background of the login and password pages (panel comment Minor #1).
    With no photo uploaded it is the CBMA design: navy, the CBMA seal and the college name.
    A photo the Admin uploads in System CMS → Site Content → Login background shows behind
    it with the same CBMA tint, so the page stays CBMA-centered either way.
--}}
@php
    $cbmaPhoto = \App\Support\SiteContent::uploadedImage('login_background') ? \App\Support\SiteContent::imageUrl('login_background') : null;
@endphp
<style>
    .cbma-bg { position: fixed; inset: 0; z-index: 0; overflow: hidden; background: #0b1d47; }
    .cbma-bg-photo { position: absolute; inset: 0; background-size: cover; background-position: center; filter: blur(3px); transform: scale(1.05); }
    .cbma-bg-tint { position: absolute; inset: 0; background: linear-gradient(135deg, rgba(11,29,71,.96) 0%, rgba(27,61,122,.88) 55%, rgba(11,29,71,.96) 100%); }
    .cbma-bg.has-photo .cbma-bg-tint { background: linear-gradient(135deg, rgba(11,29,71,.78) 0%, rgba(27,61,122,.55) 55%, rgba(11,29,71,.8) 100%); }
    .cbma-bg-pattern { position: absolute; inset: 0; background-image: repeating-linear-gradient(45deg, rgba(255,255,255,.035) 0 2px, transparent 2px 24px); }
    .cbma-bg-seal { position: absolute; background-position: center; background-size: contain; background-repeat: no-repeat; border-radius: 50%; }
    .cbma-bg-seal.big { right: -9vmin; bottom: -12vmin; width: 72vmin; height: 72vmin; opacity: .10; }
    .cbma-bg-seal.small { left: -10vmin; top: -12vmin; width: 40vmin; height: 40vmin; opacity: .05; }
    .cbma-bg.has-photo .cbma-bg-seal.big { opacity: .14; }
    .cbma-bg-stripe { position: absolute; left: 0; right: 0; bottom: 0; height: 6px; background: linear-gradient(90deg, #C12035 0 33%, #f2c94c 33% 66%, #273F89 66% 100%); }
    .cbma-bg-text { position: absolute; left: 4vw; bottom: 6vh; max-width: 40vw; color: #fff; font-family: Arial, sans-serif; }
    .cbma-bg-text .bar { width: 56px; height: 3px; background: #f2c94c; margin-bottom: 12px; }
    .cbma-bg-text .college { font-size: clamp(15px, 2vw, 26px); font-weight: 700; letter-spacing: .4px; line-height: 1.25; text-transform: uppercase; }
    .cbma-bg-text .uni { font-size: clamp(10px, 1vw, 13px); letter-spacing: 2px; text-transform: uppercase; opacity: .75; margin-top: 8px; }
    @media (max-width: 900px), (max-height: 560px) { .cbma-bg-text { display: none; } }
</style>
<div class="cbma-bg {{ $cbmaPhoto ? 'has-photo' : '' }}" aria-hidden="true">
    @if($cbmaPhoto)
        <div class="cbma-bg-photo" style="background-image: url('{{ $cbmaPhoto }}');"></div>
    @endif
    <div class="cbma-bg-tint"></div>
    <div class="cbma-bg-pattern"></div>
    <div class="cbma-bg-seal small" style="background-image: url('{{ \App\Support\SiteContent::imageUrl('login_logo') }}');"></div>
    <div class="cbma-bg-seal big" style="background-image: url('{{ \App\Support\SiteContent::imageUrl('login_logo') }}');"></div>
    <div class="cbma-bg-text">
        <div class="bar"></div>
        <div class="college">{{ \App\Support\SiteContent::text('college_name') }}</div>
        <div class="uni">{{ \App\Support\SiteContent::text('university_name') }}</div>
    </div>
    <div class="cbma-bg-stripe"></div>
</div>
