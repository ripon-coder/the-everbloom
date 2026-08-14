<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    /**
     * Store a newly created review in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|max:1000',
        ]);

        // Check if user has already reviewed this product
        $existingReview = ProductReview::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($existingReview) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already submitted a review for this product.'
                ], 422);
            }
            return back()->with('error', 'You have already submitted a review for this product.');
        }

        ProductReview::create([
            'user_id'     => Auth::id(),
            'product_id'  => $request->product_id,
            'rating'      => $request->rating,
            'review'      => $request->review,
            'is_approved' => false, // Requires admin approval before publishing
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you for your review! It will be visible after admin approval.'
            ]);
        }

        return back()->with('success', 'Thank you for your review! It will be visible after admin approval.');
    }
}
