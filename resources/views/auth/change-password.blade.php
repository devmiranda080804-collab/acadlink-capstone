<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password – {{ \App\Support\SiteContent::text('system_name') }}</title>
    @include('partials.auth-styles')
</head>
<body>
    @include('partials.auth-background')

    <div class="page-bg">
        <div class="card-wrap">

            @include('partials.auth-brand')

            <div class="form-panel">
                <form method="POST" action="{{ url('/change-password') }}">
                    @csrf

                    <div class="eyebrow">Account security</div>
                    <h1>Change your password</h1>
                    <p class="sub">For your security, please change your temporary password before continuing to the system.</p>

                    @include('partials.auth-alerts')

                    <div class="field">
                        <label for="password">New Password</label>
                        @include('partials.auth-password', ['id' => 'password', 'name' => 'password', 'placeholder' => 'Minimum 8 characters'])
                        <span class="hint">At least 8 characters</span>
                    </div>

                    <div class="field">
                        <label for="password_confirmation">Confirm New Password</label>
                        @include('partials.auth-password', ['id' => 'password_confirmation', 'name' => 'password_confirmation', 'placeholder' => 'Re-type new password'])
                    </div>

                    <button type="submit" class="btn-sign-in">
                        Update Password
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>

                    <p class="copyright">© {{ date('Y') }} {{ \App\Support\SiteContent::text('system_name') }}. All rights reserved.</p>
                </form>
            </div>

        </div>
    </div>

    @include('partials.auth-scripts')
    @include('partials.system-feedback')
</body>
</html>
