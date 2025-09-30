<?php 
namespace App\Services\Api;

use App\Repositories\Contracts\BrandRepository;

class BrandServiceApi{
    public $brandRepository;
    public function __construct(BrandRepository $brandRepository) {
        $this->brandRepository = $brandRepository;
    }
    public function AllBrand(){
        return $this->brandRepository->AllBrandApi();
    }
}