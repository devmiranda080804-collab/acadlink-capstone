{{-- Icon buttons, the Details popover, Version History and Convert to Word, shared by the Faculty CMS and the System CMS. --}}
<style>
    .icon-btn { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; padding: 0; background: #fff; border: 1px solid #d0d0d0; border-radius: 5px; color: #444; cursor: pointer; text-decoration: none; }
    .icon-btn:hover { background: #f5f5f5; }
    .icon-btn-active { color: #065f46; border-color: #6ee7b7; background: #d1fae5; }
    .icon-btn-active:hover { background: #a7f3d0; }
    .icon-btn-danger { color: #b91c1c; border-color: #fca5a5; }
    .icon-btn-danger:hover { background: #fee2e2; }
    .icon-btn-plain { width: 24px; height: 24px; border-color: transparent; background: transparent; color: #777; border-radius: 50%; }
    .icon-btn-plain:hover, .icon-btn-plain[aria-expanded="true"] { background: #eef2ff; color: #0f2557; }
    .details-popover { display: none; position: fixed; z-index: 998; width: 300px; max-width: calc(100vw - 16px); background: #fff; border: 1px solid #e0e0e0; border-radius: 10px; padding: 14px 16px; box-shadow: 0 8px 28px rgba(0,0,0,0.18); font-family: Arial, sans-serif; }
    .details-popover.open { display: block; }
    .details-popover-heading { font-size: 13px; font-weight: 700; color: #1a1a2e; margin-bottom: 10px; }
    .details-popover label { display: block; font-size: 11.5px; font-weight: 700; color: #333; margin-bottom: 4px; }
    .details-popover input, .details-popover textarea { display: block; width: 100%; box-sizing: border-box; margin-bottom: 10px; padding: 7px 9px; border: 1px solid #ccc; border-radius: 5px; font-size: 12.5px; font-family: Arial, sans-serif; outline: none; }
    .details-popover input:focus, .details-popover textarea:focus { border-color: #0f2557; }
    .details-popover textarea { resize: vertical; min-height: 64px; }
    .details-popover-actions { display: flex; justify-content: flex-end; gap: 6px; }
    .details-popover-actions button { font-size: 12px; font-weight: 600; padding: 6px 14px; border-radius: 5px; cursor: pointer; font-family: Arial, sans-serif; border: 1px solid #ccc; background: #fff; color: #444; }
    .details-popover-actions .details-save { background: #0f2557; border-color: #0f2557; color: #fff; }
    .details-popover-actions .details-save:hover { background: #1a3a7a; }
    .history-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 999; align-items: center; justify-content: center; }
    .history-overlay.open { display: flex; }
    .history-box { background: #fff; border-radius: 10px; padding: 22px 24px; width: 520px; max-width: 95vw; font-family: Arial, sans-serif; }
    .history-heading { font-size: 15px; font-weight: 700; color: #1a1a2e; margin-bottom: 4px; }
    .history-note { font-size: 11.5px; color: #777; margin-bottom: 12px; line-height: 1.5; }
    .history-list { max-height: 340px; overflow-y: auto; }
    .history-row { display: flex; align-items: center; gap: 10px; padding: 9px 0; border-top: 1px solid #f0f0f0; }
    .history-info { flex: 1; min-width: 0; font-size: 12.5px; font-weight: 600; color: #222; }
    .history-info small { display: block; font-size: 10.5px; font-weight: 400; color: #888; margin-top: 2px; }
    .history-row a, .history-row button, .history-close { background: #fff; border: 1px solid #d0d0d0; color: #333; font-size: 11.5px; font-weight: 600; padding: 5px 10px; border-radius: 5px; cursor: pointer; text-decoration: none; font-family: Arial, sans-serif; }
    .history-row form { margin: 0; }
    .history-empty { padding: 26px 0; text-align: center; font-size: 12.5px; color: #999; }
    .history-actions { display: flex; justify-content: flex-end; margin-top: 14px; }
</style>

<div class="details-popover" id="details-popover" role="dialog" aria-label="Document details">
    <form method="POST" id="details-form">
        @csrf
        @method('PUT')
        <div class="details-popover-heading">Details</div>
        <label for="details-title">Title <span style="color:#ef4444">*</span></label>
        <input type="text" id="details-title" name="title" maxlength="255" required>
        <label for="details-description">Description</label>
        <textarea id="details-description" name="description" maxlength="1000"></textarea>
        <div class="details-popover-actions">
            <button type="button" onclick="closeDetails()">Cancel</button>
            <button type="submit" class="details-save">Save</button>
        </div>
    </form>
</div>

<div class="history-overlay" id="history-overlay">
    <div class="history-box">
        <div class="history-heading">Version History</div>
        <div class="history-note">Earlier saves of <strong id="history-title"></strong>. The latest {{ \App\Models\FileVersion::KEEP }} are kept. Ask others to close the document before you restore a version.</div>
        <div class="history-list" id="history-list"></div>
        <div class="history-actions"><button type="button" class="history-close" onclick="closeHistory()">Close</button></div>
    </div>
</div>

<script>
    var detailsButton = null;

    // Sits under its button and inside its card's left edge, so it doesn't cover the sidebar,
    // and stays on screen near the edges of the window.
    function placeDetails() {
        var popover = document.getElementById('details-popover');
        var button = detailsButton.getBoundingClientRect();
        var card = detailsButton.closest('.module-card, .doc-card').getBoundingClientRect();
        var left = Math.min(Math.max(button.right - popover.offsetWidth, card.left), window.innerWidth - popover.offsetWidth - 8);
        var top = button.bottom + 6;
        if (top + popover.offsetHeight > window.innerHeight - 8) {
            top = Math.max(8, button.top - popover.offsetHeight - 6);
        }
        popover.style.left = Math.max(8, left) + 'px';
        popover.style.top = top + 'px';
    }

    function openDetails(button) {
        var same = detailsButton === button;
        closeDetails();
        if (same) return;

        document.getElementById('details-form').action = button.dataset.url;
        document.getElementById('details-title').value = button.dataset.title;
        document.getElementById('details-description').value = button.dataset.description || '';
        document.getElementById('details-popover').classList.add('open');
        button.setAttribute('aria-expanded', 'true');
        detailsButton = button;
        placeDetails();
        document.getElementById('details-title').focus();
    }

    function closeDetails() {
        if (!detailsButton) return;
        document.getElementById('details-popover').classList.remove('open');
        detailsButton.setAttribute('aria-expanded', 'false');
        detailsButton = null;
    }

    document.addEventListener('click', function (e) {
        if (detailsButton && !e.target.closest('#details-popover') && !detailsButton.contains(e.target)) closeDetails();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape' || !detailsButton) return;
        var button = detailsButton;
        closeDetails();
        button.focus();
    });
    window.addEventListener('resize', function () { if (detailsButton) placeDetails(); });
    window.addEventListener('scroll', function () { if (detailsButton) placeDetails(); }, true);

    function closeHistory() { document.getElementById('history-overlay').classList.remove('open'); }
    document.getElementById('history-overlay').addEventListener('click', function (e) { if (e.target === this) closeHistory(); });

    async function openHistory(button) {
        var list = document.getElementById('history-list');
        document.getElementById('history-title').textContent = button.dataset.title;
        list.innerHTML = '<div class="history-empty">Loading...</div>';
        document.getElementById('history-overlay').classList.add('open');

        try {
            var response = await fetch(button.dataset.url, { headers: { Accept: 'application/json' } });
            if (!response.ok) throw new Error();
            var versions = await response.json();
        } catch (e) {
            list.innerHTML = '<div class="history-empty">The history could not be loaded. Please try again.</div>';
            return;
        }

        list.innerHTML = '';
        if (!versions.length) {
            list.innerHTML = '<div class="history-empty">No earlier versions yet. They appear here after the document has been edited and saved.</div>';
            return;
        }

        var token = document.querySelector('meta[name="csrf-token"]').content;
        versions.forEach(function (version) {
            var row = document.createElement('div');
            row.className = 'history-row';

            var info = document.createElement('div');
            info.className = 'history-info';
            info.textContent = version.saved_at;
            var detail = document.createElement('small');
            detail.textContent = (version.editor ? version.editor + ' • ' : '') + version.size;
            info.appendChild(detail);

            var download = document.createElement('a');
            download.href = version.download_url;
            download.textContent = 'Download';

            var form = document.createElement('form');
            form.method = 'POST';
            form.action = version.restore_url;
            form.setAttribute('data-confirm', 'Restore this version? The current one stays in the history.');
            form.setAttribute('data-confirm-title', 'Restore version?');
            form.setAttribute('data-confirm-button', 'Restore');
            var csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = token;
            var restore = document.createElement('button');
            restore.type = 'submit';
            restore.textContent = 'Restore';
            form.append(csrf, restore);

            row.append(info, download, form);
            list.appendChild(row);
        });
    }

    // The editor service converts in the background, so this asks again until it's done.
    async function convertDocument(button) {
        var label = button.textContent;
        button.disabled = true;
        button.textContent = 'Converting...';
        var token = document.querySelector('meta[name="csrf-token"]').content;

        try {
            for (var attempt = 0; attempt < 90; attempt++) {
                var response = await fetch(button.dataset.url, {
                    method: 'POST',
                    headers: { Accept: 'application/json', 'X-CSRF-TOKEN': token },
                });
                var result = await response.json();
                if (!response.ok) throw new Error(result.message);
                if (result.redirect) {
                    window.location.href = result.redirect;
                    return;
                }
                await new Promise(function (resolve) { setTimeout(resolve, 2000); });
            }
            throw new Error('The conversion is taking too long. Please try again.');
        } catch (e) {
            button.disabled = false;
            button.textContent = label;
            if (window.confirmAction) {
                window.confirmAction(e.message || 'The file could not be converted.', function () {}, { title: 'Could not convert', confirmText: 'OK' });
            } else {
                alert(e.message || 'The file could not be converted.');
            }
        }
    }
</script>
