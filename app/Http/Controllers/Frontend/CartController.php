<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function sync(Request $request)
    {
        $cart = $request->input('cart', []);
        session()->put('cart', $cart);
        
        return response()->json(['success' => true]);
    }
}
