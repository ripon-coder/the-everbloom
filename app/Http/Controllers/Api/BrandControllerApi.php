<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Api\BrandServiceApi;
use Illuminate\Http\Request;

class BrandControllerApi extends Controller
{
    public $brandServiceApi;
    public function __construct(BrandServiceApi $brandServiceApi) {
        $this->brandServiceApi = $brandServiceApi;
    }
    public function AllBrand(){
        return $this->brandServiceApi->AllBrand();
    }
}
