{{-- Document Layouts: one column per downloaded document, with its header, contents and footer (Admin only). --}}
@php use App\Support\DocumentLayouts; use App\Http\Controllers\Admin\DocumentLayoutController as Layouts; @endphp
<style>
    /* Pick a document, then edit it: the form on the left, its preview on the right. */
    .layout-switch { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
    .layout-switch button { display: inline-flex; align-items: center; gap: 8px; padding: 9px 14px; border-radius: 999px; border: 1px solid #dfe3ec; background: #fff; color: #344054;
        font: 600 13px inherit; font-family: inherit; cursor: pointer; transition: background .15s, border-color .15s, color .15s; }
    .layout-switch button:hover { border-color: #c9d1e2; background: #f8f9fc; }
    .layout-switch button[aria-selected="true"] { background: #0f2557; border-color: #0f2557; color: #fff; }
    .layout-switch .dot { width: 7px; height: 7px; border-radius: 50%; background: #f59e0b; }
    .layout-switch small { font-size: 10.5px; font-weight: 700; padding: 1px 7px; border-radius: 999px; background: #eef2ff; color: #3730a3; }
    .layout-switch button[aria-selected="true"] small { background: rgba(255,255,255,.18); color: #fff; }

    .layout-doc { background: #fff; border: 1px solid #e7eaf1; border-radius: 16px; padding: 22px 24px; }
    .layout-doc[hidden] { display: none; }
    .layout-doc-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; padding-bottom: 16px; margin-bottom: 4px; border-bottom: 1px solid #eef0f5; }
    .layout-doc-title { font-size: 18px; font-weight: 800; color: #0f2557; }
    .layout-doc-sub { font-size: 12.5px; color: #667085; margin-top: 3px; }
    .layout-body { display: grid; grid-template-columns: minmax(0, 1fr) minmax(300px, 400px); grid-template-areas: "form aside"; gap: 28px; align-items: start; }
    .layout-form { grid-area: form; min-width: 0; }
    .layout-aside { grid-area: aside; position: sticky; top: 0; padding-top: 18px; }
    .layout-aside-title { font-size: 11px; font-weight: 700; color: #667085; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 8px; }
    .layout-aside-note { font-size: 11.5px; color: #8a90a2; line-height: 1.5; margin-top: 10px; }

    .layout-section { font-size: 11px; font-weight: 800; color: #0f2557; text-transform: uppercase; letter-spacing: .5px; margin: 20px 0 12px; display: flex; align-items: center; gap: 10px; }
    .layout-section::after { content: ""; flex: 1; height: 1px; background: #eef0f5; }
    .layout-fields { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px 18px; }
    .layout-fields .site-field { margin: 0; min-width: 0; }
    .layout-fields .wide { grid-column: 1 / -1; }
    .layout-fields input[type=text], .layout-fields select, .layout-fields textarea { width: 100%; box-sizing: border-box; }
    .layout-check { display: flex; align-items: center; gap: 9px; font-size: 13px; color: #333; cursor: pointer; padding: 11px 12px; border: 1px solid #eef0f5; border-radius: 10px; background: #fafbfd; }
    .layout-check input { width: auto; margin: 0; accent-color: #0f2557; }
    .layout-image { display: flex; gap: 12px; align-items: center; padding: 12px; border: 1px solid #eef0f5; border-radius: 12px; background: #fafbfd; min-width: 0; }
    .layout-image img { width: 56px; height: 56px; object-fit: contain; border: 1px solid #e5e7eb; border-radius: 8px; background: #fff; padding: 4px; flex-shrink: 0; }
    .layout-image .site-field { margin: 0; flex: 1; min-width: 0; }
    .layout-image input[type=file] { font-size: 11.5px; max-width: 100%; }
    .layout-changed { display: inline-block; background: #fef3c7; color: #92400e; font-size: 9.5px; font-weight: 700; padding: 1px 6px; border-radius: 8px; margin-left: 4px; vertical-align: middle; }
    .layout-actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; margin-top: 22px; padding-top: 16px; border-top: 1px solid #eef0f5; }
    .layout-actions .btn-save { margin: 0; }
    .layout-actions .danger { margin-left: auto; }
    @media (max-width: 1100px) { .layout-body { grid-template-columns: 1fr; grid-template-areas: "aside" "form"; } .layout-aside { position: static; } }
    @media (max-width: 640px) { .layout-fields { grid-template-columns: 1fr; } .layout-doc { padding: 16px; } .layout-actions .danger { margin-left: 0; } }
    /* A small picture of the letterhead and footer, updated as the Admin types. */
    .lh-preview { border: 1px solid #e5e7eb; border-radius: 10px; background: #fff; padding: 14px 16px; font-family: Calibri, Arial, sans-serif; box-shadow: 0 6px 18px rgba(15, 37, 87, .06); }
    .lh-top { display: flex; justify-content: space-between; align-items: flex-end; gap: 8px; }
    .lh-left { display: flex; align-items: center; gap: 6px; min-width: 0; }
    .lh-left img { height: 38px; }
    .lh-uni { color: #C12035; font-weight: 700; font-size: 14px; line-height: 1; text-transform: uppercase; }
    .lh-note { color: #C12035; font-size: 7.5px; }
    .lh-right { text-align: right; min-width: 0; }
    .lh-right img { height: 28px; }
    .lh-college { font-family: Tahoma, sans-serif; font-size: 7.5px; text-transform: uppercase; }
    .lh-rule { border-bottom: 3px solid #273F89; margin: 4px 0 8px; }
    .lh-body { font-size: 10px; color: #bbb; text-align: center; padding: 40px 0 46px; }
    .lh-foot { display: flex; justify-content: space-between; align-items: flex-end; gap: 6px; font-size: 7.5px; }
    .lh-tag-first { color: #273F89; font-weight: 700; }
    .lh-tag-rest { color: #C12035; font-weight: 700; }
    .lh-page { color: #555; }
    .lh-contact { color: #C12035; text-align: right; white-space: pre-line; }
</style>

<div class="site-card-sub" style="margin-bottom:14px;">
    Pick a document, change its header, contents or footer, and the next download uses it. Anything you don't change follows Site Content and the original wording.
</div>

@php
    $layoutDocs = DocumentLayouts::DOCUMENTS;
    $openLayout = session('cms_layout');
    $openLayout = isset($layoutDocs[$openLayout]) ? $openLayout : array_key_first($layoutDocs);
@endphp
<div class="layout-switch" role="tablist" aria-label="Documents">
    @foreach($layoutDocs as $doc => $meta)
        @php $docChanged = collect(DocumentLayouts::fields($doc))->keys()->contains(fn ($field) => DocumentLayouts::isChanged($doc, $field)); @endphp
        <button type="button" role="tab" id="layout-tab-{{ $doc }}" data-layout-tab="{{ $doc }}" aria-controls="layout-{{ $doc }}" aria-selected="{{ $doc === $openLayout ? 'true' : 'false' }}" onclick="showLayoutDoc('{{ $doc }}')">
            {{ $meta['title'] }}
            @if(!empty($meta['template']))<small>Template</small>@endif
            @if($docChanged)<span class="dot" title="Changed from the default"></span>@endif
        </button>
    @endforeach
</div>

<div class="layouts-docs">
    @foreach($layoutDocs as $doc => $meta)
        @php $fields = DocumentLayouts::fields($doc); @endphp
        <section class="layout-doc" id="layout-{{ $doc }}" role="tabpanel" aria-labelledby="layout-tab-{{ $doc }}" @if($doc !== $openLayout) hidden @endif>
            <div class="layout-doc-head">
                <div>
                    <div class="layout-doc-title">{{ $meta['title'] }}</div>
                    <div class="layout-doc-sub">{{ $meta['hint'] }}</div>
                </div>
            </div>

            <div class="layout-body">
            <div class="layout-aside">
            <div class="layout-aside-title">Preview</div>
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
            <div class="layout-aside-note">Updates as you type. The real file uses the same header and footer on every page.</div>
            </div>

            <form class="layout-form" method="POST" action="{{ url('/admin/system-cms/layouts/' . $doc) }}" enctype="multipart/form-data" data-layout="{{ $doc }}">
                @csrf
                @method('PUT')
                @foreach(DocumentLayouts::SECTIONS as $sectionKey => $sectionLabel)
                    @php $sectionFields = array_filter($fields, fn ($f) => $f['section'] === $sectionKey); @endphp
                    @continue(!$sectionFields)
                    <div class="layout-section">{{ $sectionLabel }}</div>
                    <div class="layout-fields">
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
                            <label class="layout-check wide">
                                <input type="hidden" name="{{ $name }}" value="0">
                                <input type="checkbox" name="{{ $name }}" value="1" @checked(DocumentLayouts::enabled($doc, $field)) data-field="{{ $doc }}-{{ $field }}">
                                {{ $f['label'] }}
                            </label>
                        @else
                            <div class="site-field {{ $f['type'] === 'textarea' ? 'wide' : '' }}">
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
                    </div>
                @endforeach

                <div class="layout-actions">
                    <button type="submit" class="btn-save">Save {{ $meta['title'] }}</button>
                    @if(!empty($meta['template']))
                        <a class="btn-type" href="{{ url('/admin/system-cms/layouts/' . $doc . '/template') }}" title="Save first; the template uses the saved layout">⬇ Download template</a>
                    @endif
                    <button type="submit" name="reset" value="1" class="btn-type danger" formnovalidate
                        data-confirm="Put the {{ $meta['title'] }} back to the default header, contents and footer? Your changes to this layout will be lost." data-confirm-title="Reset to default?" data-confirm-button="Reset" data-loading-text="Resetting…">Reset to default</button>
                </div>
            </form>
            </div>
        </section>
    @endforeach
</div>

<script>
    // One document at a time; the last one opened is remembered on this browser.
    function showLayoutDoc(doc) {
        document.querySelectorAll('[data-layout-tab]').forEach(function (tab) {
            tab.setAttribute('aria-selected', tab.dataset.layoutTab === doc ? 'true' : 'false');
        });
        document.querySelectorAll('.layout-doc').forEach(function (panel) {
            panel.hidden = panel.id !== 'layout-' + doc;
        });
        try { localStorage.setItem('cms-layout', doc); } catch (e) {}
    }
    (function () {
        // After saving, the server reopens the saved one; otherwise the last one used.
        if (@json((bool) session('cms_layout'))) return;
        var last = null;
        try { last = localStorage.getItem('cms-layout'); } catch (e) {}
        if (last && document.getElementById('layout-' + last)) showLayoutDoc(last);
    })();

    // Keeps each document's preview in step with what the Admin types or picks.
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
