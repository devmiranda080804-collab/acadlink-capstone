{{-- Loading indicator, toast notifications, and confirm dialog shared by every page.
     Included once, just before </body>. Pages call window.showToast(), window.showLoading()
     and window.hideLoading() directly; forms get a loading state automatically, and a form
     with data-confirm="Message" asks for confirmation before submitting. --}}
<style>
    :root {
        --sf-navy: #0f2557;
        --sf-blue: #2563eb;
        --sf-green: #16a34a;
        --sf-red: #dc2626;
        --sf-amber: #d97706;
        --sf-ink: #1a1a2e;
        --sf-muted: #5b6377;
        --sf-ease: cubic-bezier(.2, .9, .3, 1.15);
        --sf-ease-out: cubic-bezier(.2, .8, .2, 1);
    }

    /* ── Top progress bar (background requests, filters, search) ── */
    .sf-progress { position: fixed; top: 0; left: 0; height: 3px; width: 0; z-index: 10003; opacity: 0; pointer-events: none;
        background: linear-gradient(90deg, var(--sf-navy), var(--sf-blue), #60a5fa); box-shadow: 0 0 10px rgba(37, 99, 235, .55); border-radius: 0 3px 3px 0; }
    .sf-progress::after { content: ""; position: absolute; inset: 0; background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .7), transparent); animation: sf-shimmer 1.1s linear infinite; }
    .sf-progress.active { opacity: 1; width: 82%; transition: width 6s cubic-bezier(.1, .7, .2, 1), opacity .15s; }
    .sf-progress.done { opacity: 0; width: 100%; transition: width .25s ease, opacity .35s ease .25s; }
    @keyframes sf-shimmer { from { transform: translateX(-100%); } to { transform: translateX(100%); } }

    /* ── Loading overlay (actions that save or change something) ── */
    .sf-overlay { display: none; position: fixed; inset: 0; z-index: 10000; align-items: center; justify-content: center;
        background: rgba(15, 37, 87, .22); backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(2px); opacity: 0; transition: opacity .2s ease; }
    .sf-overlay.open { display: flex; }
    .sf-overlay.visible { opacity: 1; }
    .sf-loading-box { display: flex; align-items: center; gap: 14px; background: #fff; border-radius: 14px; padding: 18px 24px 18px 20px; min-width: 240px; max-width: 86vw;
        box-shadow: 0 18px 50px rgba(15, 37, 87, .28), 0 2px 6px rgba(15, 37, 87, .08); font-family: "Segoe UI", Arial, sans-serif;
        transform: translateY(8px) scale(.96); transition: transform .28s var(--sf-ease); }
    .sf-overlay.visible .sf-loading-box { transform: none; }
    .sf-spinner { position: relative; width: 34px; height: 34px; flex-shrink: 0; }
    .sf-spinner::before, .sf-spinner::after { content: ""; position: absolute; inset: 0; border-radius: 50%; border: 3px solid transparent; }
    .sf-spinner::before { border-top-color: var(--sf-navy); border-right-color: var(--sf-navy); animation: sf-spin .9s cubic-bezier(.55, .15, .45, .85) infinite; }
    .sf-spinner::after { inset: 7px; border-bottom-color: var(--sf-blue); border-left-color: var(--sf-blue); animation: sf-spin .7s linear infinite reverse; }
    .sf-loading-text { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
    .sf-loading-title { font-size: 14px; font-weight: 600; color: var(--sf-ink); }
    .sf-loading-sub { font-size: 12px; color: var(--sf-muted); }
    .sf-loading-sub::after { content: ""; display: inline-block; width: 1.2em; text-align: left; animation: sf-dots 1.2s steps(4, end) infinite; }
    @keyframes sf-spin { to { transform: rotate(360deg); } }
    @keyframes sf-dots { 0% { content: ""; } 25% { content: "."; } 50% { content: ".."; } 75% { content: "..."; } }

    /* ── Toasts (system responses) ── */
    .sf-toasts { position: fixed; top: 18px; right: 18px; z-index: 10002; display: flex; flex-direction: column; gap: 10px; width: 370px; max-width: calc(100vw - 32px); pointer-events: none; }
    .sf-toast { --sf-accent: var(--sf-blue); position: relative; display: flex; align-items: flex-start; gap: 12px; overflow: hidden; pointer-events: auto;
        background: #fff; border-radius: 12px; padding: 14px 12px 16px 14px; border: 1px solid rgba(15, 37, 87, .08);
        box-shadow: 0 12px 32px rgba(15, 37, 87, .16), 0 2px 6px rgba(15, 37, 87, .06); font-family: "Segoe UI", Arial, sans-serif; color: var(--sf-ink);
        animation: sf-toast-in .42s var(--sf-ease) both; }
    .sf-toast.success { --sf-accent: var(--sf-green); }
    .sf-toast.error { --sf-accent: var(--sf-red); }
    .sf-toast.warning { --sf-accent: var(--sf-amber); }
    .sf-toast::before { content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: var(--sf-accent); }
    .sf-toast-icon { width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0; display: grid; place-items: center; color: #fff; background: var(--sf-accent);
        box-shadow: 0 0 0 5px color-mix(in srgb, var(--sf-accent) 14%, transparent); animation: sf-pop .5s var(--sf-ease) .12s both; }
    .sf-toast-icon svg { width: 16px; height: 16px; }
    .sf-toast.success .sf-toast-icon path { stroke-dasharray: 24; stroke-dashoffset: 24; animation: sf-draw .45s ease-out .3s forwards; }
    .sf-toast.error .sf-toast-icon { animation: sf-pop .5s var(--sf-ease) .12s both, sf-shake .4s ease .55s; }
    .sf-toast-body { flex: 1; min-width: 0; padding-top: 1px; }
    .sf-toast-title { font-size: 13.5px; font-weight: 700; line-height: 1.3; }
    .sf-toast-msg { font-size: 13px; line-height: 1.45; color: #3c4356; margin-top: 2px; word-break: break-word; white-space: pre-line; }
    .sf-toast-close { width: 26px; height: 26px; margin: -3px -2px 0 0; border-radius: 7px; border: none; background: none; color: #8a90a2; font-size: 18px; line-height: 1;
        cursor: pointer; flex-shrink: 0; transition: background .15s, color .15s; }
    .sf-toast-close:hover { background: #f0f2f7; color: var(--sf-ink); }
    .sf-toast-close:focus-visible { outline: 2px solid var(--sf-blue); outline-offset: 1px; }
    .sf-toast-timer { position: absolute; left: 0; bottom: 0; height: 3px; width: 100%; background: var(--sf-accent); opacity: .35; transform-origin: left;
        animation: sf-timer linear forwards; }
    .sf-toast:hover .sf-toast-timer { animation-play-state: paused; }
    .sf-toast.leaving { animation: sf-toast-out .32s var(--sf-ease-out) forwards; }
    @keyframes sf-toast-in { from { opacity: 0; transform: translateX(40px) scale(.96); } to { opacity: 1; transform: none; } }
    @keyframes sf-toast-out { to { opacity: 0; transform: translateX(40px) scale(.96); } }
    @keyframes sf-pop { from { transform: scale(0); } to { transform: scale(1); } }
    @keyframes sf-draw { to { stroke-dashoffset: 0; } }
    @keyframes sf-shake { 20%, 60% { transform: translateX(-3px); } 40%, 80% { transform: translateX(3px); } }
    @keyframes sf-timer { from { transform: scaleX(1); } to { transform: scaleX(0); } }

    /* ── Confirm dialog ── */
    .sf-confirm { display: none; position: fixed; inset: 0; z-index: 10001; align-items: center; justify-content: center; padding: 16px;
        background: rgba(10, 20, 45, .5); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px); opacity: 0; transition: opacity .2s ease; }
    .sf-confirm.open { display: flex; }
    .sf-confirm.visible { opacity: 1; }
    .sf-confirm-box { --sf-accent: var(--sf-red); position: relative; background: #fff; border-radius: 16px; padding: 26px 26px 22px; width: 400px; max-width: 100%;
        box-shadow: 0 24px 64px rgba(10, 20, 45, .35); font-family: "Segoe UI", Arial, sans-serif; text-align: center;
        transform: translateY(16px) scale(.94); opacity: 0; transition: transform .32s var(--sf-ease), opacity .2s ease; }
    .sf-confirm.visible .sf-confirm-box { transform: none; opacity: 1; }
    .sf-confirm-box.primary { --sf-accent: var(--sf-navy); }
    .sf-confirm-icon { width: 54px; height: 54px; margin: 0 auto 14px; border-radius: 50%; display: grid; place-items: center; color: var(--sf-accent);
        background: color-mix(in srgb, var(--sf-accent) 11%, #fff); box-shadow: 0 0 0 7px color-mix(in srgb, var(--sf-accent) 5%, #fff); }
    .sf-confirm.visible .sf-confirm-icon { animation: sf-pop .45s var(--sf-ease) .08s both; }
    .sf-confirm-icon svg { width: 26px; height: 26px; }
    .sf-confirm-title { font-size: 17px; font-weight: 700; color: var(--sf-ink); margin-bottom: 6px; }
    .sf-confirm-msg { font-size: 13.5px; color: var(--sf-muted); line-height: 1.55; white-space: pre-line; }
    .sf-confirm-actions { display: flex; gap: 10px; margin-top: 22px; }
    .sf-btn { flex: 1; font: 600 13px "Segoe UI", Arial, sans-serif; padding: 10px 18px; border-radius: 9px; cursor: pointer;
        transition: background .15s, box-shadow .15s, transform .1s; }
    .sf-btn:active { transform: scale(.97); }
    .sf-btn:focus-visible { outline: 3px solid color-mix(in srgb, var(--sf-accent) 35%, transparent); outline-offset: 2px; }
    .sf-btn-cancel { background: #fff; border: 1px solid #d5d9e3; color: #3c4356; }
    .sf-btn-cancel:hover { background: #f4f6fa; }
    .sf-btn-danger { background: var(--sf-accent); border: 1px solid var(--sf-accent); color: #fff; box-shadow: 0 6px 16px color-mix(in srgb, var(--sf-accent) 30%, transparent); }
    .sf-btn-danger:hover { background: color-mix(in srgb, var(--sf-accent) 86%, #000); }

    @media (max-width: 480px) {
        .sf-toasts { top: auto; bottom: 14px; right: 50%; transform: translateX(50%); }
        .sf-confirm-actions { flex-direction: column-reverse; }
    }

    @media (prefers-reduced-motion: reduce) {
        .sf-overlay, .sf-loading-box, .sf-confirm, .sf-confirm-box { transition: none; }
        .sf-toast, .sf-toast.leaving, .sf-toast-icon, .sf-confirm.visible .sf-confirm-icon { animation-duration: .01s; animation-delay: 0s; }
        .sf-toast.success .sf-toast-icon path { animation: none; stroke-dashoffset: 0; }
        .sf-progress::after, .sf-loading-sub::after { animation: none; }
    }

    /* The page-level success banners are replaced by the toast below. */
    .alert-success { display: none !important; }
</style>

<div class="sf-progress" id="sf-progress"></div>
<div class="sf-overlay" id="sf-overlay" aria-live="assertive">
    <div class="sf-loading-box" role="status">
        <span class="sf-spinner" aria-hidden="true"></span>
        <span class="sf-loading-text">
            <span class="sf-loading-title" id="sf-loading-text">Processing…</span>
            <span class="sf-loading-sub" id="sf-loading-sub">Please wait</span>
        </span>
    </div>
</div>
<div class="sf-toasts" id="sf-toasts" aria-live="polite"></div>
<div class="sf-confirm" id="sf-confirm">
    <div class="sf-confirm-box" id="sf-confirm-box" role="alertdialog" aria-modal="true" aria-labelledby="sf-confirm-title" aria-describedby="sf-confirm-msg">
        <div class="sf-confirm-icon" id="sf-confirm-icon" aria-hidden="true"></div>
        <div class="sf-confirm-title" id="sf-confirm-title">Are you sure?</div>
        <div class="sf-confirm-msg" id="sf-confirm-msg"></div>
        <div class="sf-confirm-actions">
            <button type="button" class="sf-btn sf-btn-cancel" id="sf-confirm-cancel">Cancel</button>
            <button type="button" class="sf-btn sf-btn-danger" id="sf-confirm-ok">Confirm</button>
        </div>
    </div>
</div>

<script>
(function () {
    var toasts = document.getElementById('sf-toasts');
    var overlay = document.getElementById('sf-overlay');
    var progress = document.getElementById('sf-progress');
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    var SVG = function (paths) {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">' + paths + '</svg>';
    };
    var TOAST_TYPES = {
        success: { title: 'Success', icon: SVG('<path d="M5 12.5l4.5 4.5L19 7.5"/>') },
        error: { title: 'Something went wrong', icon: SVG('<path d="M7 7l10 10M17 7L7 17"/>') },
        warning: { title: 'Please check', icon: SVG('<path d="M12 7v6M12 17h.01"/>') },
        info: { title: 'Notice', icon: SVG('<path d="M12 11v6M12 7h.01"/>') }
    };
    var MAX_TOASTS = 4;

    // Technical text that can still reach a toast (a dropped connection, or a page's own
    // "Request failed (500)" fallback) is reworded so the user knows what to do next.
    var STATUS_MESSAGES = {
        401: 'You have been signed out. Please log in again.',
        403: 'You don\'t have permission to do that.',
        404: 'This item could not be found. It may have been removed, so please refresh the page.',
        413: 'The file is too large to upload.',
        419: 'Your session timed out. Please refresh the page and try again.',
        429: 'Too many requests at once. Please wait a moment and try again.'
    };
    function humanizeMessage(message) {
        var text = String(message == null ? '' : message).trim();
        if (/^(Failed to fetch|NetworkError|Load failed|Network request failed)/i.test(text)) {
            return 'Can\'t reach the server. Please check your internet connection and try again.';
        }
        var code = text.match(/^(?:Request failed \((\d{3})\)|HTTP (\d{3}))\.?$/i);
        if (code) {
            var status = Number(code[1] || code[2]);
            return STATUS_MESSAGES[status] || (status >= 500
                ? 'Something went wrong on our side. Please try again in a moment.'
                : 'Your request couldn\'t be completed. Please refresh the page and try again.');
        }
        return text || 'Something went wrong. Please try again.';
    }

    window.humanizeMessage = humanizeMessage;

    window.showToast = function (message, type, timeout) {
        type = TOAST_TYPES[type] ? type : 'info';
        if (type === 'error') message = humanizeMessage(message);
        var duration = timeout || (type === 'error' ? 8000 : 4500); // errors stay longer so they can be read

        // Keep the stack short: drop the oldest when too many pile up.
        var current = toasts.querySelectorAll('.sf-toast:not(.leaving)');
        if (current.length >= MAX_TOASTS) current[0].dismiss();

        var toast = document.createElement('div');
        toast.className = 'sf-toast ' + type;
        toast.setAttribute('role', type === 'error' ? 'alert' : 'status');

        var icon = document.createElement('span');
        icon.className = 'sf-toast-icon';
        icon.innerHTML = TOAST_TYPES[type].icon;

        var body = document.createElement('div');
        body.className = 'sf-toast-body';
        var title = document.createElement('div');
        title.className = 'sf-toast-title';
        title.textContent = TOAST_TYPES[type].title;
        var msg = document.createElement('div');
        msg.className = 'sf-toast-msg';
        msg.textContent = message;
        body.append(title, msg);

        var close = document.createElement('button');
        close.type = 'button';
        close.className = 'sf-toast-close';
        close.setAttribute('aria-label', 'Dismiss');
        close.textContent = '×';

        var timer = document.createElement('span');
        timer.className = 'sf-toast-timer';
        timer.style.animationDuration = duration + 'ms';

        toast.append(icon, body, close, timer);
        toasts.appendChild(toast);

        var gone = false;
        toast.dismiss = function () {
            if (gone) return;
            gone = true;
            toast.classList.add('leaving');
            setTimeout(function () { toast.remove(); }, reduceMotion ? 0 : 320);
        };
        close.addEventListener('click', toast.dismiss);
        // The countdown bar drives auto-dismiss, so hovering (which pauses it) keeps the toast open.
        timer.addEventListener('animationend', toast.dismiss);
        if (reduceMotion) setTimeout(toast.dismiss, duration);
    };

    // Full-screen "Processing…" for actions that save or change something.
    var loadingTimer = null, slowTimer = null, loadingOn = false;
    window.showLoading = function (text) {
        document.getElementById('sf-loading-text').textContent = text || 'Processing…';
        document.getElementById('sf-loading-sub').textContent = 'Please wait';
        loadingOn = true;
        overlay.classList.add('open');
        requestAnimationFrame(function () { if (loadingOn) overlay.classList.add('visible'); });
        clearTimeout(loadingTimer);
        clearTimeout(slowTimer);
        slowTimer = setTimeout(function () {
            document.getElementById('sf-loading-sub').textContent = 'This is taking a little longer than usual';
        }, 7000);
        // Never trap the page if a response doesn't navigate (e.g. a file download).
        loadingTimer = setTimeout(window.hideLoading, 20000);
    };
    window.hideLoading = function () {
        loadingOn = false;
        overlay.classList.remove('visible');
        clearTimeout(loadingTimer);
        clearTimeout(slowTimer);
        setTimeout(function () {
            if (!loadingOn) overlay.classList.remove('open');
        }, reduceMotion ? 0 : 200);
    };

    // Thin top bar for background requests and plain navigation (filters, search).
    var pending = 0;
    function progressStart() {
        pending++;
        progress.classList.remove('done');
        void progress.offsetWidth;
        progress.classList.add('active');
    }
    function progressDone() {
        pending = Math.max(0, pending - 1);
        if (pending === 0) {
            progress.classList.remove('active');
            progress.classList.add('done');
        }
    }

    if (window.fetch) {
        var originalFetch = window.fetch;
        window.fetch = function () {
            progressStart();
            return originalFetch.apply(this, arguments).finally(progressDone);
        };
    }

    function startFormLoading(form) {
        if (form.hasAttribute('data-no-loading') || (form.target && form.target !== '_self')) return;
        var method = (form.getAttribute('method') || 'get').toLowerCase();
        if (method === 'get') {
            progressStart();
            return;
        }
        form.querySelectorAll('button[type=submit], input[type=submit]').forEach(function (btn) { btn.disabled = true; });
        showLoading(form.getAttribute('data-loading-text') || 'Processing…');
    }

    // Programmatic form.submit() skips the submit event, so cover it too.
    var nativeSubmit = HTMLFormElement.prototype.submit;
    HTMLFormElement.prototype.submit = function () {
        startFormLoading(this);
        return nativeSubmit.call(this);
    };

    // Confirm dialog for forms marked data-confirm="Message".
    var confirmBox = document.getElementById('sf-confirm');
    var confirmPanel = document.getElementById('sf-confirm-box');
    var okBtn = document.getElementById('sf-confirm-ok');
    var cancelBtn = document.getElementById('sf-confirm-cancel');
    var pendingConfirm = null, lastFocus = null, confirmOn = false;
    var CONFIRM_ICONS = {
        danger: SVG('<path d="M4 7h16M9 7V4.5h6V7M6.5 7l1 13h9l1-13M10 11v5M14 11v5"/>'),
        primary: SVG('<path d="M12 8v5M12 16.5h.01"/><circle cx="12" cy="12" r="9"/>')
    };
    // Destructive wording gets the red style; everything else the navy one.
    var DANGER_WORDS = /delete|remove|archive|discard|deactivate|reject|return|reset|log ?out|revoke|clear/i;

    window.confirmAction = function (message, onConfirm, options) {
        options = options || {};
        var title = options.title || 'Are you sure?';
        var confirmText = options.confirmText || 'Confirm';
        var tone = options.tone || (DANGER_WORDS.test(confirmText + ' ' + title) ? 'danger' : 'primary');
        document.getElementById('sf-confirm-title').textContent = title;
        document.getElementById('sf-confirm-msg').textContent = message;
        document.getElementById('sf-confirm-icon').innerHTML = CONFIRM_ICONS[tone] || CONFIRM_ICONS.danger;
        confirmPanel.classList.toggle('primary', tone !== 'danger');
        okBtn.textContent = confirmText;
        pendingConfirm = onConfirm;
        lastFocus = document.activeElement;
        confirmOn = true;
        confirmBox.classList.add('open');
        requestAnimationFrame(function () { if (confirmOn) confirmBox.classList.add('visible'); });
        okBtn.focus();
    };
    function closeConfirm() {
        pendingConfirm = null;
        confirmOn = false;
        confirmBox.classList.remove('visible');
        setTimeout(function () {
            if (!confirmOn) confirmBox.classList.remove('open');
        }, reduceMotion ? 0 : 200);
        if (lastFocus && lastFocus.focus) lastFocus.focus();
    }
    cancelBtn.addEventListener('click', closeConfirm);
    okBtn.addEventListener('click', function () {
        var action = pendingConfirm;
        closeConfirm();
        if (action) action();
    });
    confirmBox.addEventListener('click', function (e) { if (e.target === confirmBox) closeConfirm(); });
    document.addEventListener('keydown', function (e) {
        if (!confirmOn) return;
        if (e.key === 'Escape') closeConfirm();
        // Keep keyboard focus inside the dialog.
        if (e.key === 'Tab') {
            e.preventDefault();
            (document.activeElement === okBtn ? cancelBtn : okBtn).focus();
        }
    });

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form.hasAttribute('data-confirm') || form.dataset.confirmed === '1') return;
        e.preventDefault();
        e.stopImmediatePropagation();
        confirmAction(form.getAttribute('data-confirm'), function () {
            form.dataset.confirmed = '1';
            form.submit();
        }, { title: form.getAttribute('data-confirm-title') || 'Are you sure?', confirmText: form.getAttribute('data-confirm-button') || 'Confirm' });
    }, true);

    // Bubble phase: by now a page's own handler has had its chance to cancel the submit.
    document.addEventListener('submit', function (e) {
        if (!e.defaultPrevented) startFormLoading(e.target);
    });

    // Coming back via the browser's Back button can restore a frozen "loading" page.
    window.addEventListener('pageshow', function () {
        window.hideLoading();
        pending = 0;
        progress.classList.remove('active');
        document.querySelectorAll('button[type=submit][disabled], input[type=submit][disabled]').forEach(function (btn) { btn.disabled = false; });
    });

    // Responses from the server (after a redirect).
    @if(session('success'))
        showToast(@json(session('success')), 'success');
    @endif
    @if(session('error'))
        showToast(@json(session('error')), 'error');
    @endif
    @if(isset($errors) && $errors->any())
        // Pages without their own visible error message still get one.
        var shownOnPage = document.querySelector('.alert-error, .error-msg[style*="block"]')
            || Array.from(document.querySelectorAll('.modal-error')).some(function (el) { return el.textContent.trim() !== ''; });
        if (!shownOnPage) {
            // A form that needs fixing, not a system failure: "Please check", not "Something went wrong".
            showToast(@json($errors->first() . ($errors->count() > 1 ? ' (and ' . ($errors->count() - 1) . ' more to fix)' : '')), 'warning', 8000);
        }
    @endif
})();
</script>
