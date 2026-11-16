<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredProducts = Product::with(['categoryRelation', 'productVariants'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('id')
            ->take(4)
            ->get()
            ->map(fn (Product $p) => $p->toStorefrontArray())
            ->values()
            ->all();

        $featuredIds = collect($featuredProducts)->pluck('id')->all();

        $bestsellerProducts = Product::with(['categoryRelation', 'productVariants'])
            ->where('is_active', true)
            ->where('best_seller', true)
            ->whereNotIn('id', $featuredIds)
            ->orderBy('id')
            ->take(4)
            ->get()
            ->map(fn (Product $p) => $p->toStorefrontArray())
            ->values()
            ->all();

        if (empty($bestsellerProducts)) {
            $bestsellerProducts = Product::with(['categoryRelation', 'productVariants'])
                ->where('is_active', true)
                ->whereNotIn('id', $featuredIds)
                ->orderByDesc('review_count')
                ->take(4)
                ->get()
                ->map(fn (Product $p) => $p->toStorefrontArray())
                ->values()
                ->all();
        }

        $categories = Category::where('is_active', true)
            ->withCount(['products as item_count' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->get()
            ->toArray();

        $testimonials = Review::with('product')
            ->where('status', 'Approved')
            ->where('is_featured_testimonial', true)
            ->latest()
            ->take(3)
            ->get()
            ->map(fn (Review $r) => [
                'id' => $r->id,
                'author' => $r->author,
                'location' => $r->location ?? 'Verified Ritual',
                'product' => $r->product?->name ?? 'Botanical Collection',
                'rating' => (int) $r->rating,
                'quote' => $r->body,
                'date' => $r->date,
                'verified' => (bool) $r->verified,
            ])
            ->all();

        return view('frontend.home', [
            'featuredProducts' => $featuredProducts,
            'bestsellerProducts' => $bestsellerProducts,
            'categories' => $categories,
            'testimonials' => $testimonials,
        ]);
    }
}
