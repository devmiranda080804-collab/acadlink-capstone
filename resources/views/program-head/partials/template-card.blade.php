@php $template = $r['template']; $row = $r['row']; @endphp
<div class="review-card distributed" data-status="sent" data-title="{{ strtolower($template->title) }}" @isset($folderKey) data-folder="{{ $folderKey }}" @endisset>
    <div class="review-card-top">
        <span class="review-card-icon">{{ \App\Models\TemplateDocument::typeIcon($template->type) }}</span>
        <span class="status-badge status-approved">Sent by the Dean</span>
    </div>
    <div class="review-card-title">{{ $template->title }}</div>
    <div class="review-card-type">
        {{ \App\Models\TemplateDocument::typeLabel($template->type) }}
        @if($template->version > 1) <span style="font-size:10px;font-weight:700;color:#6d28d9;background:#ede9fe;padding:1px 7px;border-radius:10px;margin-left:6px;">v{{ $template->version }}</span> @endif
    </div>
    <div class="review-card-meta">Received {{ $row?->distributed_at?->format('Y-m-d') }}</div>

    <div class="card-actions">
        <a class="btn-view-file" href="{{ Storage::url($template->file_path) }}" target="_blank">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            View
        </a>
    </div>
</div>
