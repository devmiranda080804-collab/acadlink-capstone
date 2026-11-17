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

@include('faculty.partials.submission-upload-assets')
