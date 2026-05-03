<?php

namespace App\Http\Controllers\Admin;

use App\Models\FlashSale;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFlashSaleRequest;
use App\Http\Requests\UpdateFlashSaleRequest;
use App\Repositories\Contracts\FlashSaleRepository;
use App\Services\FlashSaleService;

class FlashSaleController extends Controller
{
    protected $flashSaleRepository;
    protected $flashSaleService;

    public function __construct(FlashSaleRepository $flashSaleRepository, FlashSaleService $flashSaleService)
    {
        $this->flashSaleRepository = $flashSaleRepository;
        $this->flashSaleService = $flashSaleService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data["flashSales"] = $this->flashSaleRepository->index();
        return view("admin.flash-sales.index", $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = $this->flashSaleRepository->create();
        return view("admin.flash-sales.create", $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFlashSaleRequest $request)
    {
        return $this->flashSaleService->store($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(FlashSale $flashSale)
    {
        $flashSale->load('products.firstImage');
        $this->flashSaleService->show($flashSale);
        return view("admin.flash-sales.show", compact('flashSale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FlashSale $flashSale)
    {
        $data = $this->flashSaleRepository->edit($flashSale->id);
        return view("admin.flash-sales.edit", $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFlashSaleRequest $request, FlashSale $flashSale)
    {
        return $this->flashSaleService->update($flashSale->id, $request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FlashSale $flashSale)
    {
        return $this->flashSaleService->destroy($flashSale->id);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        return $this->flashSaleService->restore($id);
    }

    /**
     * Force delete the specified resource from storage.
     */
    public function forceDelete($id)
    {
        return $this->flashSaleService->forceDelete($id);
    }
}
