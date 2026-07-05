<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – CBMA System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            width: 100%;
            height: 100%;
            font-family: Arial, sans-serif;
        }

        .page-bg {
            position: relative;
            width: 100%;
            height: 100vh;
            background-color: #4a6b4a;
            background-image: url('{{ asset("images/bg-campus.jpg") }}');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .page-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.28);
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

        /* Left blue logo panel */
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

        /* Right white form panel */
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
            background-color: #1d4ed8;
            color: #fff;
            font-size: 13.5px;
            font-weight: 600;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            letter-spacing: 0.2px;
            transition: background-color .15s;
        }

        .btn-sign-in:hover { background-color: #1e40af; }

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
    </style>
</head>
<body>

    <div class="page-bg">
        <div class="card-wrap">

            {{-- Logo --}}
            <div class="logo-panel">
                <img src="{{ asset('images/cbma-logo.png') }}" alt="CBMA Logo">
            </div>

            {{-- Form --}}
            <div class="form-panel">
                <form method="POST" action="{{ route('login.submit') }}">
                @csrf

                <h1>CBMA System</h1>
                <p class="sub">Academic Coordination and Instructional Management</p>

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
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        required>
                </div>

                <button type="submit" class="btn-sign-in">
                    Sign In
                </button>

               <div class="forgot"><a href="{{ route('password.request') }}">Forgot your password?</a></div>

                <p class="copyright">
                    © 2026 CBMA System. All rights reserved.
                </p>
            </form>


        </div>
    </div>

</body>
</html>