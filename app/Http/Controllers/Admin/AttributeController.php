<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttributeRequest;
use App\Http\Requests\UpdateAttributeRequest;
use App\Models\Category;
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
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $attributes = $this->attributeService->getAll($perPage);
        
        return view('admin.attributes.index', compact('attributes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::active()->ordered()->get();
        $types = $this->attributeService->getTypes();
        
        return view('admin.attributes.create', compact('categories', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAttributeRequest $request)
    {
        try {
            $attribute = $this->attributeService->create($request->all());
            
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
        $categories = Category::active()->ordered()->get();
        $types = $this->attributeService->getTypes();
        
        if (!$attribute) {
            return redirect()->route('admin.attributes.index')
                ->with('error', 'Attribute not found.');
        }
        
        // Prepare options for form display
        $attribute->options = $this->attributeService->prepareOptionsForForm($attribute->options);
        
        return view('admin.attributes.edit', compact('attribute', 'categories', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttributeRequest $request, string $id)
    {
        try {
            $attribute = $this->attributeService->update($id, $request->all());
            
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

    /**
     * Update sort order.
     */
    public function updateSortOrder(Request $request)
    {
        try {
            $result = $this->attributeService->updateSortOrder($request->all());
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sort order updated successfully.'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating sort order.'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating sort order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get attributes by category ID (AJAX).
     */
    public function getByCategory($categoryId)
    {
        try {
            $attributes = $this->attributeService->getByCategoryId($categoryId, true);
            
            return response()->json([
                'success' => true,
                'attributes' => $attributes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching attributes.'
            ], 500);
        }
    }
}
