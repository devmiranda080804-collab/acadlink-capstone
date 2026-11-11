{{-- Faculty checkboxes for Assign Faculty: tick several at once. $facultyList; $showPrograms
     adds each person's programs (the Admin assigns across programs). --}}
<style>
    .af-search { width: 100%; height: 32px; padding: 0 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 12.5px; margin-bottom: 8px; outline: none; font-family: Arial, sans-serif; }
    .af-search:focus { border-color: #0f2557; }
    .af-list { border: 1px solid #ddd; border-radius: 6px; max-height: 260px; overflow-y: auto; padding: 4px 0; }
    .af-row { display: flex; align-items: center; gap: 9px; padding: 7px 12px; font-size: 12.5px; color: #333; cursor: pointer; }
    .af-row:hover { background: #f7f8fb; }
    .af-row input { width: 15px; height: 15px; margin: 0; flex-shrink: 0; accent-color: #0f2557; cursor: pointer; }
    .af-row.is-assigned { color: #888; cursor: default; }
    .af-row .af-meta { font-size: 10.5px; color: #888; margin-left: auto; white-space: nowrap; }
    .af-row.is-assigned .af-meta { color: #047857; font-weight: 700; }
    .af-empty { display: none; padding: 16px; text-align: center; font-size: 12px; color: #999; }
    .af-count { font-size: 11px; color: #666; margin-top: 6px; }
</style>
<input type="search" class="af-search" id="af-search" placeholder="Search faculty..." oninput="filterAssignFaculty(this.value)" aria-label="Search faculty">
<div class="af-list" id="af-list">
    @foreach($facultyList as $f)
        <label class="af-row" data-name="{{ strtolower($f->name) }}" data-programs="{{ implode(' ', $f->programCodes()) }}">
            <input type="checkbox" name="faculty_ids[]" value="{{ $f->id }}" onchange="updateAssignCount()">
            <span>{{ $f->name }}</span>
            <span class="af-meta">@if($showPrograms ?? false){{ $f->programs_label }}@endif</span>
        </label>
    @endforeach
    <div class="af-empty" id="af-empty">No faculty to show.</div>
</div>
<div class="af-count" id="af-count">0 selected</div>
<script>
    // Shows the faculty who can teach the course; those already assigned are ticked and locked.
    function resetAssignFaculty(assignedIds, courseProgram) {
        var assigned = (assignedIds || []).map(String);
        document.getElementById('af-search').value = '';
        document.querySelectorAll('#af-list .af-row').forEach(function (row) {
            var box = row.querySelector('input');
            var fits = !courseProgram || (row.dataset.programs || '').split(' ').indexOf(courseProgram) !== -1;
            var already = assigned.indexOf(box.value) !== -1;
            row.dataset.fits = fits ? '1' : '0';
            row.style.display = fits ? '' : 'none';
            box.disabled = !fits || already;
            box.checked = already;
            row.classList.toggle('is-assigned', already);
            if (already) row.querySelector('.af-meta').textContent = 'Already assigned';
        });
        updateAssignCount();
        filterAssignFaculty('');
    }
    function filterAssignFaculty(text) {
        var q = text.trim().toLowerCase(), shown = 0;
        document.querySelectorAll('#af-list .af-row').forEach(function (row) {
            var visible = row.dataset.fits === '1' && (!q || row.dataset.name.indexOf(q) !== -1);
            row.style.display = visible ? '' : 'none';
            if (visible) shown++;
        });
        document.getElementById('af-empty').style.display = shown ? 'none' : 'block';
    }
    function updateAssignCount() {
        var n = document.querySelectorAll('#af-list input:checked:not(:disabled)').length;
        document.getElementById('af-count').textContent = n + ' selected';
        var submit = document.querySelector('#assign-overlay button[type=submit]');
        if (submit) submit.disabled = n === 0;
    }
</script>
