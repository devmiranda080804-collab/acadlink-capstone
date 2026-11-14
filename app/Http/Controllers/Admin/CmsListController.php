<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\CmsOption;
use App\Support\AcademicTerm;
use App\Support\CmsLists;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

// System CMS → Dropdown Lists (Admin only): add items, rename them, hide or show them.
// Nothing is deleted, so records that use an item keep working.
class CmsListController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['list' => ['required', Rule::in(array_keys(array_filter(CmsLists::LISTS, fn ($l) => $l['add'])))]]);
        $list = $request->list;

        if ($list === CmsLists::PROGRAMS) {
            $request->merge(['key' => strtoupper(trim((string) $request->key))]);
            $request->validate([
                'key'   => ['required', 'regex:/^[A-Z0-9][A-Z0-9-]{1,19}$/', Rule::unique('cms_options', 'key')->where('list', $list)],
                'label' => 'required|string|max:200',
            ], [
                'key.regex'  => 'Use 2 to 20 capital letters, numbers or dashes for the code, for example BSBA-OM.',
                'key.unique' => 'That program code is already on the list.',
            ], ['key' => 'Program code', 'label' => 'Full name']);
            $label = trim($request->label);
        } else {
            $request->merge(['key' => trim((string) $request->key)]);
            $request->validate([
                'key' => ['required', function ($attribute, $value, $fail) {
                    if (!AcademicTerm::isValidSchoolYear($value)) {
                        $fail('Write the academic year as two years in a row, for example 2027-2028.');
                    }
                }, Rule::unique('cms_options', 'key')->where('list', $list)],
            ], ['key.unique' => 'That academic year is already on the list.'], ['key' => 'Academic year']);
            $label = $request->key;
        }

        CmsOption::create([
            'list'       => $list,
            'key'        => $request->key,
            'label'      => $label,
            'sort_order' => (int) CmsOption::where('list', $list)->max('sort_order') + 1,
        ]);
        if ($list === CmsLists::SCHOOL_YEARS) {
            $this->sortYears();
        }
        CmsLists::flush();

        $title = CmsLists::LISTS[$list]['title'];
        AuditLog::record('Dropdown List Updated', "{$request->key} added to {$title} by " . auth()->user()->name);

        return back()->with(['success' => "{$request->key} added to {$title}.", 'cms_panel' => 'panel-lists']);
    }

    public function update(Request $request, CmsOption $option)
    {
        $settings = CmsLists::LISTS[$option->list] ?? abort(404);
        $request->validate([
            'label'     => $settings['rename'] ? 'sometimes|required|string|max:200' : 'prohibited',
            'is_active' => 'sometimes|boolean',
        ], [], ['label' => 'Full name']);

        if ($request->has('is_active') && !$request->boolean('is_active') && $option->is_active) {
            if (CmsOption::where('list', $option->list)->where('is_active', true)->count() <= 1) {
                return back()->withErrors(['cms_list' => 'At least one item has to stay shown in ' . $settings['title'] . '.'])->with('cms_panel', 'panel-lists');
            }
            $current = [CmsLists::SCHOOL_YEARS => AcademicTerm::currentSchoolYear(), CmsLists::SEMESTERS => AcademicTerm::currentSemester()][$option->list] ?? null;
            if ($current === $option->key) {
                return back()->withErrors(['cms_list' => $option->key . ' is the current term, so it can\'t be hidden. Change the term in Settings first.'])->with('cms_panel', 'panel-lists');
            }
        }

        $before = $option->only(['label', 'is_active']);
        $option->update($request->only(array_filter(['label', 'is_active'], fn ($f) => $request->has($f))));
        CmsLists::flush();

        $what = $option->wasChanged('is_active')
            ? ($option->is_active ? 'shown again' : 'hidden')
            : 'renamed from "' . $before['label'] . '" to "' . $option->label . '"';
        if ($option->wasChanged()) {
            AuditLog::record('Dropdown List Updated', "{$option->key} in {$settings['title']} {$what} by " . auth()->user()->name);
        }

        return back()->with(['success' => "{$option->key} {$what}.", 'cms_panel' => 'panel-lists']);
    }

    // Academic years always read oldest to newest.
    protected function sortYears(): void
    {
        CmsOption::where('list', CmsLists::SCHOOL_YEARS)->orderBy('key')->get()
            ->each(fn ($year, $i) => $year->update(['sort_order' => $i + 1]));
    }
}
