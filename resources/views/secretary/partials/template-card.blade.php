@php $isForwarded = $template->isForwarded(); @endphp
<div class="tmpl-card {{ $isForwarded ? 'forwarded' : 'waiting' }}" data-status="{{ $isForwarded ? 'forwarded' : 'waiting' }}" data-title="{{ strtolower($template->title) }}" @isset($folderKey) data-folder="{{ $folderKey }}" @endisset>
    <div class="tmpl-card-top">
        <span class="tmpl-card-icon">
            @if($template->type == 'syllabus') 📘
            @elseif($template->type == 'course_guide') 📙
            @elseif($template->type == 'module') 📝
            @else 📄
            @endif
        </span>
        @if($isForwarded)
            <span class="dist-status yes">Forwarded</span>
        @else
            <span class="dist-status no">Not yet forwarded</span>
        @endif
    </div>
    <div class="tmpl-card-title">{{ $template->title }}</div>
    <div class="tmpl-card-type">{{ str_replace('_', ' ', $template->type) }}</div>
    <div class="tmpl-card-meta">Uploaded by {{ $template->creator->name }} • {{ $template->created_at->format('Y-m-d') }}</div>

    <div class="program-tags">
        @foreach($template->programs as $row)
            <span class="program-tag" title="{{ \App\Support\Programs::label($row->program) }}">{{ $row->program }}</span>
        @endforeach
    </div>

    <div class="action-buttons">
        <a class="btn-view" href="{{ Storage::url($template->file_path) }}" target="_blank">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            View
        </a>
        @unless($isForwarded)
            <form method="POST" action="{{ url('/secretary/template-distribution/' . $template->id . '/forward') }}" style="flex:1;">
                @csrf
                <button type="submit" class="btn-distribute" style="width:100%;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    Forward
                </button>
            </form>
        @endunless
    </div>
</div>
