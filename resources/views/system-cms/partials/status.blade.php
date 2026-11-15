{{-- Status card at the top of a System CMS section. Param: $panel = elements | layouts | lists | site --}}
@php
    $facts = [];
    switch ($panel) {
        case 'elements':
            $shown = $templateTypes->where('is_active', true)->count();
            $hidden = $templateTypes->count() - $shown;
            $icon = 'files';
            $title = $shown . ' template ' . ($shown === 1 ? 'type' : 'types') . ' in use';
            $sub = 'The kinds of template the Secretary can upload, file and ask for.';
            $facts = ['Shown' => $shown, 'Hidden' => $hidden, 'Used by uploads' => array_sum($templateTypeUsage ?? [])];
            break;
        case 'layouts':
            $changed = 0;
            foreach (array_keys(\App\Support\DocumentLayouts::DOCUMENTS) as $doc) {
                foreach (array_keys(\App\Support\DocumentLayouts::fields($doc)) as $field) {
                    $changed += \App\Support\DocumentLayouts::isChanged($doc, $field) ? 1 : 0;
                }
            }
            $icon = 'layout';
            $title = count(\App\Support\DocumentLayouts::DOCUMENTS) . ' documents ready to download';
            $sub = 'Header, contents and footer of every document the system prints.';
            $facts = ['Documents' => count(\App\Support\DocumentLayouts::DOCUMENTS), 'Changed parts' => $changed ?: 'None — using the defaults'];
            break;
        case 'lists':
            $count = fn ($list) => \App\Models\CmsOption::where('list', $list)->where('is_active', true)->count();
            $icon = 'clipboard';
            $title = 'Dropdown choices across the system';
            $sub = 'What people can pick in programs, academic years and semesters.';
            $facts = ['Programs' => $count('programs'), 'Academic years' => $count('school_years'), 'Semesters' => $count('semesters')];
            break;
        default:
            $texts = collect(array_keys(\App\Support\SiteContent::TEXTS))->filter(fn ($k) => \App\Support\SiteContent::text($k) !== \App\Support\SiteContent::TEXTS[$k]['default'])->count();
            $images = collect(array_keys(\App\Support\SiteContent::IMAGES))->filter(fn ($k) => \App\Support\SiteContent::uploadedImage($k))->count();
            $icon = 'pen';
            $title = 'Wording and images of the whole system';
            $sub = 'The login page, the sidebar and the letterhead of documents.';
            $facts = ['Changed wording' => $texts . ' of ' . count(\App\Support\SiteContent::TEXTS), 'Uploaded images' => $images . ' of ' . count(\App\Support\SiteContent::IMAGES)];
    }
@endphp
<div class="cms-status">
    <span class="cms-status-icon">{!! \App\Support\SidebarMenu::icon($icon, 'cms-ic') !!}</span>
    <div>
        <div class="cms-status-title">{{ $title }}</div>
        <div class="cms-status-sub">{{ $sub }}</div>
    </div>
    <div class="cms-facts">
        @foreach($facts as $label => $value)
            <span>{{ $label }}: <strong>{{ $value }}</strong></span>
        @endforeach
    </div>
</div>
