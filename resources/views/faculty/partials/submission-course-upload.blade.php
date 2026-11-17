{{-- Per-course requirement: one upload, with the course picked from a dropdown.
     Only courses the faculty member can still send for are offered: no file yet, their own
     file (replace), or a file returned to them (upload a corrected one). A course a
     co-instructor already covered isn't listed; it counts for them already.
     Params: requirementId, accept, rows (each ['course' => Course, 'submission' => ?Submission]). --}}
@php
    $me = auth()->id();
    $options = collect($rows)
        ->filter(fn ($row) => !$row['submission'] || $row['submission']->faculty_id === $me)
        ->map(function ($row) {
            $sub = $row['submission'];
            $state = !$sub ? 'missing' : ($sub->is_returned ? 'returned' : 'mine');

            return ['course' => $row['course'], 'sub' => $sub, 'state' => $state, 'order' => ['missing' => 0, 'returned' => 1, 'mine' => 2][$state]];
        })
        ->sortBy([['order', 'asc'], [fn ($a, $b) => strcmp($a['course']->code, $b['course']->code)]])
        ->values();
    $first = $options->first();
@endphp

@if($options->isEmpty())
    <div class="course-all-set">Every course you teach already has a file for this requirement.</div>
@else
<form class="upload-form course-upload" method="POST" action="{{ url('/faculty/submissions/' . $requirementId) }}" enctype="multipart/form-data">
    @csrf
    <div class="course-pick">
        <label for="course-pick-{{ $requirementId }}">Course</label>
        <select id="course-pick-{{ $requirementId }}" name="course_id" required onchange="pickSubmissionCourse(this)">
            @foreach($options as $opt)
                <option value="{{ $opt['course']->id }}"
                    data-state="{{ $opt['state'] }}"
                    data-current="{{ $opt['state'] === 'mine' ? $opt['sub']->file_name : '' }}">
                    {{ $opt['course']->code }} — {{ $opt['course']->title }} ·
                    @if($opt['state'] === 'missing') Not yet submitted
                    @elseif($opt['state'] === 'returned') Returned, upload a corrected file
                    @else Your file: {{ $opt['sub']->file_name }} (replace)
                    @endif
                </option>
            @endforeach
        </select>
    </div>
    <div class="file-picker">
        <button type="button" class="btn-choose" onclick="this.parentNode.querySelector('input[type=file]').click()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            <span class="btn-choose-text">Choose File</span>
        </button>
        <span class="file-picker-name">No file chosen</span>
        <span class="file-chip" hidden>
            <span class="file-chip-name"></span>
            <button type="button" class="file-chip-clear" aria-label="Remove the chosen file" onclick="clearSubmissionFile(this)">×</button>
        </span>
        <input type="file" name="file" accept="{{ $accept ?? '.pdf,.doc,.docx' }}" required onchange="pickSubmissionFile(this)">
    </div>
    <button type="submit" class="btn-submit" hidden>Submit</button>
    <div class="file-picker-error" role="alert" hidden></div>
</form>
@endif

@include('faculty.partials.submission-upload-assets')
