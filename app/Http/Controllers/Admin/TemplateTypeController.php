<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\TemplateType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

// Settings → Template Types: the Admin adds, renames, hides and removes kinds of template.
class TemplateTypeController extends Controller
{
    public function store(Request $request)
    {
        $data = $this->validated($request);
        $key = TemplateType::keyFor($data['type_label']);

        if ($key === '') {
            return back()->withErrors(['type_label' => 'Use letters or numbers in the type name.'])->withInput();
        }
        if (in_array($key, TemplateType::RESERVED_KEYS, true) || TemplateType::where('key', $key)->exists()) {
            return back()->withErrors(['type_label' => 'A type with this name already exists. Choose a different name.'])->withInput();
        }

        $type = TemplateType::create([
            'key'         => $key,
            'label'       => $data['type_label'],
            'icon'        => $data['type_icon'],
            'description' => $data['type_description'] ?? null,
        ]);

        AuditLog::record('Template Type Added', "{$type->label} added by " . auth()->user()->name);

        return back()->with('success', "{$type->label} added. The Secretary can now upload it.");
    }

    public function update(Request $request, TemplateType $templateType)
    {
        $data = $this->validated($request);

        // The key stays the same so its templates keep their folder after a rename.
        $taken = TemplateType::where('id', '!=', $templateType->id)
            ->whereRaw('LOWER(label) = ?', [mb_strtolower($data['type_label'])])
            ->exists();
        if ($taken) {
            return back()->withErrors(['type_label' => 'A type with this name already exists. Choose a different name.']);
        }

        $before = $templateType->label;
        $templateType->update([
            'label'       => $data['type_label'],
            'icon'        => $data['type_icon'],
            'description' => $data['type_description'] ?? null,
        ]);

        AuditLog::record('Template Type Updated', "{$before} updated by " . auth()->user()->name);

        return back()->with('success', "{$templateType->label} saved.");
    }

    public function toggle(TemplateType $templateType)
    {
        if ($templateType->isProtected()) {
            return back()->withErrors(['type' => "{$templateType->label} is used by other parts of the system, so it can't be hidden."]);
        }

        $templateType->update(['is_active' => !$templateType->is_active]);

        AuditLog::record('Template Type Updated', $templateType->label . ($templateType->is_active ? ' shown again' : ' hidden') . ' by ' . auth()->user()->name);

        return back()->with('success', $templateType->is_active
            ? "{$templateType->label} can be chosen again."
            : "{$templateType->label} is hidden. Its existing templates stay available.");
    }

    public function destroy(TemplateType $templateType)
    {
        if ($templateType->isProtected()) {
            return back()->withErrors(['type' => "{$templateType->label} is used by other parts of the system, so it can't be deleted."]);
        }
        if ($templateType->isInUse()) {
            return back()->withErrors(['type' => "{$templateType->label} already has records, so it can't be deleted. You can hide it instead."]);
        }

        $label = $templateType->label;
        $templateType->delete();

        AuditLog::record('Template Type Deleted', "{$label} deleted by " . auth()->user()->name);

        return back()->with('success', "{$label} deleted.");
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'type_label'       => 'required|string|max:60',
            'type_icon'        => ['required', Rule::in(TemplateType::ICONS)],
            'type_description' => 'nullable|string|max:500',
        ], [], ['type_label' => 'Type name', 'type_icon' => 'Icon', 'type_description' => 'Description']);
    }
}
