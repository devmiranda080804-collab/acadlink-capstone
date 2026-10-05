<div class="lib-card" data-title="{{ strtolower($item['title']) }}" data-folder="{{ $item['folder_key'] }}">
    <div class="lib-card-top">
        <span class="lib-icon">@if($item['file_type'] == 'gdoc') 📑 @elseif($item['file_type'] == 'pdf') 📄 @elseif(in_array($item['file_type'], ['xls','xlsx'])) 📊 @elseif(in_array($item['file_type'], ['ppt','pptx'])) 📊 @else 📝 @endif</span>
        @if($item['source'] == 'template')
            <span class="source-badge source-template">Template</span>
        @elseif($item['source'] == 'material')
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
    <div class="lib-actions">
        <a class="btn-view" href="{{ $item['file_url'] }}" target="_blank">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            View / Download
        </a>
        @if($item['can_delete'])
            <form method="POST" action="{{ url('/faculty/shared-library/' . $item['id']) }}" onsubmit="return confirm('Remove this resource?')" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-del" title="Delete">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                </button>
            </form>
        @endif
    </div>
</div>
