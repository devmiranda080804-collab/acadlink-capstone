{{-- Eye buttons on the sign-in pages: show the typed password; press again to hide it. --}}
<script>
    (function () {
        document.querySelectorAll('.pw-toggle').forEach(function (toggle) {
            var input = document.getElementById(toggle.getAttribute('aria-controls'));
            if (!input) return;
            toggle.addEventListener('click', function () {
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                toggle.setAttribute('aria-pressed', show ? 'true' : 'false');
                toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
                toggle.title = show ? 'Hide password' : 'Show password';
                input.focus();
            });
            // Never send the form with a password showing as text.
            if (input.form) input.form.addEventListener('submit', function () { input.type = 'password'; });
        });
    })();
</script>
