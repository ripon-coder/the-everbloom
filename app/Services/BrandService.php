<?php
namespace App\Services;

use App\Repositories\Contracts\BrandRepository;

class BrandService
{
    protected $brandRepository;
    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    public function create(array $validated)
    {
        $brand = $this->brandRepository->create($validated);
        if (array_key_exists("logo", $validated) && $validated['logo']) {
            $brand->uploadImage($validated['logo'], "brand_logo");
        }
        return $brand;
    }

    public function update($id, array $validated)
    {
        $brand = $this->brandRepository->update($id, $validated);
        if (array_key_exists("logo", $validated) && $validated['logo']) {
            $brand->uploadImage($validated['logo'], "brand_logo");
        }
        return $brand;
    }
}
