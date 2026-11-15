<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – {{ \App\Support\SiteContent::text('system_name') }}</title>
    @include('partials.auth-styles')
</head>
<body>
    @include('partials.auth-background')

    <div class="page-bg">
        <div class="card-wrap">

            @include('partials.auth-brand')

            {{-- Form --}}
            <div class="form-panel">
                <form method="POST" action="{{ route('login.submit') }}">
                @csrf

                <div class="eyebrow">Welcome back</div>
                <h1>Sign in to your account</h1>
                <p class="sub">Use the email address and password given to you by the college.</p>

                @include('partials.auth-alerts')

                <div class="field">
                    <label for="email">Email Address</label>
                    <div class="input-wrap">
                        <svg class="lead" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="Enter your email address"
                            autocomplete="username"
                            required>
                    </div>
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    @include('partials.auth-password', ['id' => 'password', 'name' => 'password', 'placeholder' => 'Enter your password', 'autocomplete' => 'current-password'])
                </div>

                <button type="submit" class="btn-sign-in">
                    Sign In
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </button>

                <div class="forgot"><a href="{{ route('password.request') }}">Forgot your password?</a></div>

                <p class="copyright">
                    © {{ date('Y') }} {{ \App\Support\SiteContent::text('system_name') }}. All rights reserved.
                </p>
            </form>
            </div>

        </div>
    </div>

    @include('partials.auth-scripts')
    @include('partials.system-feedback')
</body>
</html>
