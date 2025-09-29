<?php

namespace App\Http\Controllers\Admin;

use App\Models\Coupon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Repositories\Contracts\CouponRepository;
use App\Services\CouponService;

class CouponController extends Controller
{
    protected $couponRepository;
    protected $couponService;

    public function __construct(CouponRepository $couponRepository, CouponService $couponService)
    {
        $this->couponRepository = $couponRepository;
        $this->couponService = $couponService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data["coupons"] = $this->couponRepository->index();
        return view("admin.coupons.index", $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = $this->couponRepository->create();
        return view("admin.coupons.create", $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCouponRequest $request)
    {
        return $this->couponService->store($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(Coupon $coupon)
    {
        $this->couponService->show($coupon);
        return view("admin.coupons.show", compact('coupon'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coupon $coupon)
    {
        $data = $this->couponRepository->edit($coupon->id);
        return view("admin.coupons.edit", $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCouponRequest $request, Coupon $coupon)
    {
        return $this->couponService->update($coupon->id, $request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon)
    {
        return $this->couponService->destroy($coupon->id);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        return $this->couponService->restore($id);
    }

    /**
     * Force delete the specified resource from storage.
     */
    public function forceDelete($id)
    {
        return $this->couponService->forceDelete($id);
    }
}
