<?php

namespace App\Http\Controllers;

use App\Services\Checkout\PriceResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function quote(Request $request, PriceResolver $priceResolver): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['nullable', 'array'],
            'items.*.productId' => ['required', 'integer'],
            'items.*.variantId' => ['nullable', 'string', 'max:80'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:10'],
            'discountCode' => ['nullable', 'string', 'max:60'],
            'shippingMethod' => ['nullable', 'in:standard,express'],
        ]);

        $quote = $priceResolver->quote(
            items: $validated['items'] ?? [],
            couponCode: $validated['discountCode'] ?? null,
            shippingMethod: $validated['shippingMethod'] ?? 'standard',
        );

        return response()->json($quote);
    }
}

