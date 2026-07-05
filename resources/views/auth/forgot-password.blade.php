<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password – CBMA System</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, sans-serif; background-color: #4a6b4a; background-image: url('{{ asset("images/bg-campus.jpg") }}'); background-size:cover; background-position:center; height:100vh; display:flex; align-items:center; justify-content:center; position:relative; }
        body::before { content:''; position:absolute; inset:0; background:rgba(0,0,0,0.28); }
        .card-wrap { position:relative; display:flex; align-items:stretch; border-radius:8px; overflow:hidden; box-shadow:0 6px 28px rgba(0,0,0,0.5); width:520px; }
        .logo-panel { width:185px; flex-shrink:0; background-color:#1b3d7a; display:flex; align-items:center; justify-content:center; padding:28px 18px; }
        .logo-panel img { width:148px; height:148px; object-fit:contain; border-radius:50%; }
        .form-panel { flex:1; background-color:#fff; padding:26px 26px 22px; display:flex; flex-direction:column; }
        .form-panel h1 { font-size:17px; font-weight:700; color:#111; text-align:center; margin-bottom:2px; }
        .form-panel .sub { font-size:10.5px; color:#555; text-align:center; margin-bottom:6px; line-height:1.4; }
        .form-panel .desc { font-size:11.5px; color:#444; text-align:center; margin-bottom:18px; line-height:1.6; }
        .field { margin-bottom:13px; }
        .field label { display:block; font-size:11.5px; font-weight:700; color:#222; margin-bottom:3px; }
        .field input { width:100%; height:34px; padding:0 10px; border:1px solid #c8c8c8; border-radius:4px; font-size:12px; color:#333; outline:none; transition:border-color .15s; }
        .field input:focus { border-color:#2563eb; }
        .field input::placeholder { color:#b0b0b0; font-size:11.5px; }
        .success-msg { background:#dcfce7; border:1px solid #bbf7d0; color:#166534; font-size:11.5px; padding:8px 10px; border-radius:4px; margin-bottom:12px; line-height:1.5; }
        .error-msg { background:#fee2e2; border:1px solid #fca5a5; color:#b91c1c; font-size:11px; padding:6px 10px; border-radius:4px; margin-bottom:10px; }
        .btn-submit { width:100%; height:34px; margin-top:2px; background-color:#1d4ed8; color:#fff; font-size:13.5px; font-weight:600; border:none; border-radius:4px; cursor:pointer; transition:background-color .15s; }
        .btn-submit:hover { background-color:#1e40af; }
        .back-link { text-align:center; margin-top:10px; }
        .back-link a { font-size:11px; color:#2563eb; text-decoration:none; }
        .back-link a:hover { text-decoration:underline; }
        .copyright { text-align:center; font-size:9.5px; color:#999; margin-top:14px; }
    </style>
</head>
<body>
    <div class="card-wrap">
        <div class="logo-panel">
            <img src="{{ asset('images/cbma-logo.png') }}" alt="CBMA Logo">
        </div>

        <div class="form-panel">
            <h1>CBMA System</h1>
            <p class="sub">Academic Coordination and Instructional Management</p>
            <p class="desc">I-enter ang iyong email address at magpapadala kami ng link para ma-reset ang iyong password.</p>

            @if(session('success'))
                <div class="success-msg">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="error-msg">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="field">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email"
                        value="{{ old('email') }}"
                        placeholder="Enter your email address"
                        required>
                </div>

                <button type="submit" class="btn-submit">Send Reset Link</button>
            </form>

            <div class="back-link">
                <a href="{{ url('/login') }}">← Back to Login</a>
            </div>

            <p class="copyright">© 2026 CBMA System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>