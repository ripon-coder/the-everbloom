<?php

namespace App\Http\Controllers\Admin;

use App\Models\AttributeValue;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAttributeValueRequest;
use App\Repositories\Contracts\AttributeValueRespositoryInterface;

class AttributeValueController extends Controller
{
    protected $attributeValueRepository;

    public function __construct(AttributeValueRespositoryInterface $attributeValueRepository)
    {
        $this->attributeValueRepository = $attributeValueRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attributeValues = $this->attributeValueRepository->pagination(20);
        return view('admin.attribute-value.index', compact('attributeValues'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $attributes = $this->attributeValueRepository->attributes();
        return view('admin.attribute-value.create', compact('attributes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateAttributeValueRequest $request)
    {
        $this->attributeValueRepository->store($request->all());
        return back()->with('success', 'Created Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(AttributeValue $attributeValue)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AttributeValue $attributeValue)
    {
        $attributes = $this->attributeValueRepository->attributes();
        $attributeValue = $this->attributeValueRepository->idBy($attributeValue->id);
        return view('admin.attribute-value.edit', compact('attributeValue','attributes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateAttributeValueRequest $request, AttributeValue $attributeValue)
    {
        $this->attributeValueRepository->update($request->all(), $attributeValue->id);
        return back()->with('success', 'Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AttributeValue $attributeValue)
    {
        $this->attributeValueRepository->destroy($attributeValue->id);
        return back()->with('success', 'Deleted Successfully!');
    }
}
