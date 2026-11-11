{{-- For a Program Head with several programs: which one the pages are showing. --}}
@auth
@if(auth()->user()->role === 'program_head' && count(auth()->user()->programCodes()) > 1)
<style>
    .ps-form { display: flex; align-items: center; gap: 6px; margin-right: 6px; font-family: Arial, sans-serif; }
    .ps-form label { font-size: 11.5px; font-weight: 700; color: #555; }
    .ps-form select { height: 30px; padding: 0 26px 0 10px; border: 1px solid #c7d2fe; border-radius: 15px; background: #eef2ff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%230f2557' d='M5 7L0 2h10z'/%3E%3C/svg%3E") no-repeat right 10px center; appearance: none; -webkit-appearance: none; color: #0f2557; font-size: 12px; font-weight: 700; cursor: pointer; }
</style>
<form class="ps-form" method="POST" action="{{ url('/program-head/active-program') }}">
    @csrf
    <label for="ps-program">Program</label>
    <select id="ps-program" name="program" onchange="this.form.submit()" title="Switch the program these pages show">
        @foreach(auth()->user()->programCodes() as $code)
            <option value="{{ $code }}" @selected($code === auth()->user()->program)>{{ $code }}</option>
        @endforeach
    </select>
</form>
@endif
@endauth
