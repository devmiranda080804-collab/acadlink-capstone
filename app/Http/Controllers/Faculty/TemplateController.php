<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TemplateController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'syllabus');

        $templates = Template::where('faculty_id', auth()->id())
            ->where('type', $type)
            ->latest()
            ->get();

        return view('faculty.my-template', compact('templates', 'type'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type'  => 'required|in:syllabus,lesson_plan,course_guide,module',
            'file'  => 'required|file|mimes:pdf,doc,docx|max:20480', // 20MB
        ]);

        $file = $request->file('file');
        $path = $file->store('templates', 'public');

        Template::create([
            'faculty_id' => auth()->id(),
            'program'    => auth()->user()->program,
            'title'      => $request->title,
            'type'       => $request->type,
            'file_path'  => $path,
            'file_name'  => $file->getClientOriginalName(),
            'file_type'  => $file->getClientOriginalExtension(),
            'file_size'  => $file->getSize(),
            'status'     => 'pending_review',
        ]);

        return back()->with('success', 'Template uploaded and submitted for review.');
    }

    public function update(Request $request, Template $template)
    {
        // Faculty pwedeng mag-re-upload lang ng sariling template
        abort_unless($template->faculty_id === auth()->id(), 403);

        $request->validate([
            'title' => 'required|string|max:255',
            'file'  => 'nullable|file|mimes:pdf,doc,docx|max:20480',
        ]);

        $data = ['title' => $request->title];

        // Kung may bagong file, palitan
        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($template->file_path);
            $file = $request->file('file');
            $data['file_path'] = $file->store('templates', 'public');
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_type'] = $file->getClientOriginalExtension();
            $data['file_size'] = $file->getSize();
        }

        // Kapag nag-edit pagkatapos ma-reject/needs revision, balik sa review
        if (in_array($template->status, ['needs_revision', 'rejected'])) {
            $data['status'] = 'pending_review';
            $data['review_note'] = null;
        }

        $template->update($data);

        return back()->with('success', 'Template updated and resubmitted for review.');
    }

    public function destroy(Template $template)
    {
        abort_unless($template->faculty_id === auth()->id(), 403);

        Storage::disk('public')->delete($template->file_path);
        $template->delete();

        return back()->with('success', 'Template deleted.');
    }
}