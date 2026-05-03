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
        $sessionId = session()->getId();

        $query = Wishlist::where('product_id', $productId);

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $wishlistItem = $query->first();

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
            'session_id' => $userId ? null : $sessionId
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
        $sessionId = session()->getId();

        $wishlistIds = Wishlist::when($userId, function($q) use ($userId) {
                return $q->where('user_id', $userId);
            }, function($q) use ($sessionId) {
                return $q->where('session_id', $sessionId);
            })
            ->pluck('product_id')
            ->toArray();

        return response()->json([
            'success' => true,
            'wishlist_ids' => $wishlistIds
        ]);
    }
}
