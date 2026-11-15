<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – {{ \App\Support\SiteContent::text('system_name') }}</title>
    <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    html, body {
        width: 100%;
        height: 100%;
        font-family: Arial, sans-serif;
    }

    .page-bg {
        position: relative;
        z-index: 1;
        width: 100%;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card-wrap {
        position: relative;
        display: flex;
        align-items: stretch;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 6px 28px rgba(0,0,0,0.5);
        width: 520px;
    }

    .logo-panel {
        width: 185px;
        flex-shrink: 0;
        background-color: #1b3d7a;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 28px 18px;
    }

    .logo-panel img {
        width: 148px;
        height: 148px;
        object-fit: contain;
        border-radius: 50%;
    }

    .form-panel {
        flex: 1;
        background-color: #ffffff;
        padding: 26px 26px 18px 26px;
        display: flex;
        flex-direction: column;
    }

    .form-panel h1 {
        font-size: 17px;
        font-weight: 700;
        color: #111;
        text-align: center;
        margin-bottom: 2px;
    }

    .form-panel .sub {
        font-size: 10.5px;
        color: #555;
        text-align: center;
        margin-bottom: 16px;
        line-height: 1.4;
    }

    .field { margin-bottom: 11px; }

    .field label {
        display: block;
        font-size: 11.5px;
        font-weight: 700;
        color: #222;
        margin-bottom: 3px;
    }

    .field input,
    .field select {
        width: 100%;
        height: 32px;
        padding: 0 10px;
        border: 1px solid #c8c8c8;
        border-radius: 4px;
        font-size: 12px;
        color: #333;
        background: #fff;
        outline: none;
        transition: border-color .15s;
    }

    .field input:focus,
    .field select:focus { border-color: #2563eb; }

    .field input::placeholder { color: #b0b0b0; font-size: 11.5px; }

    .field select {
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%23666' d='M5 7L0 2h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        cursor: pointer;
    }

    .field input.invalid,
    .field select.invalid { border-color: #ef4444; }

    .error-msg {
        display: none;
        background-color: #fee2e2;
        border: 1px solid #fca5a5;
        color: #b91c1c;
        font-size: 11px;
        padding: 6px 10px;
        border-radius: 4px;
        margin-bottom: 10px;
    }

    .btn-sign-in {
        width: 100%;
        height: 34px;
        margin-top: 2px;
        background-color: #0f2557;
        color: #fff;
        font-size: 13.5px;
        font-weight: 600;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        letter-spacing: 0.2px;
        transition: background-color .15s;
    }

    .btn-sign-in:hover { background-color: #1b3d7a; }

    .forgot { text-align: center; margin-top: 9px; }

    .forgot a {
        font-size: 11px;
        color: #2563eb;
        text-decoration: none;
    }

    .forgot a:hover { text-decoration: underline; }

    .copyright {
        text-align: center;
        font-size: 9.5px;
        color: #999;
        margin-top: 13px;
    }

    /* Show/hide password: an eye button inside the password box. */
    .pw-wrap { position: relative; }
    .field .pw-wrap input { padding-right: 36px; }
    .pw-toggle {
        position: absolute; top: 0; right: 0; height: 32px; width: 34px;
        display: flex; align-items: center; justify-content: center;
        background: none; border: none; color: #888; cursor: pointer; border-radius: 0 4px 4px 0;
    }
    .pw-toggle:hover { color: #1d4ed8; }
    .pw-toggle:focus-visible { outline: 2px solid #2563eb; outline-offset: -2px; }
    .pw-toggle svg { width: 16px; height: 16px; }
    .pw-toggle .pw-eye-off { display: none; }
    .pw-toggle[aria-pressed="true"] .pw-eye { display: none; }
    .pw-toggle[aria-pressed="true"] .pw-eye-off { display: block; }
</style>
</head>
<body>
    @include('partials.auth-background')

    <div class="page-bg">
        <div class="card-wrap">

            {{-- Logo --}}
            <div class="logo-panel">
                <img src="{{ \App\Support\SiteContent::imageUrl('login_logo') }}" alt="Logo">
            </div>

            {{-- Form --}}
            <div class="form-panel">
                <form method="POST" action="{{ route('login.submit') }}">
                @csrf

                <h1>{{ \App\Support\SiteContent::text('system_name') }}</h1>
                <p class="sub">{{ \App\Support\SiteContent::text('system_tagline') }}</p>

                @if($errors->any())
                    <div class="error-msg" style="display:block;">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="field">
                    <label for="email">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Enter your email address"
                        required>
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <div class="pw-wrap">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required>
                        <button type="button" class="pw-toggle" id="pw-toggle" aria-label="Show password" aria-pressed="false" aria-controls="password" title="Show password">
                            <svg class="pw-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="pw-eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19"/><path d="M14.12 14.12a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-sign-in">
                    Sign In
                </button>

               <div class="forgot"><a href="{{ route('password.request') }}">Forgot your password?</a></div>

                <p class="copyright">
                    © {{ date('Y') }} {{ \App\Support\SiteContent::text('system_name') }}. All rights reserved.
                </p>
            </form>


        </div>
    </div>

    <script>
        // The eye shows the typed password; pressing it again hides it.
        (function () {
            var toggle = document.getElementById('pw-toggle');
            var input = document.getElementById('password');
            toggle.addEventListener('click', function () {
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                toggle.setAttribute('aria-pressed', show ? 'true' : 'false');
                toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
                toggle.title = show ? 'Hide password' : 'Show password';
                input.focus();
            });
            // Never send the form with the password showing as text.
            input.form.addEventListener('submit', function () { input.type = 'password'; });
        })();
    </script>

    @include('partials.system-feedback')
</body>
</html>