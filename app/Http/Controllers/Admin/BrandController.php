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
        return view("admin.brand.index");
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
        return $request->all();
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        //
    }
}
