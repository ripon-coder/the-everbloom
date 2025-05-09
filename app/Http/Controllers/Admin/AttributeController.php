<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAttributeRequest;
use App\Models\Attribute;
use App\Repositories\Contracts\AttributeRespositoryInterface;

class AttributeController extends Controller
{
    protected $attribute_respository;

    public function __construct(AttributeRespositoryInterface $attribute_respository)
    {
        $this->attribute_respository = $attribute_respository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attributes = $this->attribute_respository->pagination(20);
        return view("admin.attribute.index", compact("attributes"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("admin.attribute.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateAttributeRequest $request)
    {
        $this->attribute_respository->store($request->all());
        return back()->with("success", "Created Successfully!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Attribute $attribute)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attribute $attribute)
    {
        $attribute = $this->attribute_respository->idBy($attribute->id);
        return view('admin.attribute.edit', compact('attribute'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateAttributeRequest $request, Attribute $attribute)
    {
        $this->attribute_respository->update($request->all(), $attribute->id);
        return back()->with("success", "Updated Successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attribute $attribute)
    {
        $this->attribute_respository->destroy($attribute->id);
        return back()->with("success", "Deleted Successfully!");
    }
}
