<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Settings – CBMA System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background-color: #f0f0f0; display: flex; height: 100vh; overflow: hidden; }
        .sidebar { width: 210px; background-color: #0f2557; display: flex; flex-direction: column; flex-shrink: 0; overflow-y: auto; }
        .sidebar-logo { display: flex; flex-direction: column; align-items: center; padding: 22px 16px 16px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-logo img { width: 72px; height: 72px; border-radius: 50%; object-fit: contain; background: #1b3d7a; }
        .sidebar-logo .brand { color: #fff; font-size: 15px; font-weight: 700; margin-top: 8px; }
        .sidebar-logo .brand-sub { color: #a0b4d6; font-size: 10px; margin-top: 2px; }
        .nav-list { list-style: none; padding: 10px 0; flex: 1; }
        .nav-list li a { display: flex; align-items: center; gap: 11px; padding: 11px 20px; color: #c8d6ec; text-decoration: none; font-size: 13px; transition: background 0.15s, color 0.15s; }
        .nav-list li a:hover { background-color: rgba(255,255,255,0.08); color: #fff; }
        .nav-list li.active a { background-color: rgba(255,255,255,0.08); color: #fff; border-left: 3px solid #fff; }
        .nav-list li a svg { width: 18px; height: 18px; flex-shrink: 0; opacity: 0.85; }
        .nav-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 16px; height: 16px; padding: 0 4px; margin-left: auto; background: #ef4444; color: #fff; font-size: 10px; font-weight: 700; border-radius: 999px; }
        .sidebar-logout { padding: 12px 0; border-top: 1px solid rgba(255,255,255,0.1); }
        .sidebar-logout a { display: flex; align-items: center; gap: 11px; padding: 11px 20px; color: #c8d6ec; text-decoration: none; font-size: 13px; transition: background 0.15s; }
        .sidebar-logout a:hover { background-color: rgba(255,255,255,0.08); color: #fff; }

        .main { flex: 1; display: flex; flex-direction: column; overflow: hidden; background-color: #f5f6fa; }
        .topnav { background: #fff; border-bottom: 1px solid #e0e0e0; padding: 0 24px; height: 52px; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
        .topnav .label { font-size: 12px; color: #666; }
        .topnav-right { display: flex; align-items: center; gap: 12px; }
        .role-badge { background-color: #0f2557; color: #fff; font-size: 12px; font-weight: 600; padding: 5px 16px; border-radius: 20px; }
        .user-info { display: flex; align-items: center; gap: 8px; }
        .user-text { text-align: right; }
        .user-name { font-size: 12px; font-weight: 600; color: #222; }
        .user-email { font-size: 10px; color: #888; }
        .user-avatar { width: 34px; height: 34px; border-radius: 50%; background-color: #0f2557; color: #fff; font-size: 12px; font-weight: 700; display: flex; align-items: center; justify-content: center; }

        .content { flex: 1; overflow-y: auto; padding: 24px 28px 28px; }
        .alert-success { background: #dcfce7; color: #166534; padding: 12px 16px; margin-bottom: 16px; border: 1px solid #bbf7d0; border-radius: 8px; font-size: 13px; }
        .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 24px; }
        .page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; }
        .page-sub { font-size: 11.5px; color: #888; margin-top: 3px; }
        .alert-error { background: #fee2e2; color: #b91c1c; padding: 12px 16px; margin-bottom: 16px; border: 1px solid #fca5a5; border-radius: 8px; font-size: 13px; }
        .settings-card { background: #fff; border: 1px solid #e4e4e4; border-radius: 10px; padding: 22px 24px; max-width: 680px; margin-bottom: 18px; }
        .settings-card-title { font-size: 14px; font-weight: 700; color: #1a1a2e; margin-bottom: 4px; }
        .settings-card-sub { font-size: 12px; color: #777; margin-bottom: 18px; line-height: 1.5; }
        .settings-field { margin-bottom: 14px; }
        .settings-field label { display: block; font-size: 12px; font-weight: 700; color: #333; margin-bottom: 5px; }
        .settings-field input { width: 100%; padding: 9px 11px; border: 1px solid #ccc; border-radius: 6px; font-size: 13px; font-family: Arial, sans-serif; outline: none; }
        .settings-field input:focus { border-color: #0f2557; }
        .settings-field select { width: 100%; padding: 9px 11px; border: 1px solid #ccc; border-radius: 6px; font-size: 13px; font-family: Arial, sans-serif; background: #fff; outline: none; }
        .settings-field select:focus { border-color: #0f2557; }
        .settings-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
        @media (max-width: 640px) { .settings-row { grid-template-columns: 1fr; } }
        .term-modes { display: flex; flex-direction: column; gap: 8px; margin-bottom: 14px; }
        .term-mode { display: flex; align-items: flex-start; gap: 8px; font-size: 12.5px; color: #333; cursor: pointer; }
        .term-mode input { margin-top: 2px; accent-color: #0f2557; }
        .term-fields { grid-template-columns: repeat(2, 1fr); }
        .term-fields.is-disabled { opacity: .5; }
        .settings-hint { font-size: 11px; color: #999; margin-top: 4px; }
        .preview { background: #f7f8fb; border: 1px dashed #cfd5e3; border-radius: 8px; padding: 14px 16px; margin-bottom: 16px; font-size: 12.5px; color: #333; }
        .preview-label { font-size: 10.5px; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: .4px; margin-bottom: 8px; }
        .preview-name { font-weight: 700; text-decoration: underline; }
        .preview-title { font-style: italic; color: #555; font-size: 11.5px; }
        .btn-save { background: #0f2557; color: #fff; border: none; font-size: 13px; font-weight: 600; padding: 9px 22px; border-radius: 6px; cursor: pointer; }
        .btn-save:hover { background: #1a3a7a; }
        .settings-field textarea { width: 100%; padding: 9px 11px; border: 1px solid #ccc; border-radius: 6px; font-size: 13px; font-family: Arial, sans-serif; outline: none; resize: vertical; }
        .settings-field textarea:focus { border-color: #0f2557; }
    </style>
</head>
<body>

    @include('partials.sidebar')

    <div class="main">
        <div class="topnav">
            <div class="topnav-right" style="margin-left:auto;">
                @include('partials.topnav-user')
            </div>
        </div>

        <div class="content">
            @php
                $deanSet = filled($deanName);
                $termErrors = $errors->hasAny(['term_mode', 'term_school_year', 'term_semester', 'school_year']);
                $deanErrors = $errors->hasAny(['dean_name', 'dean_title']);
            @endphp
            <style>
                .st { font-family: 'Plus Jakarta Sans', Arial, sans-serif; color: #14213d; }
                .st .st-icon { width: 18px; height: 18px; flex-shrink: 0; }
                .st-eyebrow { font-size: 12px; font-weight: 800; letter-spacing: 2px; color: #1b3d7a; text-transform: uppercase; }
                .st-title { font-size: 28px; font-weight: 800; letter-spacing: -.4px; margin: 4px 0 6px; }
                .st-sub { font-size: 14px; color: #475467; max-width: 860px; }
                .st-layout { display: grid; grid-template-columns: 240px 1fr; gap: 24px; margin-top: 24px; align-items: start; }
                .st-nav { display: flex; flex-direction: column; gap: 4px; position: sticky; top: 0; }
                .st-tab { display: flex; align-items: center; gap: 12px; width: 100%; padding: 12px 14px; border: 1px solid transparent; border-radius: 12px; background: none;
                    font: 700 14.5px 'Plus Jakarta Sans', Arial, sans-serif; color: #344054; cursor: pointer; text-align: left; transition: background-color .18s ease, border-color .18s ease, box-shadow .18s ease; }
                .st-tab:hover { background: #fff; border-color: #e7eaf1; }
                .st-tab.is-active { background: #fff; border-color: #e7eaf1; color: #0f2557; box-shadow: 0 4px 14px rgba(15, 37, 87, .07); }
                .st-tab .st-dot { margin-left: auto; min-width: 22px; height: 22px; padding: 0 6px; border-radius: 11px; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; }
                .st-dot.ok { background: #ecfdf3; color: #15803d; }
                .st-dot.warn { background: #fff7e6; color: #b45309; }
                .st-panel { display: none; flex-direction: column; gap: 16px; animation: st-in .25s ease-out; }
                .sy-add { display: flex; align-items: flex-end; gap: 10px; flex-wrap: wrap; margin-top: 14px; }
                .sy-add .st-field { flex: 0 1 260px; margin: 0; }
                .sy-add .btn-save { margin: 0; }
                .st-error { margin-top: 8px; font-size: 12.5px; color: #b91c1c; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 8px 10px; }
                .sy-summary { display: flex; gap: 8px; margin: 18px 0 8px; }
                .sy-summary span { font-size: 11.5px; font-weight: 700; padding: 3px 10px; border-radius: 999px; background: #eef2ff; color: #3730a3; }
                .sy-summary span.off { background: #f1f2f5; color: #667085; }
                .sy-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 10px; }
                .sy-row { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 12px 14px; border: 1px solid #eef0f5; border-radius: 12px; background: #fafbfd; }
                .sy-row.is-hidden { opacity: .65; }
                .sy-text { min-width: 0; }
                .sy-text strong { font-size: 14px; color: #0f2557; }
                .sy-text small { display: block; font-size: 11.5px; color: #667085; margin-top: 2px; }
                .sy-tag { display: inline-block; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 10px; margin-left: 4px; background: #f1f2f5; color: #667085; vertical-align: middle; }
                .sy-tag.now { background: #dcfce7; color: #166534; }
                .sy-btn { flex-shrink: 0; background: #fff; border: 1px solid #c9d1e2; color: #0f2557; font: 700 12px inherit; font-family: inherit; padding: 6px 12px; border-radius: 8px; cursor: pointer; }
                .sy-btn.danger { color: #b91c1c; border-color: #fca5a5; }
                .sy-btn:disabled, .sy-btn.danger:disabled { color: #a0a6b4 !important; border-color: #e4e7ee !important; background: #f5f6f9 !important; cursor: not-allowed; }
                .st-panel.is-active { display: flex; }
                @keyframes st-in { from { opacity: 0; } to { opacity: 1; } }
                .st-card { background: #fff; border: 1px solid #e7eaf1; border-radius: 16px; padding: 20px 22px; box-shadow: 0 1px 2px rgba(15, 37, 87, .04), 0 6px 18px rgba(15, 37, 87, .05); }
                .st-status { display: flex; align-items: center; gap: 16px; }
                .st-status-icon { width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
                .st-status-icon .st-icon { width: 24px; height: 24px; }
                .st-status-icon.ok { background: #ecfdf3; color: #16a34a; }
                .st-status-icon.warn { background: #fff7e6; color: #d97706; }
                .st-status-title { font-size: 17px; font-weight: 800; }
                .st-status-sub { font-size: 13px; color: #667085; margin-top: 2px; }
                .st-facts { display: flex; flex-wrap: wrap; gap: 6px 22px; margin-top: 14px; padding-top: 14px; border-top: 1px dashed #e7eaf1; font-size: 12.5px; color: #667085; }
                .st-facts strong { color: #14213d; }
                .st-card-title { display: flex; align-items: center; gap: 10px; font-size: 16px; font-weight: 800; }
                .st-card-title .st-icon { color: #0f2557; }
                .st-card-sub { font-size: 13px; color: #667085; margin: 4px 0 18px; line-height: 1.55; }
                .st-modes { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 12px; margin-bottom: 18px; }
                .st-mode { position: relative; display: flex; gap: 12px; padding: 14px 16px; border: 1.5px solid #e7eaf1; border-radius: 14px; cursor: pointer; transition: border-color .18s ease, background-color .18s ease, box-shadow .18s ease; }
                .st-mode:hover { border-color: #c9d2e4; }
                .st-mode input { margin-top: 3px; accent-color: #0f2557; width: 16px; height: 16px; flex-shrink: 0; }
                .st-mode:has(input:checked) { border-color: #0f2557; background: #f6f8fd; box-shadow: 0 0 0 3px rgba(15, 37, 87, .08); }
                .st-mode strong { display: block; font-size: 14px; }
                .st-mode span span { display: block; font-size: 12.5px; color: #667085; margin-top: 3px; }
                .st-fields { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; transition: opacity .2s ease; }
                .st-fields.is-disabled { opacity: .45; }
                .st-field { margin-bottom: 14px; }
                .st-field label { display: block; font-size: 13px; font-weight: 700; color: #344054; margin-bottom: 6px; }
                .st-field input, .st-field select { width: 100%; padding: 10px 12px; border: 1px solid #d5dbe8; border-radius: 10px; font: 500 14px 'Plus Jakarta Sans', Arial, sans-serif; background: #fff; color: #14213d; outline: none; }
                .st-field input:focus, .st-field select:focus { border-color: #1b3d7a; box-shadow: 0 0 0 3px rgba(37, 99, 235, .14); }
                .st-hint { font-size: 12px; color: #667085; margin-top: 6px; }
                .st-note { display: flex; gap: 10px; align-items: flex-start; padding: 12px 14px; border-radius: 12px; background: #f6f8fd; color: #344054; font-size: 12.5px; margin: 4px 0 18px; }
                .st-note .st-icon { color: #1b3d7a; margin-top: 1px; }
                .st-actions { display: flex; justify-content: flex-end; gap: 10px; padding-top: 16px; border-top: 1px solid #f1f3f8; }
                .st-actions .btn-save { padding: 10px 22px; font-size: 14px; }
                .st-preview { border: 1px dashed #d5dbe8; border-radius: 14px; padding: 18px 20px; background: #fbfcfe; margin-bottom: 18px; }
                .st-preview-label { font-size: 11px; font-weight: 800; letter-spacing: 1.4px; text-transform: uppercase; color: #98a2b3; margin-bottom: 12px; }
                .st-preview-sheet { font-family: 'Times New Roman', serif; color: #111; font-size: 14px; }
                .st-preview-sheet .preview-name { display: inline-block; margin-top: 26px; font-weight: 700; text-decoration: none; border-top: 1px solid #111; padding-top: 3px; min-width: 260px; }
                .st-preview-sheet .preview-title { font-style: italic; font-size: 13px; color: #333; }
                @media (max-width: 900px) { .st-layout { grid-template-columns: 1fr; } .st-nav { position: static; flex-direction: row; flex-wrap: wrap; } .st-tab { width: auto; } }
            </style>

            <div class="st">
                @if($errors->any())
                    <div class="alert-error">{{ $errors->first() }}</div>
                @endif

                <div class="st-eyebrow">Configuration</div>
                <div class="st-title">System Settings</div>
                <div class="st-sub">How the CBMA System works for everyone: the academic term it runs in and who signs the documents it produces.</div>

                <div class="st-layout">
                    <nav class="st-nav" role="tablist" aria-label="Settings sections">
                        <button type="button" class="st-tab" role="tab" data-panel="term" id="tab-term">
                            {!! \App\Support\SidebarMenu::icon('calendar', 'st-icon') !!} Academic Term
                            <span class="st-dot ok" title="{{ $term['manual'] ? 'Set manually' : 'Automatic' }}">{{ $term['manual'] ? 'Manual' : 'Auto' }}</span>
                        </button>
                        <button type="button" class="st-tab" role="tab" data-panel="signatories" id="tab-signatories">
                            {!! \App\Support\SidebarMenu::icon('fileCheck', 'st-icon') !!} Document Signatories
                            @unless($deanSet)<span class="st-dot warn" title="The Dean's name is not set yet">1</span>@endunless
                        </button>
                    </nav>

                    <div>
                        {{-- Academic Term --}}
                        <section class="st-panel" id="panel-term" role="tabpanel" aria-labelledby="tab-term">
                            <div class="st-card">
                                <div class="st-status">
                                    <span class="st-status-icon ok">{!! \App\Support\SidebarMenu::icon('checkCircle', 'st-icon') !!}</span>
                                    <div>
                                        <div class="st-status-title">Now in use: {{ $term['current'] }}</div>
                                        <div class="st-status-sub">{{ $term['manual'] ? 'Set manually by the Admin.' : 'Following the calendar automatically.' }}</div>
                                    </div>
                                </div>
                                <div class="st-facts">
                                    <span>Mode: <strong>{{ $term['manual'] ? 'Manual' : 'Automatic' }}</strong></span>
                                    <span>Calendar says: <strong>{{ $term['calendar'] }}</strong></span>
                                    <span>Affects: <strong>subjects, submissions, exams, the TOS and documents</strong></span>
                                </div>
                            </div>

                            <div class="st-card">
                                <div class="st-card-title">{!! \App\Support\SidebarMenu::icon('calendar', 'st-icon') !!} Academic Term</div>
                                <div class="st-card-sub">The semester and academic year the whole system works in: the subjects faculty see, submissions, exams, the TOS and downloaded documents.</div>

                                <form method="POST" action="{{ url('/admin/settings') }}" id="term-form">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="section" value="term">
                                    <div class="st-modes">
                                        <label class="st-mode">
                                            <input type="radio" name="term_mode" value="auto" @checked(old('term_mode', $term['manual'] ? 'manual' : 'auto') === 'auto') onchange="updateTermForm()">
                                            <span><strong>Automatic</strong><span>Follows the calendar ({{ $term['calendar'] }})</span></span>
                                        </label>
                                        <label class="st-mode">
                                            <input type="radio" name="term_mode" value="manual" @checked(old('term_mode', $term['manual'] ? 'manual' : 'auto') === 'manual') onchange="updateTermForm()">
                                            <span><strong>Set manually</strong><span>Use when a semester starts early or runs late</span></span>
                                        </label>
                                    </div>

                                    <div class="st-fields term-fields" id="term-fields">
                                        <div class="st-field">
                                            <label for="term_semester">Semester</label>
                                            <select id="term_semester" name="term_semester">
                                                @foreach(\App\Support\AcademicTerm::semesters() as $semester)
                                                    <option value="{{ $semester }}" @selected(old('term_semester', $term['semester']) === $semester)>{{ $semester }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="st-field">
                                            <label for="term_school_year">Academic year</label>
                                            <select id="term_school_year" name="term_school_year">
                                                @foreach(\App\Support\AcademicTerm::settingsSchoolYearOptions() as $year)
                                                    <option value="{{ $year }}" @selected(old('term_school_year', $term['school_year']) === $year)>A.Y. {{ $year }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="st-note">{!! \App\Support\SidebarMenu::icon('clock', 'st-icon') !!}<span>Changing the term changes what everyone sees right away. Records from other terms are kept and come back when that term is set again.</span></div>

                                    <div class="st-actions"><button type="submit" class="btn-save">Save term</button></div>
                                </form>
                            </div>

                            {{-- The academic years every A.Y. dropdown of the system offers. --}}
                            <div class="st-card" id="school-years">
                                <div class="st-card-title">{!! \App\Support\SidebarMenu::icon('calendar', 'st-icon') !!} Academic Years</div>
                                <div class="st-card-sub">The academic years people can pick everywhere in the system: the term filters, Program Assignment, submission requirements and documents. A year added here shows up in all of them right away.</div>

                                <form method="POST" action="{{ url('/admin/settings/school-years') }}" class="sy-add">
                                    @csrf
                                    <div class="st-field">
                                        <label for="new_school_year">Add academic year</label>
                                        @php $syLast = $schoolYears->pluck('key')->sort()->last() ?? \App\Support\AcademicTerm::currentSchoolYear(); $syNext = (int) explode('-', $syLast)[0] + 1; @endphp
                                        <input type="text" id="new_school_year" name="school_year" maxlength="9" inputmode="numeric" placeholder="e.g. {{ $syNext }}-{{ $syNext + 1 }}" value="{{ old('school_year') }}" required>
                                    </div>
                                    <button type="submit" class="btn-save">Add</button>
                                </form>
                                @error('school_year')<div class="st-error" role="alert">{{ $message }}</div>@enderror

                                <div class="sy-summary">
                                    <span>{{ $schoolYears->where('is_active', true)->count() }} shown</span>
                                    <span class="off">{{ $schoolYears->where('is_active', false)->count() }} hidden</span>
                                </div>
                                <div class="sy-list">
                                    @foreach($schoolYears as $year)
                                        @php $isCurrent = $year->key === \App\Support\AcademicTerm::currentSchoolYear(); $used = $schoolYearUsage[$year->key] ?? 0; @endphp
                                        <div class="sy-row {{ $year->is_active ? '' : 'is-hidden' }}">
                                            <div class="sy-text">
                                                <strong>A.Y. {{ $year->key }}</strong>
                                                @if($isCurrent)<span class="sy-tag now">Current term</span>@endif
                                                @unless($year->is_active)<span class="sy-tag">Hidden</span>@endunless
                                                <small>Used by {{ $used }} {{ $used === 1 ? 'record' : 'records' }}</small>
                                            </div>
                                            <form method="POST" action="{{ url('/admin/settings/school-years/' . $year->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="is_active" value="{{ $year->is_active ? 0 : 1 }}">
                                                <button type="submit" class="sy-btn {{ $year->is_active ? 'danger' : '' }}" @disabled($year->is_active && $isCurrent) title="{{ $year->is_active && $isCurrent ? 'This is the current term. Set another term first.' : ($year->is_active ? 'Hide from new choices; records that use it keep it' : 'Offer it again in every dropdown') }}">{{ $year->is_active ? 'Hide' : 'Show' }}</button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="st-note">{!! \App\Support\SidebarMenu::icon('clock', 'st-icon') !!}<span>Hiding a year takes it out of new choices only. Records that already use it keep it, and nothing is deleted.</span></div>
                            </div>
                        </section>

                        {{-- Document Signatories --}}
                        <section class="st-panel" id="panel-signatories" role="tabpanel" aria-labelledby="tab-signatories">
                            <div class="st-card">
                                <div class="st-status">
                                    <span class="st-status-icon {{ $deanSet ? 'ok' : 'warn' }}">{!! \App\Support\SidebarMenu::icon($deanSet ? 'checkCircle' : 'fileCheck', 'st-icon') !!}</span>
                                    <div>
                                        <div class="st-status-title">{{ $deanSet ? 'Ready — documents are signed by the Dean.' : 'The Dean\'s name is not set yet.' }}</div>
                                        <div class="st-status-sub">{{ $deanSet ? 'The TOS and other documents print this name in the "Approved" signature.' : 'Documents print a blank line for signing until it is set.' }}</div>
                                    </div>
                                </div>
                                <div class="st-facts">
                                    <span>Dean: <strong>{{ $deanSet ? mb_strtoupper($deanName) : 'Not set' }}</strong></span>
                                    <span>Title: <strong>{{ $deanTitle ?: \App\Support\Signatories::DEFAULT_DEAN_TITLE }}</strong></span>
                                    <span>Program Heads: <strong>taken from their accounts</strong></span>
                                </div>
                            </div>

                            <div class="st-card">
                                <div class="st-card-title">{!! \App\Support\SidebarMenu::icon('fileCheck', 'st-icon') !!} Document Signatories</div>
                                <div class="st-card-sub">The Dean's name and title printed in the "Approved" signature of downloaded documents such as the Table of Specification. The Program Head's name is taken automatically from their account for each program.</div>

                                <form method="POST" action="{{ url('/admin/settings') }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="st-field">
                                        <label for="dean_name">Dean's full name</label>
                                        <input type="text" id="dean_name" name="dean_name" maxlength="150" value="{{ old('dean_name', $deanName) }}" placeholder="e.g. Pelilia C. Veloso, CPA, DBA, LLB" oninput="updateDeanPreview()">
                                        <div class="st-hint">Include credentials as they should appear. Leave blank to print a blank line for signing.</div>
                                    </div>
                                    <div class="st-field">
                                        <label for="dean_title">Title</label>
                                        <input type="text" id="dean_title" name="dean_title" maxlength="150" value="{{ old('dean_title', $deanTitle) }}" placeholder="{{ \App\Support\Signatories::DEFAULT_DEAN_TITLE }}" oninput="updateDeanPreview()">
                                    </div>

                                    <div class="st-preview">
                                        <div class="st-preview-label">Preview</div>
                                        <div class="st-preview-sheet">
                                            <div>Approved:</div>
                                            <div class="preview-name" id="preview-dean-name"></div>
                                            <div class="preview-title" id="preview-dean-title"></div>
                                        </div>
                                    </div>

                                    <div class="st-actions"><button type="submit" class="btn-save">Save signatories</button></div>
                                </form>
                            </div>
                        </section>
                    </div>
                </div>
            </div>

            <script>
                // Shows one section at a time; remembers the last one, and opens the one with an error.
                (function () {
                    var tabs = document.querySelectorAll('.st-tab');
                    function show(name) {
                        tabs.forEach(function (t) {
                            var on = t.dataset.panel === name;
                            t.classList.toggle('is-active', on);
                            t.setAttribute('aria-selected', on ? 'true' : 'false');
                        });
                        document.querySelectorAll('.st-panel').forEach(function (p) { p.classList.toggle('is-active', p.id === 'panel-' + name); });
                        try { localStorage.setItem('settings-tab', name); } catch (e) {}
                    }
                    tabs.forEach(function (t) { t.addEventListener('click', function () { show(t.dataset.panel); }); });
                    var start = @json($deanErrors ? 'signatories' : ($termErrors ? 'term' : null));
                    if (!start) { try { start = localStorage.getItem('settings-tab'); } catch (e) {} }
                    show(start === 'signatories' ? 'signatories' : 'term');
                })();
            </script>
        </div>
    </div>

    <script>
        function updateDeanPreview() {
            var name = document.getElementById('dean_name').value.trim();
            var title = document.getElementById('dean_title').value.trim();
            document.getElementById('preview-dean-name').textContent = name ? name.toUpperCase() : '___________________________';
            document.getElementById('preview-dean-title').textContent = title || @json(\App\Support\Signatories::DEFAULT_DEAN_TITLE);
        }
        updateDeanPreview();

        // The semester/year pickers only apply when the term is set manually.
        function updateTermForm() {
            var manual = document.querySelector('input[name="term_mode"][value="manual"]').checked;
            document.getElementById('term-fields').classList.toggle('is-disabled', !manual);
            document.getElementById('term_semester').disabled = !manual;
            document.getElementById('term_school_year').disabled = !manual;
        }
        updateTermForm();
    </script>

    <div id="logout-confirm-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:9999;align-items:center;justify-content:center;">
        <div style="background:#fff;border-radius:10px;padding:24px 26px;width:360px;max-width:92vw;box-shadow:0 8px 32px rgba(0,0,0,0.25);font-family:Arial,sans-serif;">
            <div style="font-size:15px;font-weight:700;color:#1a1a2e;margin-bottom:8px;">Log out?</div>
            <div style="font-size:13px;color:#555;margin-bottom:20px;">Are you sure you want to log out of your account?</div>
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <button type="button" onclick="document.getElementById('logout-confirm-overlay').style.display='none';" style="background:#fff;border:1px solid #ccc;color:#444;font-size:12.5px;font-weight:600;padding:8px 18px;border-radius:5px;cursor:pointer;font-family:Arial,sans-serif;">Cancel</button>
            <button type="button" onclick="document.getElementById('logout-form').submit();" style="background:#ef4444;color:#fff;border:none;font-size:12.5px;font-weight:600;padding:8px 18px;border-radius:5px;cursor:pointer;font-family:Arial,sans-serif;">Log Out</button>
            </div>
        </div>
    </div>
    <script>
        function openLogoutModal() { document.getElementById('logout-confirm-overlay').style.display = 'flex'; }
        document.getElementById('logout-confirm-overlay').addEventListener('click', function(e) { if (e.target === this) this.style.display = 'none'; });
    </script>

    @include('partials.system-feedback')
</body>
</html>
