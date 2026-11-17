<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Review: {{ $template->title }} – CBMA System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f6fa; color: #333; display: flex; height: 100vh; overflow: hidden; }

        /* Same frame as every page: sidebar, top bar, then the review. */
        .main { flex: 1; min-width: 0; display: flex; flex-direction: column; overflow: hidden; background: #f5f6fa; }
        .topnav { background: #fff; border-bottom: 1px solid #e0e0e0; padding: 0 24px; height: 52px; display: flex; align-items: center; flex-shrink: 0; }
        .topnav-right { display: flex; align-items: center; gap: 12px; margin-left: auto; }
        .user-info { display: flex; align-items: center; gap: 8px; }
        .user-text { text-align: right; }
        .user-name { font-size: 12px; font-weight: 600; color: #222; }
        .user-email { font-size: 10px; color: #888; }
        .user-avatar { width: 34px; height: 34px; border-radius: 50%; background-color: #0f2557; color: #fff; font-size: 12px; font-weight: 700; display: flex; align-items: center; justify-content: center; }
        .content.review-content { flex: 1; min-height: 0; display: flex; flex-direction: column; gap: 14px; padding: 20px 24px; overflow: hidden; }

        .review-head { display: flex; align-items: center; gap: 14px; flex-shrink: 0; flex-wrap: wrap; }
        .review-back { display: inline-flex; align-items: center; gap: 6px; flex-shrink: 0; color: #0f2557; text-decoration: none; font-size: 13px; font-weight: 700;
            padding: 8px 14px 8px 11px; border-radius: 9px; background: #fff; border: 1px solid #dfe3ec; box-shadow: 0 1px 2px rgba(15, 37, 87, .05); transition: background .15s, border-color .15s; }
        .review-back:hover { background: #f1f4fa; border-color: #c9d1e2; }
        .review-back:focus-visible { outline: 2px solid #f5b700; outline-offset: 2px; }
        .review-back svg { width: 15px; height: 15px; }
        .review-info { flex: 1; min-width: 0; }
        .review-title { font-size: 17px; font-weight: 800; color: #0f2557; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .review-sub { font-size: 12px; color: #6b7287; margin-top: 3px; }
        .review-mode { font-size: 11.5px; font-weight: 700; padding: 6px 12px; border-radius: 999px; background: #eef2ff; color: #3730a3; white-space: nowrap; }
        .review-body { flex: 1; min-height: 0; display: flex; background: #fff; border: 1px solid #e1e5ee; border-radius: 14px; overflow: hidden; }
        .review-doc { flex: 1; min-width: 0; position: relative; }
        .review-doc iframe { width: 100%; height: 100%; border: none; background: #fff; }
        .review-error { display: none; max-width: 460px; margin: 80px auto; padding: 24px; background: #fff; border-radius: 10px; text-align: center; font-size: 13px; line-height: 1.6; }
        .review-error strong { display: block; font-size: 15px; color: #1a1a2e; margin-bottom: 8px; }
        .review-panel { width: 340px; flex-shrink: 0; background: #fff; border-left: 1px solid #e0e0e0; overflow-y: auto; padding: 18px; }
        .panel-section { margin-bottom: 18px; }
        .panel-heading { font-size: 12px; font-weight: 700; color: #1a1a2e; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 8px; }
        .status-badge { display: inline-block; font-size: 10.5px; font-weight: 700; padding: 3px 10px; border-radius: 10px; }
        .status-waiting { background: #fef9c3; color: #92400e; }
        .status-returned { background: #fee2e2; color: #991b1b; }
        .status-approved { background: #d1fae5; color: #065f46; }
        .detail-row { display: flex; justify-content: space-between; gap: 10px; font-size: 12px; padding: 5px 0; border-bottom: 1px solid #f3f3f3; }
        .detail-row span:first-child { color: #888; }
        .detail-row span:last-child { text-align: right; font-weight: 600; color: #333; overflow-wrap: anywhere; }
        .comment-tip { background: #eef2ff; border: 1px solid #c7d2fe; color: #3730a3; font-size: 12px; line-height: 1.55; border-radius: 6px; padding: 10px 12px; }
        .note { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; font-size: 12px; line-height: 1.5; border-radius: 6px; padding: 9px 11px; }
        .program-list { border: 1px solid #ddd; border-radius: 6px; padding: 8px 10px; max-height: 200px; overflow-y: auto; }
        .program-list label { display: flex; align-items: center; gap: 7px; font-size: 12px; padding: 3px 0; cursor: pointer; }
        .program-list label.all { font-weight: 700; color: #0f2557; border-bottom: 1px solid #eee; padding-bottom: 7px; margin-bottom: 4px; }
        .program-list input { width: auto; margin: 0; accent-color: #0f2557; }
        textarea { width: 100%; min-height: 90px; padding: 8px 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 12.5px; font-family: Arial, sans-serif; resize: vertical; outline: none; }
        textarea:focus { border-color: #0f2557; }
        .hint { font-size: 11px; color: #888; line-height: 1.5; margin-top: 5px; }
        .btn { display: block; width: 100%; margin-top: 10px; border: none; border-radius: 6px; font-size: 13px; font-weight: 700; padding: 10px; cursor: pointer; font-family: Arial, sans-serif; }
        .btn:disabled { opacity: .6; cursor: wait; }
        .btn-approve { background: #0f2557; color: #fff; }
        .btn-approve:hover { background: #1a3a7a; }
        .btn-return { background: #fff; color: #b91c1c; border: 1px solid #fca5a5; }
        .btn-return:hover { background: #fee2e2; }
        .form-error { display: none; font-size: 11.5px; color: #b91c1c; margin-top: 6px; }
        .divider { border: none; border-top: 1px solid #eee; margin: 18px 0; }
        .program-tag { display: inline-block; font-size: 10.5px; font-weight: 700; color: #0f2557; background: #eef2ff; border-radius: 10px; padding: 2px 8px; margin: 0 4px 4px 0; }
        @media (max-width: 860px) {
            .content.review-content { overflow-y: auto; padding: 12px; }
            .review-body { flex-direction: column; flex: none; }
            .review-doc { min-height: 60vh; }
            .review-panel { width: 100%; border-left: none; border-top: 1px solid #e0e0e0; }
        }
    </style>
</head>
<body>
    {{-- Opened from the Templates page, which stays highlighted in the sidebar. --}}
    @include('partials.sidebar', ['sbActivePath' => 'admin/template-approvals'])

    <div class="main">
    <div class="topnav">
        <div class="topnav-right">
            @include('partials.topnav-user')
        </div>
    </div>

    <div class="content review-content">
    <div class="review-head">
        <a class="review-back" href="{{ url('/admin/template-approvals') }}" id="review-back" title="Back to Templates (Esc)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back
        </a>
        <div class="review-info">
            <h1 class="review-title">{{ $template->title }}</h1>
            <div class="review-sub">{{ \App\Models\TemplateDocument::typeLabel($template->type) }} · v{{ $template->version }} · Uploaded by {{ $template->creator->name ?? 'Unknown' }}</div>
        </div>
        <span class="review-mode">{{ $canComment ? 'You can add comments' : 'View only' }}</span>
    </div>

    <div class="review-body">
        <div class="review-doc">
            @if($config)
                <div id="document-editor"></div>
                <div class="review-error" id="review-error">
                    <strong>The document could not be loaded</strong>
                    Please check your connection and try again. You can still <a href="{{ \App\Support\FileLinks::view('template', $template->id) }}">open the file</a> in the viewer.
                </div>
            @else
                <iframe src="{{ \App\Support\FileLinks::view('template', $template->id) }}?embed=1" title="{{ $template->title }}"></iframe>
            @endif
        </div>

        <aside class="review-panel">
            <div class="panel-section">
                <div class="panel-heading">Details</div>
                <div class="detail-row"><span>Status</span><span>
                    @if($template->isPending())<span class="status-badge status-waiting">For Your Approval</span>
                    @elseif($template->isReturned())<span class="status-badge status-returned">Returned</span>
                    @else<span class="status-badge status-approved">{{ $sentPrograms ? 'Approved & Sent' : 'Approved, not sent' }}</span>@endif
                </span></div>
                <div class="detail-row"><span>Type</span><span>{{ \App\Models\TemplateDocument::typeLabel($template->type) }}</span></div>
                <div class="detail-row"><span>Version</span><span>v{{ $template->version }}</span></div>
                <div class="detail-row"><span>Uploaded by</span><span>{{ $template->creator->name ?? 'Unknown' }}</span></div>
                <div class="detail-row"><span>Uploaded on</span><span>{{ \App\Support\DisplayTime::format($template->created_at) }}</span></div>
                @if($template->reviewed_at)
                    <div class="detail-row"><span>{{ $template->isReturned() ? 'Returned' : 'Approved' }} by</span><span>{{ $template->reviewer->name ?? 'Unknown' }}</span></div>
                    <div class="detail-row"><span>{{ $template->isReturned() ? 'Returned' : 'Approved' }} on</span><span>{{ \App\Support\DisplayTime::format($template->reviewed_at) }}</span></div>
                @endif
                <div class="detail-row"><span>File</span><span>{{ strtoupper($template->file_type) }} · {{ $template->readable_size }}</span></div>
                @if($sentPrograms)
                    <div style="margin-top:8px;">@foreach($sentPrograms as $p)<span class="program-tag">{{ $p }}</span>@endforeach</div>
                @endif
            </div>

            @if($template->isReturned() && $template->return_reason)
                <div class="panel-section"><div class="note"><strong>Returned:</strong> {{ $template->return_reason }}</div></div>
            @endif

            @if($template->isPending())
                <div class="panel-section">
                    @if($canComment)
                        <div class="comment-tip"><strong>Leave comments like in Google Docs.</strong> Select text in the document, then click <strong>Comment</strong> (or right-click → Add comment). The Secretary sees your comments when they open the returned template in the editor.</div>
                    @else
                        <div class="comment-tip">Comments can be added on Word, Excel and PowerPoint files. For this file, write what to fix in the reason when you return it.</div>
                    @endif
                </div>

                <form class="panel-section" method="POST" action="{{ url('/admin/template-approvals/' . $template->id . '/approve') }}" data-review-form>
                    @csrf
                    <div class="panel-heading">Approve &amp; Send</div>
                    @include('admin.partials.review-programs', ['checked' => $suggestedPrograms, 'locked' => []])
                    <div class="hint">Faculty in the selected programs see the template right away. Your comments are removed from the copy they get.</div>
                    <div class="form-error">Choose at least one program.</div>
                    <button type="submit" class="btn btn-approve">Approve &amp; Send</button>
                </form>

                <hr class="divider">

                <form class="panel-section" method="POST" action="{{ url('/admin/template-approvals/' . $template->id . '/return') }}" data-review-form>
                    @csrf
                    <div class="panel-heading">Return to Secretary</div>
                    <textarea name="reason" maxlength="1000" required placeholder="e.g. Please use the 2026 course outcomes and add the signatory block. See my comments in the document."></textarea>
                    <div class="hint">The Secretary sees this reason and your comments, and can fix the template in the editor.</div>
                    <button type="submit" class="btn btn-return">Return Template</button>
                </form>
            @elseif($template->isApproved())
                <form class="panel-section" method="POST" action="{{ url('/admin/template-approvals/' . $template->id . '/send') }}" data-review-form>
                    @csrf
                    <div class="panel-heading">{{ $sentPrograms ? 'Send to More Programs' : 'Send to Programs' }}</div>
                    @include('admin.partials.review-programs', ['checked' => $sentPrograms, 'locked' => $sentPrograms])
                    <div class="form-error">Choose at least one program.</div>
                    <button type="submit" class="btn btn-approve">Send</button>
                </form>
            @endif
        </aside>
    </div>
    </div>
    </div>

    @if($config)
        <script src="{{ $scriptUrl }}" onerror="showReviewError()"></script>
    @endif
    <script>
        // Back (and Esc) returns to the Templates page as it was left, open folder included.
        (function () {
            var back = document.getElementById('review-back');
            var fromList = document.referrer && new URL(document.referrer).pathname.replace(/\/$/, '') === '/admin/template-approvals';
            back.addEventListener('click', function (e) {
                if (fromList && history.length > 1) { e.preventDefault(); history.back(); }
            });
            document.addEventListener('keydown', function (e) {
                if (e.key !== 'Escape' || e.defaultPrevented) return;
                if (document.querySelector('.nb-panel.open, .sf-confirm.open') || /^(INPUT|TEXTAREA|SELECT)$/.test((document.activeElement || {}).tagName)) return;
                back.click();
            }, true);
        })();

        function showReviewError() {
            var editor = document.getElementById('document-editor');
            if (editor) editor.style.display = 'none';
            document.getElementById('review-error').style.display = 'block';
        }

        @if($config)
        window.addEventListener('load', function () {
            if (!window.DocsAPI) { showReviewError(); return; }
            new DocsAPI.DocEditor('document-editor', Object.assign(@json($config), { width: '100%', height: '100%' }));
        });
        @endif

        function toggleAllReviewPrograms(box) {
            box.closest('.program-list').querySelectorAll('input[name="programs[]"]').forEach(function (item) {
                if (!item.disabled) item.checked = box.checked;
            });
        }

        // Comments typed in the editor reach AcadLink when it saves. Ask it to save, wait for
        // the file to arrive, then send the decision, so the Secretary gets every comment.
        var CSRF = document.querySelector('meta[name="csrf-token"]').content;
        var SAVE_URL = @json(url('/admin/template-approvals/' . $template->id));

        async function saveComments() {
            if (!@json($canComment)) return;
            var ask = await fetch(SAVE_URL + '/prepare', { method: 'POST', headers: { Accept: 'application/json', 'X-CSRF-TOKEN': CSRF } });
            if (!ask.ok) return;
            var save = await ask.json();
            if (!save.saving) return;
            for (var i = 0; i < 20; i++) {
                await new Promise(function (resolve) { setTimeout(resolve, 1000); });
                var now = await (await fetch(SAVE_URL + '/status', { headers: { Accept: 'application/json' } })).json();
                if (now.stamp !== save.stamp) return;
            }
        }

        document.querySelectorAll('[data-review-form]').forEach(function (form) {
            form.addEventListener('submit', async function (e) {
                if (form.dataset.ready) return;
                e.preventDefault();
                var programs = form.querySelectorAll('input[name="programs[]"]');
                var error = form.querySelector('.form-error');
                if (programs.length && !Array.from(programs).some(function (p) { return p.checked && !p.disabled; })) {
                    if (error) error.style.display = 'block';
                    return;
                }
                if (error) error.style.display = 'none';
                var button = form.querySelector('button[type=submit]');
                var label = button.textContent;
                button.disabled = true;
                button.textContent = 'Saving your comments…';
                try { await saveComments(); } catch (err) { /* the decision still goes through */ }
                button.textContent = label;
                form.dataset.ready = '1';
                form.submit();
            });
        });
    </script>
    @include('partials.system-feedback')
</body>
</html>
