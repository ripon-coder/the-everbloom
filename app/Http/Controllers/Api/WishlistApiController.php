<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\WishlistResource;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Contracts\WishlistRepository;


class WishlistApiController extends BaseApiController
{
    private $wishlistRepository;
    public function __construct(WishlistRepository $wishlistRepository)
    {
        $this->wishlistRepository = $wishlistRepository;
    }
    public function AddWishlist(Request $request)
    {
        $user_id = auth()->guard('sanctum')->id();
        $productExists = $this->wishlistRepository->ProductExistCheck($request->product_id);
        if (!$productExists) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ]);
        }
        $exists = $this->wishlistRepository->ExistCheck($user_id, $request->product_id);
        if ($exists) {
            $this->wishlistRepository->DeleteWishlistByProduct($user_id, $request->product_id);
            return response()->json([
                'success' => true,
                'message' => 'Wishlist removed successfully',
            ]);
        }

        $this->wishlistRepository->AddWishlist($user_id, $request->product_id);

        return response()->json([
            'success' => true,
            'message' => 'Wishlist added successfully',
        ]);
    }

    public function GetWishlist(Request $request)
    {
        $user_id = auth()->guard('sanctum')->id();
        $Wishlist = $this->wishlistRepository->GetWishlist($user_id, $request->all());
        $data['wishlist'] = WishlistResource::collection($Wishlist['wishlist']);
        $data['total'] = $Wishlist['total'];
        return $this->successResponse($data, 'Wishlist fetched successfully');
    }
    public function DeleteWishlist(Request $request)
    {
        $user_id = auth()->guard('sanctum')->id();
        $Wishlist = $this->wishlistRepository->DeleteWishlist($user_id, $request->wishlist_id);
        return $this->successResponse($Wishlist, 'Wishlist deleted successfully');
    }
}
