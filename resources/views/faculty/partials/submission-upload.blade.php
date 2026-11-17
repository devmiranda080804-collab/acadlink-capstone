{{-- Upload or replace a file for a requirement, optionally for one course.
     The submit button only appears once a file of an allowed type is chosen.
     Params: requirementId, accept, label, courseId (optional),
     current (the file already submitted, if any), returned (sent back to be fixed). --}}
@php
    $current = $current ?? null;
    $returned = (bool) ($returned ?? false);
    $chooseLabel = $returned ? 'Upload corrected file' : ($current ? 'Replace file' : 'Choose File');
@endphp
<form class="upload-form" method="POST" action="{{ url('/faculty/submissions/' . $requirementId) }}" enctype="multipart/form-data"
    @if($current && !$returned)
        data-confirm-template="Replace “{{ $current }}” with “%s”? The Program Head will see the new file instead."
        data-confirm-title="Replace your submitted file?" data-confirm-button="Re-submit"
    @endif>
    @csrf
    @if($courseId ?? null)
        <input type="hidden" name="course_id" value="{{ $courseId }}">
    @endif
    <div class="file-picker">
        <button type="button" class="btn-choose {{ $current && !$returned ? 'is-replace' : '' }}" onclick="this.parentNode.querySelector('input[type=file]').click()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            <span class="btn-choose-text">{{ $chooseLabel }}</span>
        </button>
        <span class="file-picker-name">{{ $current ? '' : 'No file chosen' }}</span>
        <span class="file-chip" hidden>
            <span class="file-chip-name"></span>
            <button type="button" class="file-chip-clear" aria-label="Remove the chosen file" onclick="clearSubmissionFile(this)">×</button>
        </span>
        <input type="file" name="file" accept="{{ $accept ?? '.pdf,.doc,.docx' }}" required onchange="pickSubmissionFile(this)">
    </div>
    <button type="submit" class="btn-submit" hidden>{{ $label }}</button>
    <div class="file-picker-error" role="alert" hidden></div>
</form>

@once
<style>
    .file-chip { display: inline-flex; align-items: center; gap: 6px; max-width: 100%; min-width: 0; padding: 4px 6px 4px 10px; border-radius: 999px; background: #eef2ff; color: #3730a3; font-size: 11.5px; font-weight: 600; }
    .file-chip[hidden] { display: none; }
    .file-chip-name { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; min-width: 0; }
    .file-chip-clear { flex-shrink: 0; width: 20px; height: 20px; border-radius: 50%; border: none; background: rgba(55, 48, 163, .12); color: #3730a3; font-size: 14px; line-height: 1; cursor: pointer; }
    .file-chip-clear:hover { background: rgba(55, 48, 163, .22); }
    .upload-form .btn-submit[hidden] { display: none; }
    .btn-choose.is-replace { background: #fff; }
    .file-picker-error { flex-basis: 100%; font-size: 11.5px; color: #b91c1c; }
    .file-picker-error[hidden] { display: none; }
    .upload-form { flex-wrap: wrap; }
</style>
<script>
    // Shows the chosen file and the submit button; a file of the wrong type is refused here
    // (the server checks again), so nothing is sent until the file is right.
    function pickSubmissionFile(input) {
        var form = input.form;
        var file = input.files && input.files[0];
        var chip = form.querySelector('.file-chip');
        var placeholder = form.querySelector('.file-picker-name');
        var submit = form.querySelector('.btn-submit');
        var error = form.querySelector('.file-picker-error');
        error.hidden = true;
        if (!file) return clearSubmissionFile(chip.querySelector('button'));

        var allowed = (input.getAttribute('accept') || '').toLowerCase().split(',').map(function (s) { return s.trim(); }).filter(Boolean);
        var ext = '.' + (file.name.split('.').pop() || '').toLowerCase();
        if (allowed.length && allowed.indexOf(ext) === -1) {
            input.value = '';
            chip.hidden = true;
            submit.hidden = true;
            placeholder.hidden = false;
            error.textContent = 'This requirement takes ' + allowed.map(function (a) { return a.slice(1).toUpperCase(); }).join(', ') + ' files only. Please choose another file.';
            error.hidden = false;
            return;
        }

        var size = file.size >= 1048576 ? (file.size / 1048576).toFixed(1) + ' MB' : Math.max(1, Math.round(file.size / 1024)) + ' KB';
        chip.querySelector('.file-chip-name').textContent = file.name + ' · ' + size;
        chip.hidden = false;
        placeholder.hidden = true;
        submit.hidden = false;
        // Replacing a submitted file asks first, naming both files.
        var template = form.getAttribute('data-confirm-template');
        if (template) form.setAttribute('data-confirm', template.replace('%s', file.name));
        submit.focus();
    }

    function clearSubmissionFile(button) {
        var form = button.closest('form');
        form.querySelector('input[type=file]').value = '';
        form.querySelector('.file-chip').hidden = true;
        form.querySelector('.file-picker-name').hidden = false;
        form.querySelector('.btn-submit').hidden = true;
        form.querySelector('.file-picker-error').hidden = true;
        form.removeAttribute('data-confirm');
    }

    // Coming back with the browser's Back button can keep a chosen file; show it again.
    window.addEventListener('pageshow', function () {
        document.querySelectorAll('.upload-form input[type=file]').forEach(function (input) {
            if (input.files && input.files.length) pickSubmissionFile(input);
        });
    });
</script>
@endonce
