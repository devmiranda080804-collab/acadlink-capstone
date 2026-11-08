@php $libraryEntry = $copy->libraryEntry; $libraryPrograms = $copy->libraryPrograms(auth()->user()); @endphp
<div class="template-card">
    <div class="card-top">
        <span class="card-icon">{{ ['xlsx' => '📊', 'pptx' => '📽️', 'pdf' => '📄'][$copy->extension()] ?? '📝' }}</span>
        @if($libraryEntry)
            <span class="status-badge status-approved">In Library</span>
        @endif
    </div>

    <div class="card-title">{{ $copy->title }}</div>
    <div class="card-meta">
        {{ strtoupper($copy->file_type) }} • {{ $copy->readable_size }} •
        {{ $copy->last_edited_at ? 'Edited ' . $copy->last_edited_at->diffForHumans() : 'Added ' . $copy->created_at->format('M j, Y') }}
    </div>

    <div class="card-actions">
        <a class="btn-sm btn-copy-open" href="{{ url('/faculty/cms/' . $copy->id . '/edit') }}">{{ $copy->isEditableFile() ? 'Edit' : 'Open' }}</a>
        <a class="icon-btn" href="{{ \App\Support\FileLinks::download('module', $copy->id) }}" title="Download" aria-label="Download">@include('documents.partials.icon', ['name' => 'download'])</a>
        @if($libraryPrograms)
        <button type="button" class="icon-btn {{ $libraryEntry ? 'icon-btn-active' : '' }}" data-programs="{{ implode(' ', $libraryPrograms) }}" title="{{ $libraryEntry ? 'In the Shared Library. Click to update it' : 'Share to Library' }}" aria-label="{{ $libraryEntry ? 'Update in Shared Library' : 'Share to Library' }}" data-library-refresh data-url="{{ url('/faculty/cms/' . $copy->id . '/library') }}" data-title="{{ $copy->title }}" @if($libraryEntry) data-entry="{{ json_encode($libraryEntry->only(['title', 'type', 'description', 'course_id', 'program'])) }}" @endif onclick="openLibraryShare(this)">@include('documents.partials.icon', ['name' => 'library'])</button>
        @endif
        <form method="POST" action="{{ url('/faculty/cms/' . $copy->id) }}" class="card-delete" data-confirm="Delete {{ $copy->title }}? This can't be undone.{{ $libraryEntry ? ' The copy already in the Shared Library stays there.' : '' }}" data-confirm-title="Delete copy?" data-confirm-button="Delete">
            @csrf
            @method('DELETE')
            <button type="submit" class="icon-btn icon-btn-danger" title="Delete" aria-label="Delete">@include('documents.partials.icon', ['name' => 'trash'])</button>
        </form>
    </div>
</div>
