@php
    $icon = match (true) {
        $module->isWritten(), $module->extension() === 'docx', $module->extension() === 'doc' => '📝',
        $module->isGoogleDoc() => '📑',
        $module->extension() === 'xlsx' => '📊',
        $module->extension() === 'pptx' => '📽️',
        default => '📄',
    };
    $canEdit = $module->isWritten() ? $owned : $module->canEdit(auth()->user());
@endphp
<div class="module-card">
    <div class="card-top">
        <span class="card-icon">{{ $icon }}</span>
        <div class="card-top-right">
            @if($owned && $module->shares->isNotEmpty())
                <span class="card-badge">Shared with {{ $module->shares->count() }}</span>
            @elseif(!$owned)
                <span class="card-badge">{{ $canEdit ? 'Can edit' : 'Can view' }}</span>
            @endif
            @if($owned)
                <button type="button" class="icon-btn icon-btn-plain" title="Details" aria-label="Details" aria-expanded="false" data-url="{{ url('/faculty/cms/' . $module->id) }}" data-title="{{ $module->title }}" data-description="{{ $module->description }}" onclick="openDetails(this)">@include('documents.partials.icon', ['name' => 'info'])</button>
            @endif
        </div>
    </div>

    <div class="card-title">{{ $module->title }}</div>
    @if($module->description)
        <div class="card-desc">{{ $module->description }}</div>
    @endif
    @unless($owned)
        <div class="card-owner">From {{ $module->creator->name ?? 'a former user' }}</div>
    @endunless
    <div class="card-meta">
        @if($module->isGoogleDoc())
            Google Doc
        @elseif($module->isWritten())
            Written in AcadLink
        @else
            {{ strtoupper($module->file_type) }} • {{ $module->readable_size }}
        @endif
        <br>
        @if($module->last_edited_at)
            Edited {{ $module->last_edited_at->diffForHumans() }}{{ $module->lastEditor ? ' by ' . $module->lastEditor->name : '' }}
        @else
            Added {{ $module->created_at->format('M j, Y') }}
        @endif
    </div>

    <div class="card-actions">
        @if($editorReady || $module->isGoogleDoc())
            <a class="btn-sm btn-open" href="{{ url('/faculty/cms/' . $module->id . '/edit') }}" @if($module->isGoogleDoc()) target="_blank" @endif>{{ $canEdit ? 'Open & Edit' : 'Open' }}</a>
        @endif
        @if($editorReady && $module->canBeConverted())
            <button type="button" class="btn-sm btn-edit-sm" data-url="{{ url('/faculty/cms/' . $module->id . '/convert') }}" onclick="convertDocument(this)">Convert to Word</button>
        @endif
        @if($module->file_path)
            <a class="icon-btn" href="{{ \App\Support\FileLinks::download('module', $module->id) }}" title="Download" aria-label="Download">@include('documents.partials.icon', ['name' => 'download'])</a>
        @endif
        @if($module->file_path && $canEdit)
            <button type="button" class="icon-btn" title="Version history" aria-label="Version history" data-url="{{ url('/faculty/cms/' . $module->id . '/versions') }}" data-title="{{ $module->title }}" onclick="openHistory(this)">@include('documents.partials.icon', ['name' => 'history'])</button>
        @endif
        @if($owned)
            <button type="button" class="icon-btn" title="Share" aria-label="Share" data-url="{{ url('/faculty/cms/' . $module->id . '/share') }}" data-shares="{{ $module->shares->mapWithKeys(fn ($share) => [$share->user_id => $share->can_edit ? 'edit' : 'view'])->toJson() }}" onclick="openShare(this)">@include('documents.partials.icon', ['name' => 'share'])</button>
            <form method="POST" action="{{ url('/faculty/cms/' . $module->id) }}" data-confirm="Delete {{ $module->title }}? This can't be undone." data-confirm-title="Delete document?" data-confirm-button="Delete">
                @csrf
                @method('DELETE')
                <button type="submit" class="icon-btn icon-btn-danger" title="Delete" aria-label="Delete">@include('documents.partials.icon', ['name' => 'trash'])</button>
            </form>
        @endif
    </div>
</div>
