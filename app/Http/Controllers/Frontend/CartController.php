<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Display the shopping cart page.
     *
     * @return View
     */
    public function index(): View
    {
        return view('pages.cart.index');
    }
    public function sync(Request $request)
    {
        $cart = $request->input('cart', []);
        $updatedCart = [];
        $totalQuantity = 0;
        $hasInactive = false;

        foreach ($cart as $item) {
            $productId = $item['product_id'] ?? null;
            $variantId = $item['variant_id'] ?? null;
            $qty = (int) ($item['quantity'] ?? 1);

            if (!$productId) continue;

            $product = \App\Models\Product::find($productId);
            $isProductActive = $product && $product->status === \App\Constants\ProductStatus::ACTIVE;

            $isVariantActive = true;
            $availableStock = 0;

            if ($variantId) {
                $variant = \App\Models\ProductVariant::find($variantId);
                $isVariantActive = $variant && $variant->status === \App\Constants\ProductVariantStatus::ACTIVE;
                if ($isVariantActive && $isProductActive) {
                    $availableStock = (int) $variant->stock;
                }
            } else {
                if ($isProductActive && $product) {
                    $availableStock = (int) $product->variants()->active()->sum('stock');
                }
            }

            $isAvailable = $isProductActive && $isVariantActive;

            if (!$isAvailable) {
                $hasInactive = true;
                $item['available'] = false;
                $item['is_active'] = false;
                $item['available_stock'] = 0;
                $item['status_message'] = 'No longer available';
                $item['line_total'] = 0;
            } else {
                $item['available'] = $availableStock > 0;
                $item['is_active'] = true;
                $item['status_message'] = null;
                // Cap quantity at available stock
                if ($qty > $availableStock) {
                    $qty = max(0, $availableStock);
                }
                $item['quantity'] = $qty;
                $item['available_stock'] = $availableStock;
                $item['line_total'] = $qty * (float) ($item['unit_final_price'] ?? 0);
                if ($qty > 0) {
                    $totalQuantity += $qty;
                }
            }

            $updatedCart[] = $item;
        }

        if ($totalQuantity > 30) {
            return response()->json([
                'success' => false, 
                'message' => 'You cannot add more than 30 products to your cart.'
            ], 422);
        }

        session()->put('cart', $updatedCart);
        
        return response()->json([
            'success' => true,
            'cart' => $updatedCart,
            'has_inactive' => $hasInactive
        ]);
    }
}
