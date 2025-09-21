<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttributeValueRequest;
use App\Http\Requests\UpdateAttributeValueRequest;
use App\Models\Attribute;
use App\Services\AttributeValueService;
use Illuminate\Http\Request;

class AttributeValueController extends Controller
{
    protected $attributeValueService;

    public function __construct(AttributeValueService $attributeValueService)
    {
        $this->attributeValueService = $attributeValueService;
    }

    /**
     * Display a listing of attribute values.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $attributeId = $request->input('attribute_id');

        try {
            if ($search) {
                $attributeValues = $this->attributeValueService->searchByValue($search, $perPage);
            } elseif ($attributeId) {
                $attributeValues = $this->attributeValueService->getByAttributeId($attributeId);
                // Convert collection to paginator for consistent view handling
                $attributeValues = new \Illuminate\Pagination\LengthAwarePaginator(
                    $attributeValues,
                    $attributeValues->count(),
                    $perPage,
                    $request->input('page', 1),
                    ['path' => $request->url(), 'query' => $request->query()]
                );
            } else {
                $attributeValues = $this->attributeValueService->getAll($perPage);
            }

            $attributes = Attribute::active()->ordered()->get();

            return view('admin.attribute-values.index', compact('attributeValues', 'attributes'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error fetching attribute values.');
        }
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
            $attributeValue = $this->attributeValueService->create($request->all());

            return redirect()->route('admin.attribute-values.index')
                ->with('success', 'Attribute value created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating attribute value: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified attribute value.
     */
    public function show($id)
    {
        try {
            $attributeValue = $this->attributeValueService->findById($id);

            if (!$attributeValue) {
                return redirect()->route('admin.attribute-values.index')
                    ->with('error', 'Attribute value not found.');
            }

            return view('admin.attribute-values.show', compact('attributeValue'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error showing attribute value.');
        }
    }

    /**
     * Show the form for editing the specified attribute value.
     */
    public function edit($id)
    {
        try {
            $attributeValue = $this->attributeValueService->findById($id);

            if (!$attributeValue) {
                return redirect()->route('admin.attribute-values.index')
                    ->with('error', 'Attribute value not found.');
            }

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
            $attributeValue = $this->attributeValueService->update($id, $request->all());

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
            $result = $this->attributeValueService->delete($id);

            if ($result) {
                return redirect()->route('admin.attribute-values.index')
                    ->with('success', 'Attribute value deleted successfully.');
            } else {
                return redirect()->route('admin.attribute-values.index')
                    ->with('error', 'Attribute value not found.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting attribute value.');
        }
    }
}
