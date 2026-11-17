<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::guard('web')->user();

        $products = $user->wishlistProducts()
            ->with(['categoryRelation', 'brandRelation', 'productVariants'])
            ->where('is_active', true)
            ->get()
            ->map(fn (Product $p) => $p->toStorefrontArray())
            ->values()
            ->all();

        return response()->json([
            'items' => collect($products)->pluck('id')->values()->all(),
            'products' => $products,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::guard('web')->user();

        $validated = $request->validate([
            'product_id' => ['nullable', 'integer', 'exists:products,id,is_active,1'],
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['integer'],
        ]);

        $candidateIds = [];
        if (! empty($validated['product_id'])) {
            $candidateIds[] = (int) $validated['product_id'];
        }
        if (! empty($validated['product_ids'])) {
            foreach ($validated['product_ids'] as $id) {
                $candidateIds[] = (int) $id;
            }
        }

        $activeIds = Product::whereIn('id', array_unique($candidateIds))
            ->where('is_active', true)
            ->pluck('id')
            ->all();

        foreach ($activeIds as $productId) {
            Wishlist::firstOrCreate([
                'user_id' => $user->id,
                'product_id' => $productId,
            ]);
        }

        $items = $user->wishlists()->pluck('product_id')->values()->all();

        return response()->json([
            'success' => true,
            'items' => $items,
        ], 201);
    }

    public function destroy(int $product): JsonResponse
    {
        /** @var User $user */
        $user = Auth::guard('web')->user();

        Wishlist::where('user_id', $user->id)
            ->where('product_id', $product)
            ->delete();

        $items = $user->wishlists()->pluck('product_id')->values()->all();

        return response()->json([
            'success' => true,
            'items' => $items,
        ]);
    }
}

