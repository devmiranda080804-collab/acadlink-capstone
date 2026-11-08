{{-- Template Elements: the kinds of template the Secretary can upload (moved here from Settings). --}}
<style>
        .type-add-row { display: grid; grid-template-columns: 1fr 96px; gap: 12px; }
        .type-divider { border: none; border-top: 1px solid #eee; margin: 20px 0 14px; }
        .type-list-title { font-size: 12px; font-weight: 700; color: #333; margin-bottom: 8px; }
        .type-row { display: flex; align-items: center; gap: 10px; padding: 9px 0; border-top: 1px solid #f0f0f0; }
        .type-row.is-hidden .type-icon, .type-row.is-hidden .type-text { opacity: .5; }
        .type-icon { font-size: 18px; width: 24px; text-align: center; flex-shrink: 0; }
        .type-text { flex: 1; min-width: 0; }
        .type-name { font-size: 13px; font-weight: 600; color: #1a1a2e; }
        .type-meta { font-size: 11px; color: #888; margin-top: 2px; }
        .type-tag { display: inline-block; background: #eef1f7; color: #555; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 10px; margin-left: 6px; }
        .type-actions { display: flex; gap: 6px; flex-shrink: 0; }
        .type-actions form { margin: 0; }
        .btn-type { background: #fff; border: 1px solid #ccc; color: #444; font-size: 11.5px; font-weight: 600; padding: 5px 10px; border-radius: 5px; cursor: pointer; font-family: Arial, sans-serif; }
        .btn-type:hover { background: #f5f6fa; }
        .btn-type.danger { border-color: #fca5a5; color: #b91c1c; }
        .btn-type.danger:hover { background: #fee2e2; }
        .type-modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 9000; align-items: center; justify-content: center; }
        .type-modal-overlay.open { display: flex; }
        .type-modal { background: #fff; border-radius: 10px; padding: 22px 24px; width: 460px; max-width: 92vw; }
        .type-modal-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 4px; }
        .site-card-title { font-size: 14px; font-weight: 700; color: #1a1a2e; margin-bottom: 4px; }
        .site-card-sub { font-size: 11.5px; color: #777; line-height: 1.5; margin-bottom: 16px; }
        .site-field select, .site-field textarea { width: 100%; padding: 9px 11px; border: 1px solid #ccc; border-radius: 6px; font-size: 13px; font-family: Arial, sans-serif; outline: none; background: #fff; box-sizing: border-box; }
        .site-field input[type=text] { box-sizing: border-box; }
</style>

            <div class="site-card">
                <div class="site-card-title">Template Types</div>
                <div class="site-card-sub">
                    The kinds of template the Secretary can upload. A new type is available right away for uploads, folders, the Document Repository and submission requirements.
                </div>

                <form method="POST" action="{{ url('/admin/template-types') }}">
                    @csrf
                    <div class="type-add-row">
                        <div class="site-field">
                            <label for="type_label">New type name</label>
                            <input type="text" id="type_label" name="type_label" maxlength="60" value="{{ old('type_label') }}" placeholder="e.g. Grade Sheet" required>
                        </div>
                        <div class="site-field">
                            <label for="type_icon">Icon</label>
                            <select id="type_icon" name="type_icon">
                                @foreach(\App\Models\TemplateType::ICONS as $icon)
                                    <option value="{{ $icon }}" @selected(old('type_icon', '📄') === $icon)>{{ $icon }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="site-field">
                        <label for="type_description">What this document normally contains (optional)</label>
                        <textarea id="type_description" name="type_description" rows="2" maxlength="500" placeholder="e.g. a Grade Sheet — a table listing students and their grades per term">{{ old('type_description') }}</textarea>
                        <div class="site-hint">Used to check that uploaded PDF and Word files match this type. Leave blank to skip the check.</div>
                    </div>
                    <button type="submit" class="btn-save">Add Type</button>
                </form>

                <hr class="type-divider">
                <div class="type-list-title">Current types</div>
                @foreach($templateTypes as $type)
                    @php $usage = $templateTypeUsage[$type->key] ?? 0; @endphp
                    <div class="type-row {{ $type->is_active ? '' : 'is-hidden' }}">
                        <span class="type-icon">{{ $type->icon }}</span>
                        <div class="type-text">
                            <div class="type-name">
                                {{ $type->label }}
                                @unless($type->is_active)<span class="type-tag">Hidden</span>@endunless
                            </div>
                            <div class="type-meta">{{ $usage }} {{ \Illuminate\Support\Str::plural('record', $usage) }}</div>
                        </div>
                        <div class="type-actions">
                            <button type="button" class="btn-type" data-url="{{ url('/admin/template-types/' . $type->id) }}" data-label="{{ $type->label }}" data-icon="{{ $type->icon }}" data-description="{{ $type->description }}" onclick="openTypeEdit(this)">Edit</button>
                            @unless($type->isProtected())
                                <form method="POST" action="{{ url('/admin/template-types/' . $type->id . '/toggle') }}">
                                    @csrf
                                    <button type="submit" class="btn-type">{{ $type->is_active ? 'Hide' : 'Show' }}</button>
                                </form>
                                @if($usage === 0)
                                    <form method="POST" action="{{ url('/admin/template-types/' . $type->id) }}" data-confirm="Delete the type {{ $type->label }}?" data-confirm-title="Delete type?" data-confirm-button="Delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-type danger">Delete</button>
                                    </form>
                                @endif
                            @endunless
                        </div>
                    </div>
                @endforeach
            </div>


    <div class="type-modal-overlay" id="type-edit-overlay">
        <div class="type-modal">
            <div class="site-card-title" style="margin-bottom:14px;">Edit Template Type</div>
            <form method="POST" id="type-edit-form">
                @csrf
                @method('PUT')
                <div class="type-add-row">
                    <div class="site-field">
                        <label for="edit_type_label">Type name</label>
                        <input type="text" id="edit_type_label" name="type_label" maxlength="60" required>
                    </div>
                    <div class="site-field">
                        <label for="edit_type_icon">Icon</label>
                        <select id="edit_type_icon" name="type_icon">
                            @foreach(\App\Models\TemplateType::ICONS as $icon)
                                <option value="{{ $icon }}">{{ $icon }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="site-field">
                    <label for="edit_type_description">What this document normally contains (optional)</label>
                    <textarea id="edit_type_description" name="type_description" rows="4" maxlength="500"></textarea>
                    <div class="site-hint">Used to check that uploaded PDF and Word files match this type. Leave blank to skip the check.</div>
                </div>
                <div class="type-modal-actions">
                    <button type="button" class="btn-type" onclick="closeTypeEdit()">Cancel</button>
                    <button type="submit" class="btn-save">Save</button>
                </div>
            </form>
        </div>
    </div>


<script>
        function openTypeEdit(button) {
            document.getElementById('type-edit-form').action = button.dataset.url;
            document.getElementById('edit_type_label').value = button.dataset.label;
            document.getElementById('edit_type_icon').value = button.dataset.icon;
            document.getElementById('edit_type_description').value = button.dataset.description || '';
            document.getElementById('type-edit-overlay').classList.add('open');
        }
        function closeTypeEdit() { document.getElementById('type-edit-overlay').classList.remove('open'); }
</script>
