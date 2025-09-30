<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryControllerApi extends Controller
{

    public function __construct(){
        
    }
    public function ParentCategory(){
        return "parent category";
    }
}
