@php
    $statusClass = $template->isPending() ? 'waiting' : ($template->isReturned() ? 'returned' : 'forwarded');
    $sentPrograms = $template->sentPrograms();
    // Programs the previous approved version of this template was sent to — pre-checked when approving a new version.
    $suggestedPrograms = $sentPrograms ?: ($sentByRoot[$template->root_template_id] ?? []);
@endphp
<div class="tmpl-card {{ $statusClass }}" data-status="{{ $template->status }}" data-title="{{ strtolower($template->title) }}" @isset($folderKey) data-folder="{{ $folderKey }}" @endisset>
    <div class="tmpl-card-top">
        <span class="tmpl-card-icon">{{ \App\Models\TemplateDocument::typeIcon($template->type) }}</span>
        @if($template->isPending())
            <span class="status-badge status-waiting">For Your Approval</span>
        @elseif($template->isReturned())
            <span class="status-badge status-returned">Returned</span>
        @elseif($sentPrograms)
            <span class="status-badge status-forwarded">Approved &amp; Sent</span>
        @else
            <span class="status-badge status-approved-only">Approved, not sent</span>
        @endif
    </div>
    <div class="tmpl-card-title">{{ $template->title }}</div>
    <div class="tmpl-card-type">
        {{ \App\Models\TemplateDocument::typeLabel($template->type) }}
        @if($template->version > 1) <span class="version-tag">v{{ $template->version }}</span> @endif
    </div>
    <div class="tmpl-card-meta">Submitted by {{ $template->creator->name ?? 'Unknown' }} • {{ $template->created_at->format('Y-m-d') }}</div>

    @if($template->isReturned() && $template->return_reason)
        <div class="return-reason"><strong>Returned:</strong> {{ $template->return_reason }}</div>
    @endif

    @if($sentPrograms)
        <div class="program-tags">
            @foreach($sentPrograms as $program)
                <span class="program-tag distributed" title="{{ \App\Support\Programs::label($program) }}">{{ $program }}</span>
            @endforeach
        </div>
    @endif

    <div class="action-buttons">
        <a class="btn-view" href="{{ \App\Support\FileLinks::view('template', $template->id) }}" target="_blank">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            View
        </a>
        <a class="btn-icon-sm" href="{{ \App\Support\FileLinks::download('template', $template->id) }}" title="Download">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        </a>
        @if($template->isPending())
            <button type="button" class="btn-approve" onclick="openSendModal({{ $template->id }}, @js($template->title), 'approve', @js($suggestedPrograms))">Approve &amp; Send</button>
            <button type="button" class="btn-return" onclick="openReturnModal({{ $template->id }}, @js($template->title))">Return</button>
        @elseif($template->isApproved())
            <button type="button" class="btn-approve" onclick="openSendModal({{ $template->id }}, @js($template->title), 'send', @js($sentPrograms))">{{ $sentPrograms ? 'Send to More' : 'Send to Programs' }}</button>
        @endif
        <button type="button" class="btn-icon-sm" title="Version history" onclick="openHistoryModal({{ $template->id }}, @js($template->title))">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </button>
        <button type="button" class="btn-icon-sm btn-icon-del" title="Remove" onclick="openDeleteModal({{ $template->id }}, @js($template->title), {{ $sentPrograms ? 'true' : 'false' }})">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
        </button>
    </div>
</div>
