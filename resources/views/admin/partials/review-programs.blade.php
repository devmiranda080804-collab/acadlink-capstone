{{-- Program checkboxes for Approve & Send / Send to More Programs. $locked: already sent. --}}
<div class="program-list">
    <label class="all"><input type="checkbox" onchange="toggleAllReviewPrograms(this)"> All Programs</label>
    @foreach(\App\Support\Programs::options() as $code => $label)
        <label title="{{ $label }}">
            <input type="checkbox" name="programs[]" value="{{ $code }}" @checked(in_array($code, $checked, true)) @disabled(in_array($code, $locked, true))>
            {{ $code }}@if(in_array($code, $locked, true)) <span style="color:#888;font-weight:400;">(already sent)</span>@endif
        </label>
    @endforeach
</div>
