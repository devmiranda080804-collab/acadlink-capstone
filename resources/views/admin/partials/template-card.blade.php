@php $isForwarded = $template->isForwarded(); @endphp
<div class="tmpl-card {{ $isForwarded ? 'forwarded' : 'waiting' }}" data-status="{{ $isForwarded ? 'forwarded' : 'waiting' }}" data-title="{{ strtolower($template->title) }}" @isset($folderKey) data-folder="{{ $folderKey }}" @endisset>
    <div class="tmpl-card-top">
        <span class="tmpl-card-icon">{{ \App\Models\TemplateDocument::typeIcon($template->type) }}</span>
        @if($isForwarded)
            <span class="status-badge status-forwarded">Forwarded</span>
        @else
            <span class="status-badge status-waiting">Awaiting Secretary</span>
        @endif
    </div>
    <div class="tmpl-card-title">{{ $template->title }}</div>
    <div class="tmpl-card-type">
        {{ \App\Models\TemplateDocument::typeLabel($template->type) }}
        @if($template->version > 1) <span class="version-tag">v{{ $template->version }}</span> @endif
    </div>
    <div class="tmpl-card-meta">Uploaded by {{ $template->creator->name }} • {{ $template->created_at->format('Y-m-d') }}</div>

    <div class="program-tags">
        @foreach($template->programs as $row)
            <span class="program-tag {{ $row->distributed_at ? 'distributed' : 'pending' }}" title="{{ \App\Support\Programs::label($row->program) }}">{{ $row->program }}</span>
        @endforeach
    </div>

    <div class="action-buttons">
        <a class="btn-view" href="{{ Storage::url($template->file_path) }}" target="_blank">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            View
        </a>
        <button type="button" class="btn-icon-sm" title="Upload a new version" onclick="openNewVersionModal({{ $template->id }}, @js($template->title))">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
        </button>
        <button type="button" class="btn-icon-sm" title="Version history" onclick="openHistoryModal({{ $template->id }}, @js($template->title))">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </button>
        <button type="button" class="btn-del" onclick="openDeleteModal({{ $template->id }}, @js($template->title), {{ $isForwarded ? 'true' : 'false' }})">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
            Remove
        </button>
    </div>
</div>
