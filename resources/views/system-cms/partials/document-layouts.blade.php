{{-- Document Layouts: one column per downloaded document, with its header, contents and footer (Admin only). --}}
@php use App\Support\DocumentLayouts; use App\Http\Controllers\Admin\DocumentLayoutController as Layouts; @endphp
<style>
    .layouts-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 16px; align-items: start; }
    .layouts-grid .site-card { margin: 0; max-width: none; }
    .layout-section { font-size: 11px; font-weight: 700; color: #0f2557; text-transform: uppercase; letter-spacing: .4px; margin: 18px 0 8px; padding-top: 12px; border-top: 1px solid #eee; }
    .layout-check { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: #333; margin-bottom: 12px; cursor: pointer; }
    .layout-check input { width: auto; margin: 0; }
    .layout-image { display: flex; gap: 10px; align-items: center; margin-bottom: 12px; }
    .layout-image img { width: 52px; height: 52px; object-fit: contain; border: 1px solid #e5e7eb; border-radius: 6px; background: #fff; padding: 3px; flex-shrink: 0; }
    .layout-image .site-field { margin: 0; flex: 1; min-width: 0; }
    .layout-image input[type=file] { font-size: 11.5px; max-width: 100%; }
    .layout-changed { display: inline-block; background: #fef3c7; color: #92400e; font-size: 9.5px; font-weight: 700; padding: 1px 6px; border-radius: 8px; margin-left: 4px; vertical-align: middle; }
    .layout-actions { display: flex; gap: 8px; align-items: center; justify-content: space-between; flex-wrap: wrap; margin-top: 16px; }
    .layout-actions .btn-save { margin: 0; }
    /* A small picture of the letterhead and footer, updated as the Admin types. */
    .lh-preview { border: 1px solid #e5e7eb; border-radius: 8px; background: #fff; padding: 10px 12px; margin-bottom: 6px; font-family: Calibri, Arial, sans-serif; }
    .lh-top { display: flex; justify-content: space-between; align-items: flex-end; gap: 8px; }
    .lh-left { display: flex; align-items: center; gap: 6px; min-width: 0; }
    .lh-left img { height: 30px; }
    .lh-uni { color: #C12035; font-weight: 700; font-size: 12px; line-height: 1; text-transform: uppercase; }
    .lh-note { color: #C12035; font-size: 6.5px; }
    .lh-right { text-align: right; min-width: 0; }
    .lh-right img { height: 22px; }
    .lh-college { font-family: Tahoma, sans-serif; font-size: 6.5px; text-transform: uppercase; }
    .lh-rule { border-bottom: 3px solid #273F89; margin: 4px 0 8px; }
    .lh-body { font-size: 9px; color: #bbb; text-align: center; padding: 6px 0 10px; }
    .lh-foot { display: flex; justify-content: space-between; align-items: flex-end; gap: 6px; font-size: 7px; }
    .lh-tag-first { color: #273F89; font-weight: 700; }
    .lh-tag-rest { color: #C12035; font-weight: 700; }
    .lh-page { color: #555; }
    .lh-contact { color: #C12035; text-align: right; white-space: pre-line; }
</style>

<div class="site-card-sub" style="margin-bottom:14px;">
    One column per downloaded document. Change the header, contents or footer of a document and the next download uses it. Anything you don't change follows Site Content and the original wording.
</div>

<div class="layouts-grid">
    @foreach(DocumentLayouts::DOCUMENTS as $doc => $meta)
        @php $fields = DocumentLayouts::fields($doc); @endphp
        <div class="site-card" id="layout-{{ $doc }}">
            <div class="site-card-title">{{ $meta['title'] }}</div>
            <div class="site-card-sub">{{ $meta['hint'] }}</div>

            <div class="lh-preview" aria-label="Preview of the {{ $meta['title'] }} header and footer">
                <div class="lh-top">
                    <div class="lh-left">
                        <img src="{{ DocumentLayouts::imageUrl($doc, 'header.logo_left') }}" alt="" data-preview="{{ $doc }}-logo_left">
                        <div>
                            <div class="lh-uni" data-preview="{{ $doc }}-university_name">{{ DocumentLayouts::text($doc, 'header.university_name') }}</div>
                            <div class="lh-note" data-preview="{{ $doc }}-university_note">{{ DocumentLayouts::text($doc, 'header.university_note') }}</div>
                        </div>
                    </div>
                    <div class="lh-right" data-preview="{{ $doc }}-right" @if(isset($fields['header.right_logo']) && !DocumentLayouts::enabled($doc, 'header.right_logo')) style="visibility:hidden" @endif>
                        <img src="{{ DocumentLayouts::imageUrl($doc, 'header.logo_right') }}" alt="" data-preview="{{ $doc }}-logo_right">
                        <div class="lh-college" data-preview="{{ $doc }}-college_name">{{ DocumentLayouts::text($doc, 'header.college_name') }}</div>
                    </div>
                </div>
                <div class="lh-rule"></div>
                <div class="lh-body">… {{ $meta['title'] }} content …</div>
                @if(DocumentLayouts::hasFooter($doc))
                <div class="lh-foot">
                    <div><span class="lh-tag-first" data-preview="{{ $doc }}-tag_first"></span><span class="lh-tag-rest" data-preview="{{ $doc }}-tag_rest"></span></div>
                    @if(isset($fields['footer.page_numbers']))
                        <div class="lh-page" data-preview="{{ $doc }}-page_numbers" @if(!DocumentLayouts::enabled($doc, 'footer.page_numbers')) style="visibility:hidden" @endif>Page 1 of 2</div>
                    @endif
                    <div class="lh-contact" data-preview="{{ $doc }}-contact">{{ DocumentLayouts::text($doc, 'footer.contact') }}</div>
                </div>
                @endif
            </div>

            <form method="POST" action="{{ url('/admin/system-cms/layouts/' . $doc) }}" enctype="multipart/form-data" data-layout="{{ $doc }}">
                @csrf
                @method('PUT')
                @foreach(DocumentLayouts::SECTIONS as $sectionKey => $sectionLabel)
                    @php $sectionFields = array_filter($fields, fn ($f) => $f['section'] === $sectionKey); @endphp
                    @continue(!$sectionFields)
                    <div class="layout-section">{{ $sectionLabel }}</div>
                    @foreach($sectionFields as $field => $f)
                        @php
                            $name = Layouts::inputName($field);
                            $id = 'layout-' . $doc . '-' . $name;
                            $changed = DocumentLayouts::isChanged($doc, $field);
                        @endphp
                        @if($f['type'] === 'image')
                            <div class="layout-image">
                                <img src="{{ DocumentLayouts::imageUrl($doc, $field) }}" alt="{{ $f['label'] }}">
                                <div class="site-field">
                                    <label for="{{ $id }}">{{ $f['label'] }} @if($changed)<span class="layout-changed">Changed</span>@endif</label>
                                    <input type="file" id="{{ $id }}" name="{{ $name }}" accept=".png" data-image="{{ $doc }}-{{ str_replace('header.', '', $field) }}">
                                    <div class="site-hint" style="margin:2px 0 0;">{{ $f['hint'] ?? '' }}</div>
                                    @if($changed)
                                        <label class="image-reset"><input type="checkbox" name="reset_{{ $name }}" value="1"> Use the Site Content logo again</label>
                                    @endif
                                </div>
                            </div>
                        @elseif($f['type'] === 'bool')
                            <label class="layout-check">
                                <input type="hidden" name="{{ $name }}" value="0">
                                <input type="checkbox" name="{{ $name }}" value="1" @checked(DocumentLayouts::enabled($doc, $field)) data-field="{{ $doc }}-{{ $field }}">
                                {{ $f['label'] }}
                            </label>
                        @else
                            <div class="site-field">
                                <label for="{{ $id }}">{{ $f['label'] }} @if($changed)<span class="layout-changed">Changed</span>@endif</label>
                                @if($f['type'] === 'textarea')
                                    <textarea id="{{ $id }}" name="{{ $name }}" rows="3" maxlength="500" data-field="{{ $doc }}-{{ $field }}">{{ DocumentLayouts::text($doc, $field) }}</textarea>
                                @elseif($f['type'] === 'select')
                                    <select id="{{ $id }}" name="{{ $name }}">
                                        @foreach(\App\Support\ExamPrintOptions::choices($f['options']) as $value => $label)
                                            <option value="{{ $value }}" @selected((string) DocumentLayouts::text($doc, $field) === (string) $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="text" id="{{ $id }}" name="{{ $name }}" maxlength="200" value="{{ DocumentLayouts::text($doc, $field) }}" data-field="{{ $doc }}-{{ $field }}">
                                @endif
                                @if(!empty($f['hint']))<div class="site-hint">{{ $f['hint'] }}</div>@endif
                            </div>
                        @endif
                    @endforeach
                @endforeach

                <div class="layout-actions">
                    <button type="submit" class="btn-save">Save {{ $meta['title'] }}</button>
                    @if(!empty($meta['template']))
                        <a class="btn-type" href="{{ url('/admin/system-cms/layouts/' . $doc . '/template') }}" title="Save first; the template uses the saved layout">⬇ Download template</a>
                    @endif
                    <button type="submit" name="reset" value="1" class="btn-type danger" formnovalidate
                        onclick="return confirm('Put the {{ $meta['title'] }} back to the default header, contents and footer?')">Reset to default</button>
                </div>
            </form>
        </div>
    @endforeach
</div>

<script>
    // Keeps each column's small preview in step with what the Admin types or picks.
    (function () {
        function set(doc, key, text) {
            var el = document.querySelector('[data-preview="' + doc + '-' + key + '"]');
            if (el) el.textContent = text;
        }
        function tagline(doc, value) {
            var parts = value.trim().split(' ');
            set(doc, 'tag_first', parts.shift() || '');
            set(doc, 'tag_rest', parts.length ? ' ' + parts.join(' ') : '');
        }
        document.querySelectorAll('form[data-layout]').forEach(function (form) {
            var doc = form.dataset.layout;
            form.querySelectorAll('[data-field]').forEach(function (input) {
                var field = input.dataset.field.slice(doc.length + 1);
                var update = function () {
                    if (field === 'footer.tagline') return tagline(doc, input.value);
                    if (field === 'footer.contact') return set(doc, 'contact', input.value);
                    if (field === 'footer.page_numbers') {
                        var page = document.querySelector('[data-preview="' + doc + '-page_numbers"]');
                        if (page) page.style.visibility = input.checked ? 'visible' : 'hidden';
                        return;
                    }
                    if (field === 'header.right_logo') {
                        var right = document.querySelector('[data-preview="' + doc + '-right"]');
                        if (right) right.style.visibility = input.checked ? 'visible' : 'hidden';
                        return;
                    }
                    if (field.indexOf('header.') === 0) set(doc, field.slice(7), input.value);
                };
                input.addEventListener('input', update);
                input.addEventListener('change', update);
                if (field === 'footer.tagline') update();
            });
            form.querySelectorAll('[data-image]').forEach(function (input) {
                input.addEventListener('change', function () {
                    var file = input.files && input.files[0];
                    var img = document.querySelector('[data-preview="' + input.dataset.image + '"]');
                    if (file && img) img.src = URL.createObjectURL(file);
                });
            });
        });
    })();
</script>
