{{-- Status pill + heading at the top of an email. $tone picks the pill colours. --}}
@php
    $tones = [
        'info'    => ['#e8eefc', '#1f4fbf'],
        'success' => ['#e3f5ea', '#1d7a43'],
        'danger'  => ['#fde8ea', '#b4232f'],
        'warning' => ['#fff4dc', '#9a6200'],
    ];
    [$pillBg, $pillFg] = $tones[$tone ?? 'info'];
@endphp
<span style="display:inline-block; background-color:{{ $pillBg }}; color:{{ $pillFg }}; font-size:11px; font-weight:bold; letter-spacing:1px; text-transform:uppercase; padding:5px 12px; border-radius:999px;">{{ $label }}</span>
<h1 style="margin:14px 0 18px; font-family:Arial, Helvetica, sans-serif; font-size:22px; line-height:30px; color:#0f2557;">{{ $title }}</h1>
