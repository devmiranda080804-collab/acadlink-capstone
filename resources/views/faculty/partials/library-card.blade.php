<div class="lib-card" data-title="{{ strtolower($item['title']) }}" data-folder="{{ $item['folder_key'] }}">
    <div class="lib-card-top">
        <span class="lib-icon">@if($item['file_type'] == 'gdoc') 📑 @elseif($item['file_type'] == 'pdf') 📄 @elseif(in_array($item['file_type'], ['xls','xlsx'])) 📊 @elseif(in_array($item['file_type'], ['ppt','pptx'])) 📊 @else 📝 @endif</span>
        @if($item['source'] == 'material')
            <span class="source-badge source-material">Material</span>
        @else
            <span class="source-badge source-shared">Shared</span>
        @endif
    </div>
    <div class="lib-title">{{ $item['title'] }}</div>
    <div class="lib-desc">{{ $item['desc'] }}</div>
    @if($showFolder ?? false)
        <div class="lib-folder">in {{ $item['folder_icon'] }} {{ $item['folder_label'] }}</div>
    @endif
    <div class="lib-meta">
        <span>By {{ $item['shared_by'] }}</span>
        <span class="dot">·</span>
        <span>{{ $item['date']->format('M d, Y') }}</span>
        @if(!empty($item['file_size']))
            <span class="dot">·</span>
            <span>{{ $item['file_size'] }}</span>
        @endif
    </div>
    @if(!empty($item['edited']) || !empty($item['together']))
        <div class="lib-collab">
            @if(!empty($item['edited']))<div>{{ $item['edited'] }}</div>@endif
            @if(!empty($item['together']) && !empty($item['edit_url']))<div>{{ $item['together'] }}</div>@endif
        </div>
    @endif
    @if(!empty($item['can_edit']))
        <a class="btn-collab" href="{{ $item['edit_url'] }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Edit Together
        </a>
    @endif
    <div class="lib-actions">
        <a class="btn-view" href="{{ $item['view_url'] }}" target="_blank">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            View
        </a>
        <a class="btn-view" href="{{ $item['download_url'] }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Download
        </a>
        @if(!empty($item['history_url']))
            <button type="button" class="btn-del" title="Version history" data-url="{{ $item['history_url'] }}" data-title="{{ $item['title'] }}" onclick="openHistory(this)">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><polyline points="3 3 3 8 8 8"/><polyline points="12 7 12 12 15.5 14"/></svg>
            </button>
        @endif
        @if($item['can_delete'])
            <form method="POST" action="{{ url('/faculty/shared-library/' . $item['id']) }}" data-confirm="Remove this resource from the Shared Library? This cannot be undone." data-confirm-title="Remove Resource" data-confirm-button="Remove" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-del" title="Delete">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                </button>
            </form>
        @endif
    </div>
</div>
