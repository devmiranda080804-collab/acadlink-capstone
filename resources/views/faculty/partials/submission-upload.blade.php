{{-- Upload or replace a file for a requirement, optionally for one course. --}}
<form class="upload-form" method="POST" action="{{ url('/faculty/submissions/' . $requirementId) }}" enctype="multipart/form-data">
    @csrf
    @if($courseId ?? null)
        <input type="hidden" name="course_id" value="{{ $courseId }}">
    @endif
    <div class="file-picker">
        <button type="button" class="btn-choose" onclick="this.nextElementSibling.nextElementSibling.click()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            Choose File
        </button>
        <span class="file-picker-name">No file chosen</span>
        <input type="file" name="file" accept="{{ $accept ?? '.pdf,.doc,.docx' }}" required onchange="this.previousElementSibling.textContent = this.files[0] ? this.files[0].name : 'No file chosen'">
    </div>
    <button type="submit" class="btn-submit">{{ $label }}</button>
</form>
