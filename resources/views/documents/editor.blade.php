<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} – CBMA System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; font-family: Arial, sans-serif; background: #f0f0f0; }
        body { display: flex; flex-direction: column; }
        .editor-bar { display: flex; align-items: center; gap: 14px; height: 46px; flex-shrink: 0; padding: 0 16px; background: #0f2557; color: #fff; }
        .editor-back { color: #c8d6ec; text-decoration: none; font-size: 12.5px; font-weight: 600; white-space: nowrap; }
        .editor-back:hover { color: #fff; }
        .editor-title { font-size: 13.5px; font-weight: 700; flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .editor-access { font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 10px; background: rgba(255,255,255,0.15); white-space: nowrap; }
        .editor-share { background: #fff; color: #0f2557; border: none; font-size: 11.5px; font-weight: 700; padding: 6px 12px; border-radius: 5px; cursor: pointer; white-space: nowrap; font-family: Arial, sans-serif; }
        .editor-share:hover { background: #e8edf7; }
        .editor-area { flex: 1; min-height: 0; }
        .editor-submit { background: #f5c242; color: #0f2557; border: none; font-size: 12px; font-weight: 700; padding: 7px 14px; border-radius: 6px; cursor: pointer; white-space: nowrap; font-family: Arial, sans-serif; }
        .editor-submit:hover { background: #ffd35c; }
        .editor-note { display: flex; align-items: flex-start; gap: 10px; flex-shrink: 0; padding: 9px 16px; background: #fef2f2; border-bottom: 1px solid #fecaca; color: #7f1d1d; font-size: 12.5px; line-height: 1.5; }
        .editor-note strong { white-space: nowrap; }
        .editor-note span { flex: 1; min-width: 0; }
        .editor-note button { background: none; border: none; color: #7f1d1d; font-size: 16px; line-height: 1; cursor: pointer; padding: 0 2px; }
        .es-overlay { display: none; position: fixed; inset: 0; z-index: 50; align-items: center; justify-content: center; padding: 16px; background: rgba(10, 20, 45, .5); }
        .es-overlay.open { display: flex; }
        .es-box { background: #fff; border-radius: 14px; padding: 24px; width: 420px; max-width: 100%; box-shadow: 0 24px 64px rgba(10, 20, 45, .35); text-align: center; }
        .es-title { font-size: 16px; font-weight: 700; color: #1a1a2e; margin-bottom: 6px; }
        .es-msg { font-size: 13px; color: #5b6377; line-height: 1.55; }
        .es-error { display: none; margin-top: 12px; padding: 8px 10px; border-radius: 7px; background: #fef2f2; color: #b91c1c; font-size: 12.5px; text-align: left; }
        .es-actions { display: flex; gap: 10px; margin-top: 20px; }
        .es-actions button { flex: 1; font: 600 13px Arial, sans-serif; padding: 10px 14px; border-radius: 8px; cursor: pointer; }
        .es-cancel { background: #fff; border: 1px solid #d5d9e3; color: #3c4356; }
        .es-ok { background: #0f2557; border: 1px solid #0f2557; color: #fff; }
        .es-ok:disabled, .es-cancel:disabled { opacity: .6; cursor: wait; }
        .es-spin { display: inline-block; width: 13px; height: 13px; margin-right: 6px; vertical-align: -2px; border: 2px solid rgba(255,255,255,.4); border-top-color: #fff; border-radius: 50%; animation: es-spin .8s linear infinite; }
        @keyframes es-spin { to { transform: rotate(360deg); } }
        .editor-error { display: none; max-width: 460px; margin: 80px auto; padding: 24px; background: #fff; border-radius: 10px; text-align: center; font-size: 13px; line-height: 1.6; color: #444; }
        .editor-error strong { display: block; font-size: 15px; color: #1a1a2e; margin-bottom: 8px; }
    </style>
</head>
<body>
    <div class="editor-bar">
        <a class="editor-back" href="{{ $backUrl }}">← {{ $backLabel }}</a>
        <div class="editor-title">{{ $title }}</div>
        <span class="editor-access">{{ $canEdit ? 'Changes save automatically' : 'View only' }}</span>
        @if($submitDraft ?? null)
            <button type="button" class="editor-submit" onclick="openSubmitDraft()">Submit to Dean</button>
        @endif
        @if($libraryModule ?? null)
            <button type="button" class="editor-share" data-programs="{{ implode(' ', $libraryModule->libraryPrograms(auth()->user())) }}" data-url="{{ url('/faculty/cms/' . $libraryModule->id . '/library') }}" data-title="{{ $libraryModule->title }}" @if($libraryModule->libraryEntry) data-entry="{{ json_encode($libraryModule->libraryEntry->only(['title', 'type', 'description', 'course_id', 'program'])) }}" @endif onclick="openLibraryShare(this)">{{ $libraryModule->libraryEntry ? 'Update in Library' : 'Share to Library' }}</button>
        @endif
    </div>
    @if($returnNote ?? null)
        <div class="editor-note" id="editor-note" role="note">
            <strong>Returned by {{ $returnNote['by'] }}:</strong>
            <span>{{ $returnNote['reason'] }} Make the changes here, then click <b>Submit to Dean</b>.</span>
            <button type="button" aria-label="Hide" onclick="document.getElementById('editor-note').remove()">×</button>
        </div>
    @endif
    <div class="editor-area"><div id="document-editor"></div></div>
    @if($submitDraft ?? null)
        <div class="es-overlay" id="es-overlay">
            <div class="es-box" role="alertdialog" aria-modal="true" aria-labelledby="es-title" aria-describedby="es-msg">
                <div class="es-title" id="es-title">Submit to the Dean?</div>
                <div class="es-msg" id="es-msg">Version {{ $submitDraft['version'] }} goes to the Dean for approval. Your latest changes are saved first; you don't need to download or upload anything.</div>
                <div class="es-error" id="es-error" role="alert"></div>
                <div class="es-actions">
                    <button type="button" class="es-cancel" id="es-cancel" onclick="closeSubmitDraft()">Keep editing</button>
                    <button type="button" class="es-ok" id="es-ok" onclick="submitDraft()">Submit</button>
                </div>
            </div>
        </div>
    @endif
    @if($libraryModule ?? null)
        {{-- The system's modal look, for the Share to Library dialog. --}}
        @include('partials.theme')
        @include('documents.partials.library-share')
    @endif
    <div class="editor-error" id="editor-error">
        <strong>The editor could not be loaded</strong>
        Please check your connection and try again. Your document is safe. If this keeps happening, contact the system administrator.
    </div>

    <script src="{{ $scriptUrl }}" onerror="showEditorError()"></script>
    <script>
        function showEditorError() {
            document.querySelector('.editor-area').style.display = 'none';
            document.getElementById('editor-error').style.display = 'block';
        }

        window.addEventListener('load', function () {
            if (!window.DocsAPI) {
                showEditorError();
                return;
            }
            new DocsAPI.DocEditor('document-editor', Object.assign(@json($config), { width: '100%', height: '100%' }));
        });
    </script>
    @if($submitDraft ?? null)
    <script>
        // Submit from inside the editor: ask the editor to save now, wait for the file
        // to reach AcadLink, then send it to the Dean.
        var DRAFT_URL = @json($submitDraft['url']);
        var DRAFT_CSRF = @json(csrf_token());
        var submitting = false;

        function openSubmitDraft() {
            document.getElementById('es-error').style.display = 'none';
            document.getElementById('es-overlay').classList.add('open');
            document.getElementById('es-ok').focus();
        }
        function closeSubmitDraft() {
            if (submitting) return;
            document.getElementById('es-overlay').classList.remove('open');
        }
        document.getElementById('es-overlay').addEventListener('click', function (e) { if (e.target === this) closeSubmitDraft(); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeSubmitDraft(); });

        function draftRequest(path, method) {
            return fetch(DRAFT_URL + path, { method: method, credentials: 'same-origin', headers: { Accept: 'application/json', 'X-CSRF-TOKEN': DRAFT_CSRF } });
        }
        function setBusy(text) {
            var ok = document.getElementById('es-ok');
            ok.innerHTML = text ? '<span class="es-spin" aria-hidden="true"></span>' + text : 'Submit';
            ok.disabled = document.getElementById('es-cancel').disabled = !!text;
        }

        async function submitDraft() {
            if (submitting) return;
            submitting = true;
            var error = document.getElementById('es-error');
            error.style.display = 'none';
            try {
                setBusy('Saving your changes…');
                var ask = await draftRequest('/prepare', 'POST');
                if (ask.ok) {
                    var save = await ask.json();
                    for (var i = 0; save.saving && i < 20; i++) {
                        await new Promise(function (resolve) { setTimeout(resolve, 1000); });
                        var now = await (await draftRequest('/status', 'GET')).json();
                        if (now.stamp !== save.stamp) break;
                    }
                }
                setBusy('Submitting…');
                var res = await draftRequest('/submit', 'POST');
                var data = await res.json().catch(function () { return {}; });
                if (!res.ok) throw new Error(data.message || (res.status === 419 ? 'Your session timed out. Please refresh the page and try again.' : 'The template could not be submitted. Please try again.'));
                window.location.href = data.redirect || @json($backUrl);
            } catch (e) {
                submitting = false;
                setBusy(null);
                error.textContent = /fetch|network/i.test(e.message) ? 'Can\'t reach the server. Please check your internet connection and try again.' : e.message;
                error.style.display = 'block';
            }
        }
    </script>
    @endif
</body>
</html>
