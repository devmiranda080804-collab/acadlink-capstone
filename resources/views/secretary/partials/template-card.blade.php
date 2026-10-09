@php
    $statusClass = $template->isPending() ? 'waiting' : ($template->isReturned() ? 'returned' : 'forwarded');
    $sentPrograms = $template->sentPrograms();
@endphp
<div class="tmpl-card {{ $statusClass }}" data-status="{{ $template->status }}" data-title="{{ strtolower($template->title) }}" @isset($folderKey) data-folder="{{ $folderKey }}" @endisset>
    <div class="tmpl-card-top">
        <span class="tmpl-card-icon">{{ \App\Models\TemplateDocument::typeIcon($template->type) }}</span>
        @if($template->isPending())
            <span class="dist-status no">Waiting for Dean</span>
        @elseif($template->isReturned())
            <span class="dist-status returned">Returned</span>
        @else
            <span class="dist-status yes">Approved</span>
        @endif
    </div>
    <div class="tmpl-card-title">{{ $template->title }}</div>
    <div class="tmpl-card-type">
        {{ \App\Models\TemplateDocument::typeLabel($template->type) }}
        @if($template->version > 1) <span style="font-size:10px;font-weight:700;color:#6d28d9;background:#ede9fe;padding:1px 7px;border-radius:10px;margin-left:6px;">v{{ $template->version }}</span> @endif
    </div>
    <div class="tmpl-card-meta">Submitted by {{ $template->creator->name ?? 'Unknown' }} • {{ $template->created_at->format('Y-m-d') }}</div>

    @if($template->isReturned())
        <div class="return-reason">
            <strong>Returned by {{ $template->reviewer->name ?? 'the Dean' }}:</strong> {{ $template->return_reason }}
        </div>
    @endif

    @if($sentPrograms)
        <div class="program-tags">
            @foreach($sentPrograms as $program)
                <span class="program-tag" title="{{ \App\Support\Programs::label($program) }}">{{ $program }}</span>
            @endforeach
        </div>
    @endif

    <div class="action-buttons">
        <a class="btn-view" href="{{ Storage::url($template->file_path) }}" target="_blank">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            View
        </a>
        @unless($template->isPending())
            <button type="button" class="btn-distribute" onclick="openNewVersionModal({{ $template->id }}, @js($template->title))">
                {{ $template->isReturned() ? 'Upload Corrected Version' : 'Upload New Version' }}
            </button>
        @endunless
        @unless($template->isApproved())
            <button type="button" class="btn-withdraw" title="Withdraw" onclick="openWithdrawModal({{ $template->id }}, @js($template->title))">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
            </button>
        @endunless
    </div>
</div>
