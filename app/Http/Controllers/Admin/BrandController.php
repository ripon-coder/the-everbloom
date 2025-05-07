<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBrandRequest;
use App\Models\Brand;
use App\Repositories\Contracts\BrandRespositoryInterface;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    protected $brand_respository;
    public function __construct(BrandRespositoryInterface $brand_respository)
    {
        $this->brand_respository = $brand_respository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = $this->brand_respository->pagination(5);
        return view("admin.brand.index",compact("brands"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("admin.brand.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateBrandRequest $request)
    {
        $this->brand_respository->store($request->all());
        return back()->with("success","Created Successfully!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        $brand = $this->brand_respository->idBy($brand->id);
        return view('admin.brand.edit',compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $this->brand_respository->update($request->all(),$brand->id);
        return back()->with("success","Updated Successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        //
    }
}
