<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttributeRequest;
use App\Http\Requests\UpdateAttributeRequest;
use App\Models\Attribute;
use App\Services\AttributeService;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    protected $attributeService;

    public function __construct(AttributeService $attributeService)
    {
        $this->attributeService = $attributeService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attributes = $this->attributeService->getAllWithPagination();
        return view('admin.attributes.index', compact('attributes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.attributes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAttributeRequest $request)
    {
        try {
            $this->attributeService->create($request->all());

            return redirect()->route('admin.attributes.index')
                ->with('success', 'Attribute created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating attribute: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $attribute = $this->attributeService->findById($id);

        if (!$attribute) {
            return redirect()->route('admin.attributes.index')
                ->with('error', 'Attribute not found.');
        }

        return view('admin.attributes.show', compact('attribute'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $attribute = $this->attributeService->findById($id);

        if (!$attribute) {
            return redirect()->route('admin.attributes.index')
                ->with('error', 'Attribute not found.');
        }

        return view('admin.attributes.edit', compact('attribute'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttributeRequest $request, string $id)
    {
        try {
            $this->attributeService->update($id, $request->all());

            return redirect()->route('admin.attributes.index')
                ->with('success', 'Attribute updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating attribute: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $result = $this->attributeService->delete($id);

            if ($result) {
                return redirect()->route('admin.attributes.index')
                    ->with('success', 'Attribute deleted successfully.');
            }

            return redirect()->route('admin.attributes.index')
                ->with('error', 'Attribute not found.');
        } catch (\Exception $e) {
            return redirect()->route('admin.attributes.index')
                ->with('error', 'Error deleting attribute: ' . $e->getMessage());
        }
    }

    // For Product Ajax
    public function getValues(Attribute $attribute)
    {
        $values = $attribute->attributeValues()->active()->get(['id', 'value']);
        return response()->json($values);
    }
}
