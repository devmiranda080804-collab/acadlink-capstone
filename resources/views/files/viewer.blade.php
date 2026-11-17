<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $file['title'] }} – CBMA System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f6fa; display: flex; height: 100vh; overflow: hidden; color: #1a1a2e; }

        /* Same frame as every page: sidebar, top bar, then the file. */
        .main { flex: 1; min-width: 0; display: flex; flex-direction: column; overflow: hidden; background: #f5f6fa; }
        .topnav { background: #fff; border-bottom: 1px solid #e0e0e0; padding: 0 24px; height: 52px; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
        .topnav-right { display: flex; align-items: center; gap: 12px; margin-left: auto; }
        .user-info { display: flex; align-items: center; gap: 8px; }
        .user-text { text-align: right; }
        .user-name { font-size: 12px; font-weight: 600; color: #222; }
        .user-email { font-size: 10px; color: #888; }
        .user-avatar { width: 34px; height: 34px; border-radius: 50%; background-color: #0f2557; color: #fff; font-size: 12px; font-weight: 700; display: flex; align-items: center; justify-content: center; }
        .content.viewer-content { flex: 1; min-height: 0; display: flex; flex-direction: column; gap: 14px; padding: 20px 24px 20px; overflow: hidden; }

        .viewer-head { display: flex; align-items: center; gap: 14px; flex-shrink: 0; flex-wrap: wrap; }
        .viewer-back { display: inline-flex; align-items: center; gap: 6px; flex-shrink: 0; color: #0f2557; text-decoration: none; font-size: 13px; font-weight: 700;
            padding: 8px 14px 8px 11px; border-radius: 9px; background: #fff; border: 1px solid #dfe3ec; box-shadow: 0 1px 2px rgba(15, 37, 87, .05); transition: background .15s, border-color .15s; }
        .viewer-back:hover { background: #f1f4fa; border-color: #c9d1e2; }
        .viewer-back:focus-visible { outline: 2px solid #f5b700; outline-offset: 2px; }
        .viewer-back svg { width: 15px; height: 15px; }
        .viewer-icon { font-size: 26px; line-height: 1; flex-shrink: 0; }
        .viewer-info { flex: 1; min-width: 0; }
        .viewer-title { font-size: 17px; font-weight: 800; color: #0f2557; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .viewer-meta { font-size: 12px; color: #6b7287; margin-top: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .btn-download { display: inline-flex; align-items: center; gap: 6px; background: #0f2557; color: #fff; text-decoration: none; font-size: 12.5px; font-weight: 700; padding: 9px 16px; border-radius: 9px; flex-shrink: 0; }
        .btn-download:hover { background: #1a3a7a; }
        .btn-download svg { width: 14px; height: 14px; }
        .viewer-notice { background: #fef3c7; color: #92400e; font-size: 12px; font-weight: 700; padding: 8px 12px; border-radius: 8px; flex-shrink: 0; }

        .viewer-body { flex: 1; min-height: 0; display: flex; justify-content: center; overflow: auto; padding: 16px; background: #e9ecf2; border: 1px solid #e1e5ee; border-radius: 14px; }
        .viewer-frame { width: 100%; max-width: 1100px; height: 100%; border: none; background: #fff; border-radius: 6px; box-shadow: 0 2px 12px rgba(0,0,0,0.12); }
        .viewer-image { max-width: 100%; max-height: 100%; object-fit: contain; align-self: center; background: #fff; border-radius: 6px; box-shadow: 0 2px 12px rgba(0,0,0,0.12); }
        .viewer-docx { width: 100%; max-width: 1100px; }
        .viewer-docx .docx-wrapper { background: transparent !important; padding: 0 !important; }

        .viewer-empty { align-self: center; background: #fff; border-radius: 10px; padding: 32px 36px; text-align: center; max-width: 440px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
        .viewer-empty-icon { font-size: 40px; margin-bottom: 12px; }
        .viewer-empty-title { font-size: 15px; font-weight: 700; margin-bottom: 6px; }
        .viewer-empty-text { font-size: 13px; color: #555; line-height: 1.5; margin-bottom: 18px; }
        .btn-download-primary { display: inline-flex; align-items: center; gap: 6px; background: #0f2557; color: #fff; text-decoration: none; font-size: 13px; font-weight: 700; padding: 10px 18px; border-radius: 6px; }
        .btn-download-primary:hover { background: #1a3a7a; }

        @media (max-width: 600px) {
            .content.viewer-content { padding: 12px; gap: 10px; }
            .viewer-body { padding: 8px; }
            .viewer-title { font-size: 15px; }
        }
    </style>
</head>
<body>
    @php
        $icon = match ($previewAs) { 'pdf' => '📄', 'image' => '🖼️', 'docx' => '📝', default => '📎' };
        $size = !$file['size'] ? null : ($file['size'] >= 1048576 ? round($file['size'] / 1048576, 1) . ' MB' : round($file['size'] / 1024, 1) . ' KB');
        $notice = $notice ?? null;
        // Back goes to the page the file was opened from (same tab), or the dashboard.
        $previous = url()->previous();
        $home = match (auth()->user()->role ?? null) {
            'admin' => '/admin/dashboard', 'program_head' => '/program-head/dashboard',
            'secretary' => '/secretary/dashboard', 'faculty' => '/faculty/dashboard', default => '/',
        };
        $sameSite = $previous && parse_url($previous, PHP_URL_HOST) === request()->getHost();
        $previousPath = trim((string) parse_url((string) $previous, PHP_URL_PATH), '/');
        $isViewer = $previousPath === trim(request()->path(), '/') || str_contains($previousPath, '/download') || str_contains($previousPath, '/raw') || str_contains($previousPath, '/preview/file');
        // A page can say where Back goes (?back=/path), e.g. to reopen the same exam.
        $backParam = request()->query('back');
        $backGiven = is_string($backParam) && preg_match('#^/(?!/)[^\s]*$#', $backParam);
        $backUrl = $backGiven ? url($backParam) : ($sameSite && !$isViewer ? $previous : url($home));
        // The menu item of the page the file came from stays highlighted in the sidebar.
        $sbActivePath = trim((string) parse_url($backUrl, PHP_URL_PATH), '/');
        $meta = collect([
            $file['name'],
            strtoupper($file['type']),
            $size,
            $file['uploader'] ? 'Uploaded by ' . $file['uploader'] : null,
            $file['date'] ? $file['date']->format('M d, Y') : null,
        ])->filter()->implode(' · ');
    @endphp

    @include('partials.sidebar', ['sbActivePath' => $sbActivePath])

    <div class="main">
        <div class="topnav">
            <div class="topnav-right">
                @include('partials.topnav-user')
            </div>
        </div>

        <div class="content viewer-content">
            <div class="viewer-head">
                <a class="viewer-back" href="{{ $backUrl }}" id="viewer-back" title="Back to the previous page (Esc)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                    Back
                </a>
                <span class="viewer-icon" aria-hidden="true">{{ $icon }}</span>
                <div class="viewer-info">
                    <h1 class="viewer-title">{{ $file['title'] }}</h1>
                    <div class="viewer-meta" title="{{ $meta }}">{{ $meta }}</div>
                </div>
                @if($downloadUrl)
                    <a class="btn-download" href="{{ $downloadUrl }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download
                    </a>
                @elseif($notice)
                    <span class="viewer-notice">{{ $notice }}</span>
                @endif
            </div>

            <div class="viewer-body">
                @if($previewAs === 'pdf')
                    <iframe class="viewer-frame" src="{{ $rawUrl }}" title="{{ $file['title'] }}"></iframe>
                @elseif($previewAs === 'image')
                    <img class="viewer-image" src="{{ $rawUrl }}" alt="{{ $file['title'] }}">
                @elseif($previewAs === 'docx')
                    <div class="viewer-docx" id="docx-container"></div>
                    <div class="viewer-empty" id="docx-fallback" style="display:none;">
                        <div class="viewer-empty-icon">📝</div>
                        <div class="viewer-empty-title">This document can't be shown here</div>
                        @if($downloadUrl)
                            <div class="viewer-empty-text">Download it to open it in Microsoft Word.</div>
                            <a class="btn-download-primary" href="{{ $downloadUrl }}">Download</a>
                        @else
                            <div class="viewer-empty-text">Please reload this page to try again.</div>
                        @endif
                    </div>
                @else
                    <div class="viewer-empty">
                        <div class="viewer-empty-icon">📎</div>
                        <div class="viewer-empty-title">{{ strtoupper($file['type']) }} files can't be previewed in the browser</div>
                        <div class="viewer-empty-text">Download the file to open it in the app it was made with (for example Excel, PowerPoint, or Word).</div>
                        <a class="btn-download-primary" href="{{ $downloadUrl }}">Download {{ strtoupper($file['type']) }}</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if($previewAs === 'docx')
        <script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/docx-preview@0.4.1/dist/docx-preview.min.js"></script>
        <script>
            (async function () {
                var container = document.getElementById('docx-container');
                try {
                    var res = await fetch(@json($rawUrl), { credentials: 'same-origin' });
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    await docx.renderAsync(await res.blob(), container);
                } catch (e) {
                    container.style.display = 'none';
                    document.getElementById('docx-fallback').style.display = '';
                }
            })();
        </script>
    @endif

    <script>
        // Going back through the browser's history returns to the page as it was left
        // (open folder, filters, scroll). Without that history, the link's address is used.
        (function () {
            var back = document.getElementById('viewer-back');
            var fromHere = !@json((bool) $backGiven) && document.referrer && new URL(document.referrer).origin === location.origin;
            back.addEventListener('click', function (e) {
                if (fromHere && history.length > 1) {
                    e.preventDefault();
                    history.back();
                }
            });
            document.addEventListener('keydown', function (e) {
                if (e.key !== 'Escape' || e.defaultPrevented) return;
                // Esc first closes whatever is open (notifications, a dialog).
                if (document.querySelector('.nb-panel.open, .sf-confirm.open, .modal-overlay.open')) return;
                if (fromHere && history.length > 1) history.back(); else location.href = back.href;
            }, true); // before the panels' own Esc handlers close them
        })();
    </script>
    @include('partials.system-feedback')
</body>
</html>
