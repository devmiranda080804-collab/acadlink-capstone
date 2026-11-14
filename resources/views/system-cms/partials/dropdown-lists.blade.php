{{-- Dropdown Lists: the choices offered across the system, one column per list (Admin only). --}}
<style>
    .lists-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 16px; align-items: start; }
    .lists-grid .site-card { margin: 0; max-width: none; }
    .list-add { display: flex; gap: 8px; align-items: flex-end; flex-wrap: wrap; margin-bottom: 4px; }
    .list-add .site-field { margin: 0; flex: 1; min-width: 110px; }
    .list-add .site-field.code { flex: 0 0 110px; }
    .list-add .btn-save { margin: 0; padding: 9px 14px; }
    .list-rename { display: flex; gap: 6px; margin-top: 6px; }
    .list-rename input { flex: 1; min-width: 0; padding: 6px 8px; border: 1px solid #ccc; border-radius: 5px; font-size: 12px; font-family: Arial, sans-serif; }
    .lists-grid .btn-type:disabled { opacity: .45; cursor: not-allowed; }
    .list-current { display: inline-block; background: #dcfce7; color: #166534; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 10px; margin-left: 6px; }
</style>

<div class="site-card-sub" style="margin-bottom:14px;">
    The choices people pick from in dropdowns across the system. Hiding an item takes it out of new choices only; records that already use it keep it. Nothing here is deleted.
</div>

<div class="lists-grid">
    @foreach(\App\Support\CmsLists::LISTS as $list => $settings)
        @php
            $items = \App\Models\CmsOption::where('list', $list)->orderBy('sort_order')->orderBy('id')->get();
            $usage = \App\Support\CmsLists::usage($list);
            $current = [\App\Support\CmsLists::SCHOOL_YEARS => \App\Support\AcademicTerm::currentSchoolYear(), \App\Support\CmsLists::SEMESTERS => \App\Support\AcademicTerm::currentSemester()][$list] ?? null;
        @endphp
        <div class="site-card" id="list-{{ $list }}">
            <div class="site-card-title">{{ $settings['title'] }}</div>
            <div class="site-card-sub">{{ $settings['hint'] }}</div>

            @if($settings['add'])
                <form method="POST" action="{{ url('/admin/system-cms/lists') }}" class="list-add">
                    @csrf
                    <input type="hidden" name="list" value="{{ $list }}">
                    @if($list === \App\Support\CmsLists::PROGRAMS)
                        <div class="site-field code">
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
            @endif

            <hr class="type-divider">
            <div class="type-list-title">{{ $items->where('is_active', true)->count() }} shown · {{ $items->where('is_active', false)->count() }} hidden</div>
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
    @endforeach
</div>
