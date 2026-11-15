<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password – {{ \App\Support\SiteContent::text('system_name') }}</title>
    @include('partials.auth-styles')
</head>
<body>
    @include('partials.auth-background')

    <div class="page-bg">
        <div class="card-wrap">

            @include('partials.auth-brand')

            <div class="form-panel">
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="eyebrow">Account recovery</div>
                    <h1>Forgot your password?</h1>
                    <p class="sub">Enter your email address and we will send you a link to reset your password.</p>

                    @include('partials.auth-alerts')

                    <div class="field">
                        <label for="email">Email Address</label>
                        <div class="input-wrap">
                            <svg class="lead" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            <input type="email" id="email" name="email"
                                value="{{ old('email') }}"
                                placeholder="Enter your email address"
                                autocomplete="email"
                                required>
                        </div>
                    </div>

                    <button type="submit" class="btn-sign-in">
                        Send Reset Link
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    </button>

                    <div class="back-link">
                        <a href="{{ url('/login') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>Back to Login</a>
                    </div>

                    <p class="copyright">© {{ date('Y') }} {{ \App\Support\SiteContent::text('system_name') }}. All rights reserved.</p>
                </form>
            </div>

        </div>
    </div>

    @include('partials.system-feedback')
</body>
</html>
