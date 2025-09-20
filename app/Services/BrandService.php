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
    public function all()
    {
        return $this->brandRepository->all();
    }
}