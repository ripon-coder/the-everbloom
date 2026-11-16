<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminAttributeController extends Controller
{
    public function index()
    {
        $attributes = Attribute::with(['values', 'categories'])->orderBy('sort_order')->orderBy('name')->get();
        $categories = Category::with('parent')->orderBy('level')->orderBy('sort_order')->get();

        return view('admin.attributes.index', compact('attributes', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'display_type' => ['nullable', 'in:button,color,select'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
        ]);

        $slug = Str::slug($validated['name']);
        if (Attribute::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::lower(Str::random(4));
        }

        $attribute = Attribute::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'display_type' => $validated['display_type'] ?? 'button',
            'sort_order' => (Attribute::max('sort_order') ?? 0) + 1,
            'is_active' => true,
        ]);

        $attribute->categories()->sync($request->input('category_ids', []));

        return redirect()->route('admin.attributes.index')->with('success', 'Attribute created successfully.');
    }

    public function update(Request $request, Attribute $attribute)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
        ]);

        $attribute->update([
            'name' => $validated['name'],
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $attribute->is_active,
        ]);

        if ($request->has('category_ids') || $request->has('sync_categories')) {
            $attribute->categories()->sync($request->input('category_ids', []));
        }

        return redirect()->route('admin.attributes.index')->with('success', 'Attribute updated successfully.');
    }

    public function storeValue(Request $request, Attribute $attribute)
    {
        $validated = $request->validate([
            'value' => ['required', 'string', 'max:120'],
            'color_code' => ['nullable', 'string', 'max:30'],
        ]);

        AttributeValue::create([
            'attribute_id' => $attribute->id,
            'value' => $validated['value'],
            'slug' => Str::slug($validated['value']),
            'color_code' => $validated['color_code'] ?? null,
            'sort_order' => ($attribute->values()->max('sort_order') ?? 0) + 1,
            'is_active' => true,
        ]);

        return redirect()->route('admin.attributes.index')->with('success', 'Attribute value added successfully.');
    }

    public function destroyValue(AttributeValue $value)
    {
        $value->delete();

        return redirect()->route('admin.attributes.index')->with('success', 'Attribute value removed.');
    }
}

