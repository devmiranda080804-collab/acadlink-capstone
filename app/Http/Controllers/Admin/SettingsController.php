<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\CmsOption;
use App\Support\CmsLists;
use App\Models\Setting;
use App\Support\AcademicTerm;
use App\Support\Signatories;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings', [
            'deanName'  => Setting::get(Signatories::DEAN_NAME),
            'deanTitle' => Setting::get(Signatories::DEAN_TITLE),
            'term'      => [
                'manual'      => AcademicTerm::isManual(),
                'school_year' => Setting::get(AcademicTerm::SETTING_SCHOOL_YEAR) ?? AcademicTerm::currentSchoolYear(),
                'semester'    => Setting::get(AcademicTerm::SETTING_SEMESTER) ?? AcademicTerm::currentSemester(),
                'current'     => AcademicTerm::currentSemester() . ', A.Y. ' . AcademicTerm::currentSchoolYear(),
                'calendar'    => AcademicTerm::calendarSemester() . ', A.Y. ' . AcademicTerm::calendarSchoolYear(),
            ],
            // The academic years offered across the system (every A.Y. dropdown reads this list).
            'schoolYears'     => CmsOption::where('list', CmsLists::SCHOOL_YEARS)->orderBy('sort_order')->orderBy('id')->get(),
            'schoolYearUsage' => CmsLists::usage(CmsLists::SCHOOL_YEARS),
        ]);
    }

    // Settings → Academic Term → Add academic year. It shows up right away in every
    // academic-year dropdown of the system (term filters, assignments, requirements...).
    public function addSchoolYear(Request $request)
    {
        $request->merge(['school_year' => preg_replace('/\s*[–—-]\s*/u', '-', trim((string) $request->school_year))]);
        $request->validate([
            'school_year' => ['required', function ($attribute, $value, $fail) {
                if (!AcademicTerm::isValidSchoolYear($value)) {
                    $fail('Write the academic year as two years in a row, for example 2027-2028.');
                }
            }, Rule::unique('cms_options', 'key')->where('list', CmsLists::SCHOOL_YEARS)],
        ], ['school_year.unique' => 'That academic year is already on the list.'], ['school_year' => 'Academic year']);

        CmsOption::create([
            'list'       => CmsLists::SCHOOL_YEARS,
            'key'        => $request->school_year,
            'label'      => $request->school_year,
            'sort_order' => (int) CmsOption::where('list', CmsLists::SCHOOL_YEARS)->max('sort_order') + 1,
        ]);
        CmsLists::sortSchoolYears();
        AuditLog::record('Academic Year Added', "A.Y. {$request->school_year} added by " . auth()->user()->name);

        return back()->with('success', "A.Y. {$request->school_year} added. It can now be picked everywhere in the system.");
    }

    // Hide an academic year from new choices (records that use it keep it), or show it again.
    public function toggleSchoolYear(Request $request, CmsOption $option)
    {
        abort_unless($option->list === CmsLists::SCHOOL_YEARS, 404);
        $request->validate(['is_active' => 'required|boolean']);
        $show = $request->boolean('is_active');

        if (!$show && $option->is_active) {
            if ($option->key === AcademicTerm::currentSchoolYear()) {
                return back()->withErrors(['school_year' => "A.Y. {$option->key} is the current term, so it can't be hidden. Set another term first."]);
            }
            if (CmsOption::where('list', CmsLists::SCHOOL_YEARS)->where('is_active', true)->count() <= 1) {
                return back()->withErrors(['school_year' => 'At least one academic year has to stay shown.']);
            }
        }

        $option->update(['is_active' => $show]);
        CmsLists::flush();
        AuditLog::record('Academic Year Updated', "A.Y. {$option->key} " . ($show ? 'shown again' : 'hidden') . ' by ' . auth()->user()->name);

        return back()->with('success', "A.Y. {$option->key} " . ($show ? 'is shown again.' : 'is hidden from new choices.'));
    }

    // Each card on the page saves on its own.
    public function update(Request $request)
    {
        if ($request->input('section') === 'term') {
            $request->validate([
                'term_mode'        => ['required', Rule::in([AcademicTerm::MODE_AUTO, AcademicTerm::MODE_MANUAL])],
                'term_school_year' => ['required_if:term_mode,' . AcademicTerm::MODE_MANUAL, 'nullable', Rule::in(AcademicTerm::settingsSchoolYearOptions())],
                'term_semester'    => ['required_if:term_mode,' . AcademicTerm::MODE_MANUAL, 'nullable', Rule::in(AcademicTerm::SEMESTERS)],
            ], [
                'term_school_year.required_if' => 'Choose the academic year.',
                'term_semester.required_if'    => 'Choose the semester.',
            ], ['term_school_year' => 'Academic year', 'term_semester' => 'Semester']);

            $before = AcademicTerm::currentSemester() . ', A.Y. ' . AcademicTerm::currentSchoolYear();

            Setting::put(AcademicTerm::SETTING_MODE, $request->term_mode);
            if ($request->term_mode === AcademicTerm::MODE_MANUAL) {
                Setting::put(AcademicTerm::SETTING_SCHOOL_YEAR, $request->term_school_year);
                Setting::put(AcademicTerm::SETTING_SEMESTER, $request->term_semester);
            }
            AcademicTerm::flush();

            $after = AcademicTerm::currentSemester() . ', A.Y. ' . AcademicTerm::currentSchoolYear();
            AuditLog::record('Settings Updated', "Academic term changed from {$before} to {$after} by " . auth()->user()->name);

            return back()->with('success', "Academic term saved. The system now uses {$after}.");
        }

        $request->validate([
            'dean_name'  => 'nullable|string|max:150',
            'dean_title' => 'nullable|string|max:150',
        ], [], ['dean_name' => "Dean's full name", 'dean_title' => 'Title']);

        Setting::put(Signatories::DEAN_NAME, trim((string) $request->dean_name) ?: null);
        Setting::put(Signatories::DEAN_TITLE, trim((string) $request->dean_title) ?: null);

        AuditLog::record('Settings Updated', 'Document signatories updated by ' . auth()->user()->name);

        return back()->with('success', 'Settings saved.');
    }
}
