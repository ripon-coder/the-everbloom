<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function toggle(Request $request)
    {
        $productId = $request->input('product_id');
        $userId = Auth::id();

        $wishlistItem = Wishlist::where('product_id', $productId)
            ->where('user_id', $userId)
            ->first();

        if ($wishlistItem) {
            $wishlistItem->delete();
            return response()->json([
                'success' => true,
                'in_wishlist' => false,
                'message' => 'Product removed from wishlist'
            ]);
        }

        Wishlist::create([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        return response()->json([
            'success' => true,
            'in_wishlist' => true,
            'message' => 'Product added to wishlist'
        ]);
    }

    public function getWishlist(Request $request)
    {
        $userId = Auth::id();

        $wishlistIds = Wishlist::where('user_id', $userId)
            ->pluck('product_id')
            ->toArray();

        return response()->json([
            'success' => true,
            'wishlist_ids' => $wishlistIds
        ]);
    }
}
