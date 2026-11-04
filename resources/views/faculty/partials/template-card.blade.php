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
        @if($template->isEditableFile())
            <form method="POST" action="{{ url('/faculty/my-template/' . $template->id . '/use') }}" class="card-copy">
                @csrf
                <button type="submit" class="btn-sm btn-view-file" title="{{ $template->has_copy ? 'Open your copy of this template in the editor' : 'Make your own copy and edit it in the editor' }}">{{ $template->has_copy ? 'Continue Editing' : 'Make Copy' }}</button>
            </form>
        @endif
        <a class="icon-btn" href="{{ \App\Support\FileLinks::view('template', $template->id) }}" target="_blank" title="View" aria-label="View" onclick="markTemplateViewed({{ $template->id }}, this)">@include('documents.partials.icon', ['name' => 'eye'])</a>
        <a class="icon-btn" href="{{ \App\Support\FileLinks::download('template', $template->id) }}" title="Download" aria-label="Download" onclick="markTemplateViewed({{ $template->id }}, this)">@include('documents.partials.icon', ['name' => 'download'])</a>
    </div>
</div>
