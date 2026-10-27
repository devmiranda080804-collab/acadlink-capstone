@php
    // Error pages avoid anything that could fail again (a 500 may mean the database is
    // down), so the signed-in user is read defensively.
    try {
        $role = auth()->check() ? auth()->user()->role : null;
    } catch (\Throwable $e) {
        $role = null;
    }
    $home = match ($role) {
        'admin'        => url('/admin/dashboard'),
        'program_head' => url('/program-head/dashboard'),
        'secretary'    => url('/secretary/dashboard'),
        'faculty'      => url('/faculty/dashboard'),
        default        => url('/login'),
    };
    $homeLabel = $role ? 'Back to Dashboard' : 'Go to Login';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') – CBMA System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; background: #eef1f7; color: #2b3245; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px 16px; }
        .card { width: 100%; max-width: 520px; background: #fff; border: 1px solid #dde2ee; border-radius: 16px; overflow: hidden; box-shadow: 0 18px 50px rgba(15, 37, 87, 0.12); text-align: center; }
        .head { background: #0f2557; border-bottom: 4px solid #f2b705; padding: 26px 24px 20px; }
        .head img { width: 70px; height: 70px; object-fit: contain; display: block; margin: 0 auto 10px; }
        .head .college { font-size: 10.5px; font-weight: 700; letter-spacing: 1.5px; color: #f2b705; text-transform: uppercase; }
        .head .uni { font-size: 12px; color: #c9d3ee; margin-top: 2px; }
        .body { padding: 30px 32px 32px; }
        .icon { width: 58px; height: 58px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
        .icon svg { width: 28px; height: 28px; }
        .tone-info .icon { background: #e8eefc; color: #1f4fbf; }
        .tone-warning .icon { background: #fff4dc; color: #b45309; }
        .tone-danger .icon { background: #fde8ea; color: #b4232f; }
        .code { font-size: 11px; font-weight: 700; letter-spacing: 1px; color: #8a91a5; text-transform: uppercase; margin-bottom: 6px; }
        h1 { font-size: 22px; line-height: 1.3; color: #0f2557; margin-bottom: 10px; }
        p { font-size: 14.5px; line-height: 1.6; color: #5b6377; }
        .actions { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; margin-top: 24px; }
        .btn { display: inline-flex; align-items: center; gap: 7px; font-size: 14px; font-weight: 700; padding: 11px 20px; border-radius: 8px; text-decoration: none; cursor: pointer; border: 1px solid transparent; font-family: inherit; }
        .btn svg { width: 16px; height: 16px; }
        .btn-primary { background: #0f2557; color: #fff; }
        .btn-primary:hover { background: #1b3d7a; }
        .btn-light { background: #fff; color: #0f2557; border-color: #c9d1e3; }
        .btn-light:hover { background: #f3f6fd; }
        .foot { border-top: 1px solid #eef1f7; background: #f8f9fc; padding: 12px 20px; font-size: 11.5px; color: #8a91a5; }
    </style>
</head>
<body>
    <main class="card tone-@yield('tone', 'info')">
        <div class="head">
            <img src="{{ asset('images/cbma-logo.png') }}" alt="CBMA logo">
            <div class="college">College of Business Management and Accountancy</div>
            <div class="uni">Urdaneta City University · CBMA System</div>
        </div>
        <div class="body">
            <div class="icon" aria-hidden="true">@yield('icon')</div>
            <div class="code">Error @yield('code')</div>
            <h1>@yield('heading')</h1>
            <p>@yield('message')</p>
            <div class="actions">
                @hasSection('primary')
                    @yield('primary')
                    <a class="btn btn-light" href="{{ $home }}">{{ $homeLabel }}</a>
                @else
                    <a class="btn btn-primary" href="{{ $home }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1z"/></svg>
                        {{ $homeLabel }}
                    </a>
                    <button type="button" class="btn btn-light" onclick="history.length > 1 ? history.back() : location.assign(@js($home))">Go Back</button>
                @endif
            </div>
        </div>
        <div class="foot">If this keeps happening, please contact the system administrator.</div>
    </main>
</body>
</html>
