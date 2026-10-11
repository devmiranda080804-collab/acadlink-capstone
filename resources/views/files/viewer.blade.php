<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $file['title'] }} – CBMA System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; }
        body { font-family: Arial, sans-serif; background: #e9ecf2; display: flex; flex-direction: column; color: #1a1a2e; }

        .viewer-bar { display: flex; align-items: center; gap: 14px; background: #0f2557; color: #fff; padding: 12px 20px; flex-shrink: 0; }
        .viewer-icon { font-size: 26px; line-height: 1; flex-shrink: 0; }
        .viewer-info { flex: 1; min-width: 0; }
        .viewer-title { font-size: 15px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .viewer-meta { font-size: 11.5px; color: #c8d6ec; margin-top: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .btn-download { display: inline-flex; align-items: center; gap: 6px; background: #fff; color: #0f2557; text-decoration: none; font-size: 12.5px; font-weight: 700; padding: 9px 16px; border-radius: 6px; flex-shrink: 0; }
        .btn-download:hover { background: #e8eefb; }
        .btn-download svg { width: 14px; height: 14px; }

        .viewer-body { flex: 1; min-height: 0; display: flex; justify-content: center; overflow: auto; padding: 16px; }
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
            .viewer-bar { flex-wrap: wrap; padding: 10px 14px; }
            .viewer-body { padding: 8px; }
        }
    </style>
</head>
<body>
    @php
        $icon = match ($previewAs) { 'pdf' => '📄', 'image' => '🖼️', 'docx' => '📝', default => '📎' };
        $size = $file['size'] >= 1048576 ? round($file['size'] / 1048576, 1) . ' MB' : round($file['size'] / 1024, 1) . ' KB';
        $meta = collect([
            $file['name'],
            strtoupper($file['type']),
            $size,
            $file['uploader'] ? 'Uploaded by ' . $file['uploader'] : null,
            $file['date'] ? $file['date']->format('M d, Y') : null,
        ])->filter()->implode(' · ');
    @endphp

    <div class="viewer-bar">
        <span class="viewer-icon">{{ $icon }}</span>
        <div class="viewer-info">
            <div class="viewer-title">{{ $file['title'] }}</div>
            <div class="viewer-meta" title="{{ $meta }}">{{ $meta }}</div>
        </div>
        <a class="btn-download" href="{{ $downloadUrl }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Download
        </a>
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
                <div class="viewer-empty-text">Download it to open it in Microsoft Word.</div>
                <a class="btn-download-primary" href="{{ $downloadUrl }}">Download</a>
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

    @include('partials.system-feedback')
</body>
</html>
