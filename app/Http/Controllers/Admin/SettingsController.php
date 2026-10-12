<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Support\ExamPrintOptions;
use App\Support\Signatories;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings', [
            'deanName'  => Setting::get(Signatories::DEAN_NAME),
            'deanTitle' => Setting::get(Signatories::DEAN_TITLE),
            'examPrint' => ExamPrintOptions::collegeDefaults(),
        ]);
    }

    // Each card on the page saves on its own.
    public function update(Request $request)
    {
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
