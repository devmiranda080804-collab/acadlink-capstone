<div class="template-card" data-title="{{ strtolower($template->title) }}" @isset($folderKey) data-folder="{{ $folderKey }}" @endisset>
    <div class="card-top">
        <span class="card-icon">{{ \App\Models\TemplateDocument::typeIcon($template->type) }}</span>
        <span class="status-badge status-approved">{{ \App\Models\TemplateDocument::typeLabel($template->type) }}</span>
    </div>

    <div class="card-title">
        {{ $template->title }}
        @if($template->version > 1) <span class="version-tag">v{{ $template->version }}</span> @endif
    </div>
    <div class="card-meta">
        {{ strtoupper($template->file_type) }} • {{ $template->readable_size }} • Provided by {{ $template->creator->name }}
    </div>

    <div class="card-actions">
        <a class="btn-sm btn-view-file" href="{{ Storage::url($template->file_path) }}" target="_blank">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            View / Download
        </a>
    </div>
</div>
