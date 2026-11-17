{{-- Styles and script of the submission upload forms, put on the page once however many forms use them. --}}
@php $subUploadAssets = !request()->attributes->get('sub_upload_assets'); request()->attributes->set('sub_upload_assets', true); @endphp
@if($subUploadAssets)
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
    .course-upload { margin-top: 12px; padding: 12px 14px; border: 1px solid #eef0f5; border-radius: 12px; background: #fafbfd; }
    .course-pick { display: flex; align-items: center; gap: 8px; flex-basis: 100%; min-width: 0; }
    .course-pick label { font-size: 11.5px; font-weight: 700; color: #475467; }
    .course-pick select { flex: 1; min-width: 0; max-width: 520px; padding: 7px 10px; border: 1px solid #d5d9e3; border-radius: 8px; font-size: 12.5px; font-family: inherit; background: #fff; }
    .course-all-set { margin-top: 12px; font-size: 12px; color: #166534; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 9px 12px; }
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

    // Per-course form: the chosen course decides how it reads. Your own file: Replace file,
    // and Re-submit asks first naming it; returned: Upload corrected file, no question;
    // no file yet: Choose File and Submit.
    function pickSubmissionCourse(select) {
        var form = select.form;
        var opt = select.options[select.selectedIndex];
        var state = opt ? opt.dataset.state : 'missing';
        var current = opt ? opt.dataset.current : '';
        form.querySelector('.btn-choose-text').textContent = state === 'returned' ? 'Upload corrected file' : (state === 'mine' ? 'Replace file' : 'Choose File');
        form.querySelector('.btn-choose').classList.toggle('is-replace', state === 'mine');
        form.querySelector('.btn-submit').textContent = state === 'missing' ? 'Submit' : 'Re-submit';
        if (state === 'mine' && current) {
            form.setAttribute('data-confirm-template', 'Replace “' + current + '” with “%s”? The Program Head will see the new file instead.');
            form.setAttribute('data-confirm-title', 'Replace your submitted file?');
            form.setAttribute('data-confirm-button', 'Re-submit');
        } else {
            ['data-confirm-template', 'data-confirm-title', 'data-confirm-button', 'data-confirm'].forEach(function (a) { form.removeAttribute(a); });
        }
        var input = form.querySelector('input[type=file]');
        if (input.files && input.files.length) pickSubmissionFile(input);
    }
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.course-upload select[name=course_id]').forEach(pickSubmissionCourse);
    });

    // Coming back with the browser's Back button can keep a chosen file; show it again.
    window.addEventListener('pageshow', function () {
        document.querySelectorAll('.upload-form input[type=file]').forEach(function (input) {
            if (input.files && input.files.length) pickSubmissionFile(input);
        });
    });
</script>
@endif
