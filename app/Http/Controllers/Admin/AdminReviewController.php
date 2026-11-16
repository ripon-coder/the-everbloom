<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['product', 'user'])
            ->latest()
            ->get()
            ->map(function (Review $r) {
                return [
                    'id' => $r->id,
                    'author' => $r->author,
                    'avatar' => $r->user?->avatar ?? 'admin/assets/images/users/avatar-2.jpg',
                    'product_name' => $r->product?->name ?? 'Botanical Formulation',
                    'rating' => (int) $r->rating,
                    'headline' => $r->headline,
                    'comment' => $r->body,
                    'date' => $r->created_at?->format('M d, Y') ?? '',
                    'status' => $r->status,
                    'status_badge' => $r->status === 'Approved' ? 'success' : ($r->status === 'Pending' ? 'warning' : 'danger'),
                ];
            })
            ->all();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function toggleStatus(Request $request, $id)
    {
        $review = Review::with('product')->findOrFail($id);
        $newStatus = $review->status === 'Approved' ? 'Pending' : 'Approved';
        $review->update(['status' => $newStatus]);

        if ($review->product) {
            $approved = $review->product->reviews()->where('status', 'Approved');
            $count = $approved->count();
            $avg = $count > 0 ? round((float) $approved->avg('rating'), 2) : 5.00;
            $review->product->update([
                'review_count' => $count,
                'rating' => $avg,
            ]);
        }

        return redirect()->back()->with('success', 'Review status updated.');
    }
}
