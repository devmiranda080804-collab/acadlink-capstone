<div class="template-card" data-title="{{ strtolower($template->title) }}" @isset($folderKey) data-folder="{{ $folderKey }}" @endisset>
    <div class="card-top">
        <span class="card-icon">{{ \App\Models\TemplateDocument::typeIcon($template->type) }}</span>
        <span class="status-badge status-approved">{{ \App\Models\TemplateDocument::typeLabel($template->type) }}</span>
    </div>

    <div class="card-title">
        @if($template->is_new ?? false) <span class="new-tag">NEW</span> @endif
        {{ $template->title }}
        @if($template->version > 1) <span class="version-tag">v{{ $template->version }}</span> @endif
    </div>
    <div class="card-meta">
        {{ strtoupper($template->file_type) }} • {{ $template->readable_size }} • Provided by {{ $template->creator->name }}
    </div>

    <div class="card-actions">
        <a class="btn-sm btn-view-file" href="{{ \App\Support\FileLinks::view('template', $template->id) }}" target="_blank" onclick="markTemplateViewed({{ $template->id }}, this)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            View
        </a>
        <a class="btn-sm btn-view-file" href="{{ \App\Support\FileLinks::download('template', $template->id) }}" onclick="markTemplateViewed({{ $template->id }}, this)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Download
        </a>
    </div>
</div>
