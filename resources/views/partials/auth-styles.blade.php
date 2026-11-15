{{-- Look of the sign-in pages (login, forgot / reset / change password): a CBMA panel beside the form. --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    html, body { width: 100%; min-height: 100%; font-family: 'Plus Jakarta Sans', Arial, sans-serif; }

    .page-bg {
        position: relative; z-index: 1;
        width: 100%; min-height: 100vh;
        display: flex; align-items: center; justify-content: center;
        padding: 24px 16px;
    }

    /* ── The card: a CBMA panel on the left, the sign-in form on the right ── */
    .card-wrap {
        position: relative; display: flex; align-items: stretch;
        width: 100%; max-width: 860px; min-height: 500px;
        border-radius: 22px; overflow: hidden; background: #fff;
        box-shadow: 0 30px 80px rgba(3, 10, 30, .55), 0 0 0 1px rgba(255, 255, 255, .06);
        animation: card-in .55s cubic-bezier(.2, .8, .2, 1) both;
    }
    @keyframes card-in { from { opacity: 0; transform: translateY(18px) scale(.985); } to { opacity: 1; transform: none; } }

    .logo-panel {
        position: relative; width: 44%; flex-shrink: 0; overflow: hidden;
        display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;
        padding: 44px 34px 92px; color: #fff;
        background:
            radial-gradient(circle at 50% 38%, rgba(242, 201, 76, .16) 0, rgba(242, 201, 76, 0) 46%),
            repeating-linear-gradient(45deg, rgba(255, 255, 255, .035) 0 2px, transparent 2px 22px),
            linear-gradient(160deg, #1b3d7a 0%, #0f2557 55%, #0b1d47 100%);
    }
    /* Soft rings behind the seal. */
    .logo-panel::before, .logo-panel::after {
        content: ""; position: absolute; left: 50%; top: 40%; border-radius: 50%; transform: translate(-50%, -50%);
        border: 1px solid rgba(255, 255, 255, .07); pointer-events: none;
    }
    .logo-panel::before { width: 300px; height: 300px; }
    .logo-panel::after { width: 400px; height: 400px; border-color: rgba(255, 255, 255, .045); }

    .seal {
        position: relative; z-index: 1; width: 156px; height: 156px; border-radius: 50%; padding: 6px;
        background: conic-gradient(from 210deg, #f2c94c, #c99a1e, #f7dd8a, #c99a1e, #f2c94c);
        box-shadow: 0 0 0 8px rgba(242, 201, 76, .10), 0 18px 40px rgba(0, 0, 0, .35);
        animation: seal-in .7s .1s cubic-bezier(.2, .8, .2, 1) both;
    }
    .seal img { width: 100%; height: 100%; display: block; object-fit: contain; border-radius: 50%; background: #0b1d47; }
    @keyframes seal-in { from { opacity: 0; transform: scale(.9); } to { opacity: 1; transform: none; } }

    .brand { position: relative; z-index: 1; margin-top: 26px; }
    .brand-name { font-size: 26px; font-weight: 800; letter-spacing: -.3px; line-height: 1.15; }
    .brand-bar { width: 44px; height: 3px; border-radius: 2px; background: #f2c94c; margin: 12px auto; }
    .brand-tag { font-size: 13px; line-height: 1.55; color: rgba(255, 255, 255, .78); max-width: 280px; margin: 0 auto; }

    /* The college's name sits in the CBMA panel here, so the one on the background is not repeated behind the card. */
    .cbma-bg-text { display: none; }
    .college-line { position: absolute; z-index: 1; left: 24px; right: 24px; bottom: 22px; font-size: 10.5px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; line-height: 1.5; color: rgba(255, 255, 255, .62); }
    .college-line span { display: block; font-weight: 600; letter-spacing: 2px; color: rgba(242, 201, 76, .75); }

    .logo-panel .stripe { position: absolute; left: 0; right: 0; bottom: 0; height: 5px; background: linear-gradient(90deg, #C12035 0 33.3%, #f2c94c 33.3% 66.6%, #273F89 66.6% 100%); }

    /* ── Form ── */
    .form-panel { flex: 1; display: flex; flex-direction: column; justify-content: center; padding: 48px 52px 30px; background: #fff; }
    .form-panel form { width: 100%; max-width: 360px; margin: 0 auto; }

    .eyebrow { font-size: 11.5px; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; color: #1b3d7a; }
    .form-panel h1 { font-size: 27px; font-weight: 800; letter-spacing: -.4px; color: #14213d; margin: 6px 0 6px; }
    .form-panel .sub { font-size: 13.5px; color: #667085; line-height: 1.5; margin-bottom: 26px; }

    .field { margin-bottom: 16px; }
    .field label { display: block; font-size: 13px; font-weight: 700; color: #344054; margin-bottom: 7px; }

    .input-wrap { position: relative; }
    .input-wrap .lead {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        width: 18px; height: 18px; color: #98a2b3; pointer-events: none; transition: color .15s;
    }
    .field input {
        width: 100%; height: 46px; padding: 0 14px 0 44px;
        border: 1.5px solid #e1e6ef; border-radius: 12px; background: #f8fafc;
        font-family: inherit; font-size: 14px; color: #14213d; outline: none;
        transition: border-color .15s, box-shadow .15s, background-color .15s;
    }
    .field input::placeholder { color: #a5adbb; font-size: 13.5px; }
    .field input:hover { border-color: #cdd5e3; }
    .field input:focus { border-color: #1b3d7a; background: #fff; box-shadow: 0 0 0 4px rgba(27, 61, 122, .12); }
    .input-wrap:focus-within .lead { color: #1b3d7a; }
    .field input.invalid { border-color: #ef4444; }

    .error-msg {
        display: none; align-items: flex-start; gap: 10px;
        background: #fef2f2; border: 1px solid #fecaca; border-left: 4px solid #dc2626;
        color: #b91c1c; font-size: 13px; line-height: 1.45;
        padding: 10px 12px; border-radius: 10px; margin-bottom: 18px;
    }
    .error-msg svg { width: 17px; height: 17px; flex-shrink: 0; margin-top: 1px; }

    .btn-sign-in {
        width: 100%; height: 48px; margin-top: 6px;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        background: linear-gradient(135deg, #1b3d7a 0%, #0f2557 100%); color: #fff;
        font-family: inherit; font-size: 15px; font-weight: 700; letter-spacing: .2px;
        border: none; border-radius: 12px; cursor: pointer;
        box-shadow: 0 10px 22px rgba(15, 37, 87, .28);
        transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
    }
    .btn-sign-in svg { width: 18px; height: 18px; transition: transform .18s ease; }
    .btn-sign-in:hover { filter: brightness(1.08); box-shadow: 0 14px 28px rgba(15, 37, 87, .34); }
    .btn-sign-in:hover svg { transform: translateX(3px); }
    .btn-sign-in:active { transform: translateY(1px); }
    .btn-sign-in:focus-visible { outline: 3px solid #f2c94c; outline-offset: 2px; }

    .forgot { text-align: center; margin-top: 16px; }
    .forgot a { font-size: 13px; font-weight: 600; color: #1b3d7a; text-decoration: none; }
    .forgot a:hover { text-decoration: underline; }

    .copyright { text-align: center; font-size: 11.5px; color: #98a2b3; margin-top: 30px; padding-top: 16px; border-top: 1px solid #eef1f6; }

    /* Show/hide password: an eye button inside the password box. */
    .pw-wrap { position: relative; }
    .field .pw-wrap input { padding-right: 46px; }
    .pw-toggle {
        position: absolute; top: 0; right: 0; height: 46px; width: 46px;
        display: flex; align-items: center; justify-content: center;
        background: none; border: none; color: #98a2b3; cursor: pointer; border-radius: 0 12px 12px 0;
    }
    .pw-toggle:hover { color: #1b3d7a; }
    .pw-toggle:focus-visible { outline: 2px solid #2563eb; outline-offset: -4px; }
    .pw-toggle svg { width: 18px; height: 18px; }
    .pw-toggle .pw-eye-off { display: none; }
    .pw-toggle[aria-pressed="true"] .pw-eye { display: none; }
    .pw-toggle[aria-pressed="true"] .pw-eye-off { display: block; }

    /* ── Shared by the other sign-in pages ── */
    .success-msg {
        display: flex; align-items: flex-start; gap: 10px;
        background: #f0fdf4; border: 1px solid #bbf7d0; border-left: 4px solid #16a34a;
        color: #166534; font-size: 13px; line-height: 1.5;
        padding: 10px 12px; border-radius: 10px; margin-bottom: 18px;
    }
    .success-msg svg { width: 17px; height: 17px; flex-shrink: 0; margin-top: 1px; }
    .hint { display: block; font-size: 12px; color: #98a2b3; margin-top: 6px; }
    .back-link { text-align: center; margin-top: 16px; }
    .back-link a { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #1b3d7a; text-decoration: none; }
    .back-link a svg { width: 15px; height: 15px; transition: transform .18s ease; }
    .back-link a:hover { text-decoration: underline; }
    .back-link a:hover svg { transform: translateX(-3px); }

    /* ── Phones and small windows: the CBMA panel becomes a header above the form ── */
    @media (max-width: 760px) {
        .card-wrap { flex-direction: column; max-width: 440px; min-height: 0; border-radius: 18px; }
        .logo-panel { width: 100%; flex-direction: row; text-align: left; gap: 16px; padding: 22px 22px 26px; }
        .logo-panel::before, .logo-panel::after { display: none; }
        .seal { width: 68px; height: 68px; padding: 3px; box-shadow: 0 0 0 4px rgba(242, 201, 76, .10); }
        .brand { margin-top: 0; }
        .brand-name { font-size: 19px; }
        .brand-bar { margin: 7px 0; width: 32px; }
        .brand-tag { font-size: 12px; margin: 0; }
        .college-line { display: none; }
        .form-panel { padding: 28px 22px 22px; }
        .form-panel h1 { font-size: 23px; }
    }

    @media (prefers-reduced-motion: reduce) {
        .card-wrap, .seal { animation: none; }
        .btn-sign-in, .btn-sign-in svg { transition: none; }
    }
</style>
