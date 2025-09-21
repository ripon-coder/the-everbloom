<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttributeValueRequest;
use App\Http\Requests\UpdateAttributeValueRequest;
use App\Models\Attribute;
use Illuminate\Http\Request;

class AttributeValueController extends Controller
{

    /**
     * Display a listing of attribute values.
     */
    public function index(Request $request)
    {
        $query = \App\Models\AttributeValue::with('attribute');
        
        // Apply search filter
        if ($request->filled('search')) {
            $query->where('value', 'like', '%' . $request->search . '%');
        }
        
        // Apply attribute filter
        if ($request->filled('attribute_id')) {
            $query->where('attribute_id', $request->attribute_id);
        }
        
        $attributeValues = $query->latest()->paginate(10);
        $attributes = \App\Models\Attribute::active()->ordered()->get();
        
        return view('admin.attribute-values.index', compact('attributeValues', 'attributes'));
    }

    /**
     * Show the form for creating a new attribute value.
     */
    public function create()
    {
        try {
            $attributes = Attribute::active()->ordered()->get();

            return view('admin.attribute-values.create', compact('attributes'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading form.');
        }
    }

    /**
     * Store a newly created attribute value in storage.
     */
    public function store(StoreAttributeValueRequest $request)
    {
        try {
            \App\Models\AttributeValue::create($request->validated());

            return redirect()->route('admin.attribute-values.index')
                ->with('success', 'Attribute value created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating attribute value: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified attribute value.
     */
    public function edit($id)
    {
        try {
            $attributeValue = \App\Models\AttributeValue::findOrFail($id);
            $attributes = Attribute::active()->ordered()->get();

            return view('admin.attribute-values.edit', compact('attributeValue', 'attributes'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading form.');
        }
    }

    /**
     * Update the specified attribute value in storage.
     */
    public function update(UpdateAttributeValueRequest $request, $id)
    {
        try {
            $attributeValue = \App\Models\AttributeValue::findOrFail($id);
            $attributeValue->update($request->validated());

            return redirect()->route('admin.attribute-values.index')
                ->with('success', 'Attribute value updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating attribute value: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $attributeValue = \App\Models\AttributeValue::findOrFail($id);
            $attributeValue->delete();

            return redirect()->route('admin.attribute-values.index')
                ->with('success', 'Attribute value deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting attribute value.');
        }
    }
}
