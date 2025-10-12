<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttributeValueRequest;
use App\Http\Requests\UpdateAttributeValueRequest;
use App\Models\Attribute;
use App\Repositories\Contracts\AttributeRepository;
use App\Repositories\Contracts\AttributeValueRepository;
use Illuminate\Http\Request;

class AttributeValueController extends Controller
{

    private $attributeValueRepository;
    public function __construct(AttributeValueRepository $attributeValueRepository)
    {
        $this->attributeValueRepository = $attributeValueRepository;
    }

    /**
     * Display a listing of attribute values.
     */
    public function index(Request $request)
    {
        $attributes = app(AttributeRepository::class)->getAll();
        $attributeValues = $this->attributeValueRepository->getAllWithPagination(20);
        return view('admin.attribute-values.index', compact('attributeValues', 'attributes'));
    }

    /**
     * Show the form for creating a new attribute value.
     */
    public function create()
    {
        try {
            $attributes = app(AttributeRepository::class)->getAll();
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
            $this->attributeValueRepository->create($request->validated());
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
            $attributeValue = $this->attributeValueRepository->findById($id);
            $attributes = app(AttributeRepository::class)->getAll();

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
            $this->attributeValueRepository->update($id,$request->validated());
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
            $this->attributeValueRepository->delete($id);
            return redirect()->route('admin.attribute-values.index')
                ->with('success', 'Attribute value deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting attribute value.');
        }
    }
}
