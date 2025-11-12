<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Models\Wishlist;
use App\Http\Resources\WishlistResource;
use App\Repositories\Contracts\WishlistRepository;

class WishlistEloquent implements WishlistRepository
{

    public function ProductExistCheck($product_id)
    {
        return Product::active()->where('id', $product_id)->exists();
    }

    public function ExistCheck($user_id, $product_id)
    {
        return Wishlist::where('user_id', $user_id)->where('product_id', $product_id)->exists();
    }
    public function AddWishlist($user_id, $product_id)
    {
        return Wishlist::create([
            'user_id' => $user_id,
            'product_id' => $product_id
        ]);
    }
    public function GetWishlist($user_id, $data)
    {
        $current_page = $data['current_page'] ?? 1;
        $per_page = $data['per_page'] ?? 10;
        $offset = ($current_page - 1) * $per_page;

        $baseQuery = Wishlist::where('user_id', $user_id);

        $total = (clone $baseQuery)->count();

        $Wishlist = (clone $baseQuery)
            ->select([
                'id',
                'user_id',
                'product_id',
                'created_at',
            ])
            ->with(['product:id,name,slug,price', 'product.firstImage'])
            ->latest()
            ->skip($offset)
            ->take($per_page)
            ->get();

        return [
            'wishlist' => $Wishlist,
            'total' => $total,
        ];
    }
    public function DeleteWishlist($user_id, $wishlist_id)
    {
        return Wishlist::where('user_id', $user_id)->where('id',$wishlist_id)->delete();
    }

    public function DeleteWishlistByProduct($user_id, $product_id)
    {
        return Wishlist::where('user_id', $user_id)->where('product_id',$product_id)->delete();
    }
}
