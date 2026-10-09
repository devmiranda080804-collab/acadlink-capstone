{{-- Loading indicator, toast notifications, and confirm dialog shared by every page.
     Included once, just before </body>. Pages call window.showToast(), window.showLoading()
     and window.hideLoading() directly; forms get a loading state automatically, and a form
     with data-confirm="Message" asks for confirmation before submitting. --}}
<style>
    .sf-progress { position: fixed; top: 0; left: 0; height: 3px; width: 0; background: #2563eb; z-index: 10003; opacity: 0; pointer-events: none; }
    .sf-progress.active { opacity: 1; width: 80%; transition: width 6s cubic-bezier(.1, .7, .2, 1); }
    .sf-progress.done { opacity: 0; width: 100%; transition: width .2s ease, opacity .3s ease .2s; }

    .sf-overlay { display: none; position: fixed; inset: 0; background: rgba(15, 37, 87, 0.18); z-index: 10000; align-items: center; justify-content: center; }
    .sf-overlay.open { display: flex; }
    .sf-loading-box { display: flex; align-items: center; gap: 12px; background: #fff; border-radius: 10px; padding: 16px 22px; box-shadow: 0 8px 28px rgba(0, 0, 0, 0.2); font: 600 13px Arial, sans-serif; color: #1a1a2e; }
    .sf-spinner { width: 22px; height: 22px; border: 3px solid #dbe3f5; border-top-color: #0f2557; border-radius: 50%; animation: sf-spin .8s linear infinite; flex-shrink: 0; }
    @keyframes sf-spin { to { transform: rotate(360deg); } }

    .sf-toasts { position: fixed; top: 16px; right: 16px; z-index: 10002; display: flex; flex-direction: column; gap: 8px; width: 360px; max-width: calc(100vw - 32px); }
    .sf-toast { display: flex; align-items: flex-start; gap: 10px; background: #fff; border-left: 4px solid #2563eb; border-radius: 8px; padding: 12px 12px 12px 14px; box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15); font: 13px/1.45 Arial, sans-serif; color: #1a1a2e; animation: sf-toast-in .2s ease; }
    .sf-toast.success { border-left-color: #16a34a; }
    .sf-toast.error { border-left-color: #dc2626; }
    .sf-toast-icon { font-weight: 700; flex-shrink: 0; }
    .sf-toast.success .sf-toast-icon { color: #16a34a; }
    .sf-toast.error .sf-toast-icon { color: #dc2626; }
    .sf-toast.info .sf-toast-icon { color: #2563eb; }
    .sf-toast-msg { flex: 1; word-break: break-word; }
    .sf-toast-close { background: none; border: none; color: #999; font-size: 17px; line-height: 1; cursor: pointer; padding: 0 2px; }
    .sf-toast-close:hover { color: #333; }
    .sf-toast.leaving { opacity: 0; transform: translateX(12px); transition: opacity .2s, transform .2s; }
    @keyframes sf-toast-in { from { opacity: 0; transform: translateX(12px); } to { opacity: 1; transform: none; } }

    .sf-confirm { display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.45); z-index: 10001; align-items: center; justify-content: center; }
    .sf-confirm.open { display: flex; }
    .sf-confirm-box { background: #fff; border-radius: 10px; padding: 24px 26px; width: 380px; max-width: 92vw; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25); font-family: Arial, sans-serif; }
    .sf-confirm-title { font-size: 15px; font-weight: 700; color: #1a1a2e; margin-bottom: 8px; }
    .sf-confirm-msg { font-size: 13px; color: #555; line-height: 1.5; }
    .sf-confirm-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 20px; }
    .sf-btn { font: 600 12.5px Arial, sans-serif; padding: 8px 18px; border-radius: 5px; cursor: pointer; }
    .sf-btn-cancel { background: #fff; border: 1px solid #ccc; color: #444; }
    .sf-btn-danger { background: #ef4444; border: none; color: #fff; }

    /* The page-level success banners are replaced by the toast below. */
    .alert-success { display: none !important; }
</style>

<div class="sf-progress" id="sf-progress"></div>
<div class="sf-overlay" id="sf-overlay" aria-live="assertive">
    <div class="sf-loading-box"><span class="sf-spinner"></span><span id="sf-loading-text">Processing…</span></div>
</div>
<div class="sf-toasts" id="sf-toasts" role="status" aria-live="polite"></div>
<div class="sf-confirm" id="sf-confirm">
    <div class="sf-confirm-box" role="dialog" aria-modal="true" aria-labelledby="sf-confirm-title">
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
    var ICONS = { success: '✓', error: '!', info: 'i' };

    window.showToast = function (message, type, timeout) {
        type = ICONS[type] ? type : 'info';
        var toast = document.createElement('div');
        toast.className = 'sf-toast ' + type;
        toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
        var icon = document.createElement('span');
        icon.className = 'sf-toast-icon';
        icon.textContent = ICONS[type];
        var msg = document.createElement('span');
        msg.className = 'sf-toast-msg';
        msg.textContent = message;
        var close = document.createElement('button');
        close.type = 'button';
        close.className = 'sf-toast-close';
        close.setAttribute('aria-label', 'Dismiss');
        close.textContent = '×';
        toast.append(icon, msg, close);
        toasts.appendChild(toast);

        var dismiss = function () {
            toast.classList.add('leaving');
            setTimeout(function () { toast.remove(); }, 220);
        };
        close.addEventListener('click', dismiss);
        // Errors stay a little longer so they can be read.
        setTimeout(dismiss, timeout || (type === 'error' ? 8000 : 4500));
    };

    // Full-screen "Processing…" for actions that save or change something.
    var loadingTimer = null;
    window.showLoading = function (text) {
        document.getElementById('sf-loading-text').textContent = text || 'Processing…';
        overlay.classList.add('open');
        clearTimeout(loadingTimer);
        // Never trap the page if a response doesn't navigate (e.g. a file download).
        loadingTimer = setTimeout(window.hideLoading, 20000);
    };
    window.hideLoading = function () {
        overlay.classList.remove('open');
        clearTimeout(loadingTimer);
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
    var pendingConfirm = null;
    window.confirmAction = function (message, onConfirm, options) {
        options = options || {};
        document.getElementById('sf-confirm-title').textContent = options.title || 'Are you sure?';
        document.getElementById('sf-confirm-msg').textContent = message;
        document.getElementById('sf-confirm-ok').textContent = options.confirmText || 'Confirm';
        pendingConfirm = onConfirm;
        confirmBox.classList.add('open');
        document.getElementById('sf-confirm-ok').focus();
    };
    function closeConfirm() {
        pendingConfirm = null;
        confirmBox.classList.remove('open');
    }
    document.getElementById('sf-confirm-cancel').addEventListener('click', closeConfirm);
    document.getElementById('sf-confirm-ok').addEventListener('click', function () {
        var action = pendingConfirm;
        closeConfirm();
        if (action) action();
    });
    confirmBox.addEventListener('click', function (e) { if (e.target === confirmBox) closeConfirm(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && confirmBox.classList.contains('open')) closeConfirm(); });

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
            showToast(@json($errors->first()), 'error');
        }
    @endif
})();
</script>
