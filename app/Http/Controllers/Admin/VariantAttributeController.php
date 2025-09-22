<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVariantAttributeRequest;
use App\Http\Requests\UpdateVariantAttributeRequest;
use App\Models\VariantAttribute;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class VariantAttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $variantAttributes = VariantAttribute::with(['productVariant.product', 'attribute', 'attributeValue'])
            ->latest()
            ->paginate(10);

        return view("admin.variant-attributes.index", compact('variantAttributes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $productVariants = ProductVariant::active()->get();
        
        return view("admin.variant-attributes.create", compact('productVariants'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVariantAttributeRequest $request)
    {
        try {
            VariantAttribute::create([
                'product_variant_id' => $request->product_variant_id,
                'attribute_id' => $request->attribute_id,
                'attribute_value_id' => $request->attribute_value_id,
            ]);

            return redirect()->route('admin.variant-attributes.index')
                ->with('success', 'Variant attribute created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating variant attribute: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(VariantAttribute $variantAttribute)
    {
        $variantAttribute->load(['productVariant.product', 'attribute', 'attributeValue']);
        
        return view("admin.variant-attributes.show", compact('variantAttribute'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VariantAttribute $variantAttribute)
    {
        $productVariants = ProductVariant::active()->get();
        $variantAttribute->load(['productVariant.product', 'attribute', 'attributeValue']);
        
        return view("admin.variant-attributes.edit", compact('variantAttribute', 'productVariants'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVariantAttributeRequest $request, VariantAttribute $variantAttribute)
    {
        try {
            $variantAttribute->update([
                'attribute_id' => $request->attribute_id,
                'attribute_value_id' => $request->attribute_value_id,
            ]);

            return redirect()->route('admin.variant-attributes.index')
                ->with('success', 'Variant attribute updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating variant attribute: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VariantAttribute $variantAttribute)
    {
        try {
            $variantAttribute->delete();

            return redirect()->route('admin.variant-attributes.index')
                ->with('success', 'Variant attribute deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting variant attribute: ' . $e->getMessage());
        }
    }
}
