{{-- File-type tag for a dashboard row. Params: $type (file extension) --}}
@php
    $dbExt = strtolower((string) ($type ?? ''));
    $dbKind = match (true) {
        $dbExt === 'pdf' => 'pdf',
        in_array($dbExt, ['doc', 'docx'], true) => 'doc',
        in_array($dbExt, ['xls', 'xlsx'], true) => 'xls',
        in_array($dbExt, ['ppt', 'pptx'], true) => 'ppt',
        default => 'other',
    };
@endphp
<span class="db-file {{ $dbKind }}" aria-hidden="true">{{ $dbExt ? strtoupper(substr($dbExt, 0, 4)) : 'FILE' }}</span>
