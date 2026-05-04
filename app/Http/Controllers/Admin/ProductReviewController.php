<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    /**
     * Display a listing of the reviews.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $reviews = ProductReview::with(['user', 'product'])->latest()->paginate(20);
        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Toggle the approval status of a review.
     *
     * @param  \App\Models\ProductReview  $review
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleApproval(ProductReview $review)
    {
        $review->is_approved = !$review->is_approved;
        $review->save();

        $status = $review->is_approved ? 'approved' : 'unapproved';
        return back()->with('success', "Review has been {$status} successfully.");
    }

    /**
     * Remove the specified review from storage.
     *
     * @param  \App\Models\ProductReview  $review
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ProductReview $review)
    {
        $review->delete();
        return back()->with('success', 'Review has been deleted successfully.');
    }
}
