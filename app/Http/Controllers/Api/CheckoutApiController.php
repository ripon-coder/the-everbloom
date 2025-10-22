<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Repositories\Contracts\DistrictRepository;
use Illuminate\Http\Request;

class CheckoutApiController extends Controller
{
    public function DistrictList(){
       return app(DistrictRepository::class)->districtList();
    }
}
