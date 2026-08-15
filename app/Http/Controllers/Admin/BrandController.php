<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Repositories\Contracts\BrandRepository;
use App\Services\BrandService;

class BrandController extends Controller
{

    protected $brandService;
    protected $brandRepository;
    public function __construct(BrandService $brandService, BrandRepository $brandRepository)
    {
        $this->brandService = $brandService;
        $this->brandRepository = $brandRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(\Illuminate\Http\Request $request)
    {
        $filters = $request->only(['search', 'status']);
        $data['brands'] = $this->brandRepository->all($filters);
        return view("admin.brands.index", $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("admin.brands.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBrandRequest $request)
    {
        $validated = $request->validated();
        $brand = $this->brandService->create($validated);
        return redirect()->route('admin.brands.index')->with('success', 'Brand created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['brand'] = $this->brandRepository->FindById($id);
        return view("admin.brands.edit", $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBrandRequest $request, string $id)
    {
        $validated = $request->validated();
        $brand = $this->brandService->update($id, $validated);
        return redirect()->back()->with('success', 'Brand updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
         if($this->brandRepository->DeleteFindBuyId($id)){
            return redirect()->route("admin.brands.index")->with("danger","Brand Deleted Successfully!");
         }
    }
}
