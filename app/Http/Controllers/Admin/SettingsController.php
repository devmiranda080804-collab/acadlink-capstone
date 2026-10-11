<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Support\Signatories;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings', [
            'deanName'  => Setting::get(Signatories::DEAN_NAME),
            'deanTitle' => Setting::get(Signatories::DEAN_TITLE),
        ]);
    }

    public function update(Request $request)
    {
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
