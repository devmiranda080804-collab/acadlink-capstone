<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemplateElement;
use Illuminate\Http\Request;

class TemplateElementsController extends Controller
{
    public function index(Request $request)
    {
        $types = TemplateElement::knownTypes();
        $activeType = $request->get('type', $types[0] ?? 'syllabus');
        $status = $request->get('status', 'active');

        $elements = TemplateElement::forType($activeType)
            ->when($status === 'archived', fn($q) => $q->where('is_active', false), fn($q) => $q->where('is_active', true))
            ->orderBy('order')
            ->get();

        return view('admin.cms', compact('types', 'activeType', 'elements', 'status'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'template_type' => 'required|string|max:100',
            'label'         => 'required|string|max:255',
            'instructions'  => 'nullable|string',
            'field_type'    => 'required|in:text,rich_text,table',
            'is_required'   => 'boolean',
        ]);

        $nextOrder = TemplateElement::forType($request->template_type)->max('order') + 1;

        TemplateElement::create([
            'template_type' => $request->template_type,
            'label'         => $request->label,
            'instructions'  => $request->instructions,
            'field_type'    => $request->field_type,
            'is_required'   => $request->boolean('is_required'),
            'order'         => $nextOrder,
        ]);

        return back()->with('success', 'Template element added.');
    }

    public function update(Request $request, TemplateElement $templateElement)
    {
        $request->validate([
            'label'        => 'required|string|max:255',
            'instructions' => 'nullable|string',
            'field_type'   => 'required|in:text,rich_text,table',
            'is_required'  => 'boolean',
        ]);

        $templateElement->update([
            'label'        => $request->label,
            'instructions' => $request->instructions,
            'field_type'   => $request->field_type,
            'is_required'  => $request->boolean('is_required'),
        ]);

        return back()->with('success', 'Template element updated.');
    }

    public function reorder(Request $request, TemplateElement $templateElement)
    {
        $request->validate(['direction' => 'required|in:up,down']);

        $sibling = TemplateElement::forType($templateElement->template_type)
            ->where('order', $request->direction === 'up' ? '<' : '>', $templateElement->order)
            ->orderBy('order', $request->direction === 'up' ? 'desc' : 'asc')
            ->first();

        if ($sibling) {
            [$a, $b] = [$templateElement->order, $sibling->order];
            $templateElement->update(['order' => $b]);
            $sibling->update(['order' => $a]);
        }

        return back();
    }

    public function toggleActive(TemplateElement $templateElement)
    {
        $templateElement->update(['is_active' => ! $templateElement->is_active]);

        return back()->with('success', $templateElement->is_active
            ? 'Template element restored.'
            : 'Template element archived.');
    }

    public function destroy(TemplateElement $templateElement)
    {
        $templateElement->delete();

        return back()->with('success', 'Template element deleted.');
    }
}
