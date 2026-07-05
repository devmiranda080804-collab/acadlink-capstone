<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password – CBMA System</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, sans-serif; background:#f0f0f0; height:100vh; display:flex; align-items:center; justify-content:center; }
        .card { background:#fff; width:420px; padding:32px 28px; border-radius:10px; box-shadow:0 6px 24px rgba(0,0,0,0.12); }
        .logo { text-align:center; margin-bottom:20px; }
        .logo img { width:64px; height:64px; border-radius:50%; }
        h1 { font-size:17px; color:#111; margin-bottom:6px; text-align:center; }
        p.sub { font-size:12px; color:#666; margin-bottom:22px; line-height:1.5; text-align:center; }
        .field { margin-bottom:14px; }
        .field label { display:block; font-size:11.5px; font-weight:700; color:#222; margin-bottom:4px; }
        .field input { width:100%; height:36px; padding:0 10px; border:1px solid #ccc; border-radius:5px; font-size:12.5px; outline:none; transition:border-color .15s; }
        .field input:focus { border-color:#1d4ed8; }
        .error-msg { background:#fee2e2; border:1px solid #fca5a5; color:#b91c1c; font-size:11px; padding:8px 10px; border-radius:4px; margin-bottom:14px; }
        .hint { font-size:11px; color:#999; margin-top:4px; }
        .btn { width:100%; height:38px; background:#1d4ed8; color:#fff; border:none; border-radius:5px; font-size:13px; font-weight:600; cursor:pointer; margin-top:6px; transition:background .15s; }
        .btn:hover { background:#1e40af; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">
            <img src="{{ asset('images/cbma-logo.png') }}" alt="CBMA Logo">
        </div>
        <h1>Change Your Password</h1>
        <p class="sub">Para sa iyong seguridad, palitan muna ang temporary password mo bago ka magpatuloy sa system.</p>

        @if($errors->any())
            <div class="error-msg">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ url('/change-password') }}">
            @csrf
            <div class="field">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password"
                    placeholder="Minimum 8 characters" required>
                <span class="hint">At least 8 characters</span>
            </div>
            <div class="field">
                <label for="password_confirmation">Confirm New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                    placeholder="Re-type new password" required>
            </div>
            <button type="submit" class="btn">Update Password</button>
        </form>
    </div>
</body>
</html>