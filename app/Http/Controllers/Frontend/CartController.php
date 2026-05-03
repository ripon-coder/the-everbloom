<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function sync(Request $request)
    {
        $cart = $request->input('cart', []);
        
        $totalQuantity = 0;
        foreach ($cart as $item) {
            $totalQuantity += (int) ($item['quantity'] ?? 0);
        }

        if ($totalQuantity > 30) {
            return response()->json([
                'success' => false, 
                'message' => 'You can not add more than 30 products to your cart.'
            ], 422);
        }

        session()->put('cart', $cart);
        
        return response()->json(['success' => true]);
    }
}
