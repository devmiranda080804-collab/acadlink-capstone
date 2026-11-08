{{-- "Share to" options: a whole program, or one of its courses (value "program:BSA" / "course:12").
     Needs $targetPrograms (codes) and $targetCourses (Course models in those programs). --}}
@foreach($targetPrograms as $code)
    @if(count($targetPrograms) > 1)<optgroup label="{{ $code }}" data-program="{{ $code }}">@endif
    <option value="program:{{ $code }}" data-program="{{ $code }}">Whole program ({{ $code }})</option>
    @foreach($targetCourses->where('program', $code) as $course)
        <option value="course:{{ $course->id }}" data-program="{{ $code }}">{{ $course->code }} — {{ $course->title }}</option>
    @endforeach
    @if(count($targetPrograms) > 1)</optgroup>@endif
@endforeach
