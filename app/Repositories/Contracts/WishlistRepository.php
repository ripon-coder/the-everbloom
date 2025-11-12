<?php

namespace App\Repositories\Contracts;

interface WishlistRepository {
    public function ProductExistCheck($product_id);
    public function ExistCheck($user_id,$product_id);
    public function AddWishlist($user_id,$product_id);
    public function GetWishlist($user_id,$data);
    public function DeleteWishlist($user_id,$wishlist_id);
    public function DeleteWishlistByProduct($user_id,$product_id);
}
