<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class AdminReviewController extends Controller
{
    public function index(Request $request): View
    {
        $statusFilter = $request->string('status')->toString();
        $ratingFilter = $request->integer('rating');
        $search = trim($request->string('search')->toString());

        $reviews = Review::with(['product', 'user'])
            ->when($statusFilter !== '' && $statusFilter !== 'all', fn ($q) => $q->where('status', $statusFilter))
            ->when($ratingFilter >= 1 && $ratingFilter <= 5, fn ($q) => $q->where('rating', $ratingFilter))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('author', 'like', "%{$search}%")
                        ->orWhere('headline', 'like', "%{$search}%")
                        ->orWhere('body', 'like', "%{$search}%")
                        ->orWhereHas('product', fn ($p) => $p->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.reviews.index', compact('reviews', 'statusFilter', 'ratingFilter', 'search'));
    }

    public function toggleStatus(Request $request, $id): RedirectResponse
    {
        $review = Review::with('product')->findOrFail($id);
        $newStatus = $review->status === Review::STATUS_APPROVED
            ? Review::STATUS_PENDING
            : Review::STATUS_APPROVED;

        $review->update(['status' => $newStatus]);
        $this->recalculateProductRating($review->product);

        return redirect()->back()->with('success', 'Review status updated.');
    }

    public function approve($id): RedirectResponse
    {
        $review = Review::with('product')->findOrFail($id);
        $review->update(['status' => Review::STATUS_APPROVED]);
        $this->recalculateProductRating($review->product);

        return redirect()->back()->with('success', 'Review approved.');
    }

    public function reject($id): RedirectResponse
    {
        $review = Review::with('product')->findOrFail($id);
        $review->update(['status' => Review::STATUS_REJECTED]);
        $this->recalculateProductRating($review->product);

        return redirect()->back()->with('success', 'Review rejected.');
    }

    public function toggleFeatured($id): RedirectResponse
    {
        $review = Review::findOrFail($id);
        $review->update([
            'is_featured_testimonial' => ! $review->is_featured_testimonial,
        ]);

        return redirect()->back()->with('success', $review->is_featured_testimonial ? 'Marked as featured testimonial.' : 'Removed from featured testimonials.');
    }

    public function reply(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'admin_reply' => ['nullable', 'string', 'max:2000'],
        ]);

        $review = Review::findOrFail($id);
        $replyText = trim((string) ($validated['admin_reply'] ?? ''));

        $review->update([
            'admin_reply' => $replyText !== '' ? $replyText : null,
            'replied_at' => $replyText !== '' ? now() : null,
        ]);

        return redirect()->back()->with('success', 'Review reply saved.');
    }

    public function destroy($id): RedirectResponse
    {
        $review = Review::with('product')->findOrFail($id);
        $product = $review->product;
        $review->delete();
        $this->recalculateProductRating($product);

        return redirect()->back()->with('success', 'Review deleted.');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:approve,reject,delete'],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:reviews,id'],
        ]);

        $reviews = Review::with('product')->whereIn('id', $validated['ids'])->get();
        $products = $reviews->pluck('product')->filter()->unique('id');

        if ($validated['action'] === 'approve') {
            Review::whereIn('id', $validated['ids'])->update(['status' => Review::STATUS_APPROVED]);
            $msg = 'Selected reviews approved.';
        } elseif ($validated['action'] === 'reject') {
            Review::whereIn('id', $validated['ids'])->update(['status' => Review::STATUS_REJECTED]);
            $msg = 'Selected reviews rejected.';
        } else {
            Review::whereIn('id', $validated['ids'])->delete();
            $msg = 'Selected reviews deleted.';
        }

        foreach ($products as $product) {
            $this->recalculateProductRating($product);
        }

        return redirect()->back()->with('success', $msg);
    }

    private function recalculateProductRating(?Product $product): void
    {
        if (! $product) {
            return;
        }

        $approved = $product->reviews()->where('status', Review::STATUS_APPROVED);
        $count = $approved->count();
        $avg = $count > 0 ? round((float) $approved->avg('rating'), 2) : 5.00;

        $product->update([
            'review_count' => $count,
            'rating' => $avg,
        ]);

        Cache::forget('storefront_catalog_json');
    }
}
