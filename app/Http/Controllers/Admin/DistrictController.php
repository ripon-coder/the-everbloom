<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDistrictRequest;
use App\Http\Requests\UpdateDistrictRequest;
use App\Repositories\Contracts\DistrictRepository;

class DistrictController extends Controller
{

    private $district;

    public function __construct(DistrictRepository $district)
    {
        $this->district = $district;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("admin.districts.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDistrictRequest $request)
    {
        $data = $this->district->store($request->validated());
        if ($data) {
            return back()->with("success", "District Created Successfully!");
        }
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
        $district =  $this->district->edit($id);
        return view("admin.districts.edit", compact('district'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDistrictRequest $request, string $id)
    {
        $true =  $this->district->update($id, $request->validated());
        if ($true) {
            return back()->with("success", "District Updated Successfully!");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
