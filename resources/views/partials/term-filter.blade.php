{{--
    A.Y. and semester filter of a document page. Both start at "All", so documents from an
    earlier term that are still in use stay in sight; pick a term to narrow the page.
    Usage: @include('partials.term-filter', ['term' => [$schoolYear, $semester], 'count' => $templates->count()])
--}}
@php
    [$termYear, $termSemester] = $term;
    $keep = collect(request()->query())->except(['school_year', 'semester']);
@endphp
<style>
    .term-filter { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; background: #fff; border: 1px solid #e4e4e4; border-radius: 8px; padding: 9px 12px; margin-bottom: 16px; }
    .term-filter label { font-size: 12px; font-weight: 700; color: #444; }
    .term-filter select { padding: 6px 28px 6px 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 12.5px; background: #fff; font-family: Arial, sans-serif; cursor: pointer; }
    .term-filter .term-now { font-size: 11.5px; color: #777; margin-left: auto; }
    .term-filter .term-clear { font-size: 12px; color: #1d4ed8; text-decoration: none; font-weight: 600; }
    .term-filter .term-clear:hover { text-decoration: underline; }
    .term-tag { display: inline-block; background: #eef2ff; color: #3730a3; font-size: 10.5px; font-weight: 700; padding: 1px 7px; border-radius: 9px; white-space: nowrap; }
</style>
<form method="GET" class="term-filter" role="search" aria-label="Filter by academic year and semester">
    @foreach($keep as $name => $value)
        @if(is_scalar($value))<input type="hidden" name="{{ $name }}" value="{{ $value }}">@endif
    @endforeach
    <label for="term-filter-year">A.Y.</label>
    <select id="term-filter-year" name="school_year" onchange="this.form.submit()">
        <option value="">All academic years</option>
        @foreach(\App\Support\AcademicTerm::documentYears() as $year)
            <option value="{{ $year }}" @selected($termYear === $year)>{{ $year }}</option>
        @endforeach
    </select>
    <label for="term-filter-semester">Semester</label>
    <select id="term-filter-semester" name="semester" onchange="this.form.submit()">
        <option value="">All semesters</option>
        @foreach(\App\Support\AcademicTerm::semesters() as $semester)
            <option value="{{ $semester }}" @selected($termSemester === $semester)>{{ $semester }}</option>
        @endforeach
    </select>
    @if($termYear || $termSemester)
        <a class="term-clear" href="{{ url()->current() . ($keep->isNotEmpty() ? '?' . http_build_query($keep->all()) : '') }}">Show all</a>
    @else
        <a class="term-clear" href="{{ url()->current() . '?' . http_build_query($keep->merge(['school_year' => \App\Support\AcademicTerm::currentSchoolYear(), 'semester' => \App\Support\AcademicTerm::currentSemester()])->all()) }}">This term only</a>
    @endif
    <span class="term-now">
        @isset($count){{ $count }} {{ $count === 1 ? 'document' : 'documents' }} · @endisset
        Current term: {{ \App\Support\AcademicTerm::shortLabel(\App\Support\AcademicTerm::currentSemester(), \App\Support\AcademicTerm::currentSchoolYear()) }}
    </span>
</form>
