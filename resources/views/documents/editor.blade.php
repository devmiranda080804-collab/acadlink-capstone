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
        .editor-error { display: none; max-width: 460px; margin: 80px auto; padding: 24px; background: #fff; border-radius: 10px; text-align: center; font-size: 13px; line-height: 1.6; color: #444; }
        .editor-error strong { display: block; font-size: 15px; color: #1a1a2e; margin-bottom: 8px; }
    </style>
</head>
<body>
    <div class="editor-bar">
        <a class="editor-back" href="{{ $backUrl }}">← {{ $backLabel }}</a>
        <div class="editor-title">{{ $title }}</div>
        <span class="editor-access">{{ $canEdit ? 'Changes save automatically' : 'View only' }}</span>
        @if($libraryModule ?? null)
            <button type="button" class="editor-share" data-programs="{{ implode(' ', $libraryModule->libraryPrograms(auth()->user())) }}" data-url="{{ url('/faculty/cms/' . $libraryModule->id . '/library') }}" data-title="{{ $libraryModule->title }}" @if($libraryModule->libraryEntry) data-entry="{{ json_encode($libraryModule->libraryEntry->only(['title', 'type', 'description', 'course_id', 'program'])) }}" @endif onclick="openLibraryShare(this)">{{ $libraryModule->libraryEntry ? 'Update in Library' : 'Share to Library' }}</button>
        @endif
    </div>
    <div class="editor-area"><div id="document-editor"></div></div>
    @if($libraryModule ?? null)
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
</body>
</html>
