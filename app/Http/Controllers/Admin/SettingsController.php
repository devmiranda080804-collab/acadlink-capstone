<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Support\AcademicTerm;
use App\Support\ExamPrintOptions;
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
            'examPrint' => ExamPrintOptions::collegeDefaults(),
            'term'      => [
                'manual'      => AcademicTerm::isManual(),
                'school_year' => Setting::get(AcademicTerm::SETTING_SCHOOL_YEAR) ?? AcademicTerm::currentSchoolYear(),
                'semester'    => Setting::get(AcademicTerm::SETTING_SEMESTER) ?? AcademicTerm::currentSemester(),
                'current'     => AcademicTerm::currentSemester() . ', A.Y. ' . AcademicTerm::currentSchoolYear(),
                'calendar'    => AcademicTerm::calendarSemester() . ', A.Y. ' . AcademicTerm::calendarSchoolYear(),
            ],
        ]);
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

        if ($request->input('section') === 'exam_print') {
            $request->validate(
                ExamPrintOptions::rules('exam_font', 'exam_font_size', 'exam_paper', true),
                [],
                ['exam_font' => 'Font style', 'exam_font_size' => 'Font size', 'exam_paper' => 'Paper size']
            );

            Setting::put(ExamPrintOptions::SETTING_FONT, $request->exam_font);
            Setting::put(ExamPrintOptions::SETTING_SIZE, (string) $request->exam_font_size);
            Setting::put(ExamPrintOptions::SETTING_PAPER, $request->exam_paper);

            AuditLog::record('Settings Updated', 'Exam printing defaults updated by ' . auth()->user()->name);

            return back()->with('success', 'Exam printing defaults saved.');
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
