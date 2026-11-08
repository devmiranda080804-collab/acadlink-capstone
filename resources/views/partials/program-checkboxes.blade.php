{{-- Pick one or more programs for a faculty account (name="programs[]"). --}}
<style>
    .program-checks { display: grid; grid-template-columns: 1fr 1fr; gap: 6px 12px; padding: 10px 12px; border: 1px solid #ccc; border-radius: 5px; background: #fff; }
    .program-checks label { display: flex; align-items: center; gap: 7px; font-size: 12px; font-weight: 400; color: #333; margin: 0; cursor: pointer; }
    .program-checks input[type=checkbox] { width: 14px; height: 14px; margin: 0; padding: 0; flex-shrink: 0; accent-color: #0f2557; }
    .program-checks-hint { font-size: 10.5px; color: #888; margin-top: 4px; }
    @media (max-width: 560px) { .program-checks { grid-template-columns: 1fr; } }
</style>
<div class="program-checks" id="{{ $id ?? 'f-programs' }}">
    @foreach(\App\Support\Programs::options() as $code => $label)
        <label title="{{ $label }}"><input type="checkbox" name="programs[]" value="{{ $code }}"> {{ $code }}</label>
    @endforeach
</div>
<div class="program-checks-hint">Choose every program this faculty member teaches in.</div>
