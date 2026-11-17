{{-- Dropdown Lists: the choices offered across the system, one list at a time (Admin only).
     Uses the Document Layouts look: pills on top, the list on the left, Add on the right. --}}
<style>
    .list-items .type-row:first-child { border-top: 0; }
    .list-add { display: flex; flex-direction: column; gap: 12px; }
    .list-add .site-field { margin: 0; }
    .list-add .site-field input { width: 100%; box-sizing: border-box; }
    .list-add .btn-save { margin: 0; align-self: flex-start; }
    .list-add-card { border: 1px solid #eef0f5; border-radius: 12px; background: #fafbfd; padding: 16px; }
    .list-add-title { font-size: 13px; font-weight: 800; color: #0f2557; margin-bottom: 10px; }
    .list-summary { display: flex; gap: 8px; flex-wrap: wrap; margin: 16px 0 6px; }
    .list-summary span { font-size: 11.5px; font-weight: 700; padding: 3px 10px; border-radius: 999px; background: #eef2ff; color: #3730a3; }
    .list-summary span.off { background: #f1f2f5; color: #667085; }
    .list-rename { display: flex; gap: 6px; margin-top: 8px; }
    .list-rename input { flex: 1; min-width: 0; padding: 7px 9px; border: 1px solid #ccc; border-radius: 8px; font-size: 12.5px; font-family: inherit; }
    .list-items .btn-type:disabled { opacity: .45; cursor: not-allowed; }
    .list-current { display: inline-block; background: #dcfce7; color: #166534; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 10px; margin-left: 6px; }
</style>

<div class="site-card-sub" style="margin-bottom:14px;">
    The choices people pick from in dropdowns across the system. Hiding an item takes it out of new choices only; records that already use it keep it. Nothing here is deleted.
    Academic years are managed in <a href="{{ url('/admin/settings') }}#school-years" onclick="try { localStorage.setItem('settings-tab', 'term'); } catch (e) {}">Settings → Academic Term</a>.
</div>

@php
    // Academic years moved to Settings → Academic Term; the rest stay here.
    $cmsLists = collect(\App\Support\CmsLists::LISTS)->except(\App\Support\CmsLists::SCHOOL_YEARS)->all();
    $openList = session('cms_list') ?: old('list');
    $openList = isset($cmsLists[$openList]) ? $openList : array_key_first($cmsLists);
    $listItems = collect($cmsLists)->map(fn ($s, $list) => \App\Models\CmsOption::where('list', $list)->orderBy('sort_order')->orderBy('id')->get());
@endphp

<div class="layout-switch" role="tablist" aria-label="Dropdown lists">
    @foreach($cmsLists as $list => $settings)
        <button type="button" role="tab" id="list-tab-{{ $list }}" data-part-group="lists" data-part-key="{{ $list }}" aria-controls="list-{{ $list }}" aria-selected="{{ $list === $openList ? 'true' : 'false' }}" onclick="showCmsPart('lists', '{{ $list }}')">
            {{ $settings['title'] }} <small>{{ $listItems[$list]->where('is_active', true)->count() }}</small>
        </button>
    @endforeach
</div>

@foreach($cmsLists as $list => $settings)
    @php
        $items = $listItems[$list];
        $usage = \App\Support\CmsLists::usage($list);
        $current = [\App\Support\CmsLists::SCHOOL_YEARS => \App\Support\AcademicTerm::currentSchoolYear(), \App\Support\CmsLists::SEMESTERS => \App\Support\AcademicTerm::currentSemester()][$list] ?? null;
    @endphp
    <section class="layout-doc" id="list-{{ $list }}" data-part-panel="lists" data-part-key="{{ $list }}" role="tabpanel" aria-labelledby="list-tab-{{ $list }}" @if($list !== $openList) hidden @endif>
        <div class="layout-doc-head">
            <div>
                <div class="layout-doc-title">{{ $settings['title'] }}</div>
                <div class="layout-doc-sub">{{ $settings['hint'] }}</div>
            </div>
        </div>

        <div class="layout-body">
            <div class="layout-form">
                <div class="list-summary">
                    <span>{{ $items->where('is_active', true)->count() }} shown</span>
                    <span class="off">{{ $items->where('is_active', false)->count() }} hidden</span>
                </div>
                <div class="list-items">
                    @foreach($items as $item)
                        <div class="type-row {{ $item->is_active ? '' : 'is-hidden' }}">
                            <div class="type-text">
                                <div class="type-name">
                                    {{ $item->key }}
                                    @if($current === $item->key)<span class="list-current">Current term</span>@endif
                                    @unless($item->is_active)<span class="type-tag">Hidden</span>@endunless
                                </div>
                                @if($item->label !== $item->key)
                                    <div class="type-meta">{{ $item->label }}</div>
                                @endif
                                <div class="type-meta">Used by {{ $usage[$item->key] ?? 0 }} {{ ($usage[$item->key] ?? 0) === 1 ? 'record' : 'records' }}</div>
                                @if($settings['rename'])
                                    <form method="POST" action="{{ url('/admin/system-cms/lists/' . $item->id) }}" class="list-rename" id="rename-{{ $item->id }}" style="display:none;">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="label" maxlength="200" value="{{ $item->label }}" aria-label="Full name of {{ $item->key }}" required>
                                        <button type="submit" class="btn-type">Save</button>
                                    </form>
                                @endif
                            </div>
                            <div class="type-actions">
                                @if($settings['rename'])
                                    <button type="button" class="btn-type" onclick="var f = document.getElementById('rename-{{ $item->id }}'); f.style.display = f.style.display === 'none' ? 'flex' : 'none';">Rename</button>
                                @endif
                                <form method="POST" action="{{ url('/admin/system-cms/lists/' . $item->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="is_active" value="{{ $item->is_active ? 0 : 1 }}">
                                    <button type="submit" class="btn-type {{ $item->is_active ? 'danger' : '' }}" @disabled($item->is_active && $current === $item->key) title="{{ $item->is_active && $current === $item->key ? 'This is the current term. Change the term in Settings first.' : '' }}">{{ $item->is_active ? 'Hide' : 'Show' }}</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="layout-aside">
                @if($settings['add'])
                    <div class="list-add-card">
                        <div class="list-add-title">Add to {{ $settings['title'] }}</div>
                        <form method="POST" action="{{ url('/admin/system-cms/lists') }}" class="list-add">
                            @csrf
                            <input type="hidden" name="list" value="{{ $list }}">
                            @if($list === \App\Support\CmsLists::PROGRAMS)
                                <div class="site-field">
                                    <label for="add-{{ $list }}-key">Code</label>
                                    <input type="text" id="add-{{ $list }}-key" name="key" maxlength="20" placeholder="e.g. BSBA-OM" value="{{ old('list') === $list ? old('key') : '' }}" required>
                                </div>
                                <div class="site-field">
                                    <label for="add-{{ $list }}-label">Full name</label>
                                    <input type="text" id="add-{{ $list }}-label" name="label" maxlength="200" placeholder="e.g. Bachelor of Science in…" value="{{ old('list') === $list ? old('label') : '' }}" required>
                                </div>
                            @else
                                <div class="site-field">
                                    <label for="add-{{ $list }}-key">New academic year</label>
                                    <input type="text" id="add-{{ $list }}-key" name="key" maxlength="9" placeholder="e.g. {{ (int) explode('-', \App\Support\AcademicTerm::currentSchoolYear())[0] + 2 }}-{{ (int) explode('-', \App\Support\AcademicTerm::currentSchoolYear())[0] + 3 }}" value="{{ old('list') === $list ? old('key') : '' }}" required>
                                </div>
                            @endif
                            <button type="submit" class="btn-save">Add</button>
                        </form>
                    </div>
                @else
                    <div class="list-add-card">
                        <div class="list-add-title">A fixed list</div>
                        <div class="site-hint" style="margin:0;">Items here can't be added, only hidden or shown again.</div>
                    </div>
                @endif
                <div class="layout-aside-note">Hidden items stay on the records that already use them.</div>
            </div>
        </div>
    </section>
@endforeach

<script>
    // One list at a time (partials.switcher): after a change, the same list; else the last used.
    restoreCmsPart('lists', @json((bool) (session('cms_list') || old('list'))));
</script>
