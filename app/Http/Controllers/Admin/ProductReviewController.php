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
    public function index(Request $request)
    {
        $query = ProductReview::with(['user', 'product']);

        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            $query->where(function($q) use ($search) {
                $q->where('review', 'LIKE', "%{$search}%")
                  ->orWhere('id', $search)
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('product', function($p) use ($search) {
                      $p->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('status')) {
            $isApproved = $request->status === 'approved' ? 1 : 0;
            $query->where('is_approved', $isApproved);
        }

        $reviews = $query->latest()->paginate(20)->withQueryString();
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
