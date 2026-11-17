<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\Setting;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $flashSaleProducts = Product::with(['categoryRelation', 'productVariants'])
            ->where('is_active', true)
            ->whereNotNull('compare_at_price')
            ->whereColumn('compare_at_price', '>', 'price')
            ->orderBy('id')
            ->take(4)
            ->get()
            ->map(fn (Product $p) => $p->toStorefrontArray())
            ->values()
            ->all();

        $flashSaleIds = collect($flashSaleProducts)->pluck('id')->all();

        $flashSaleSetting = Setting::where('group', 'storefront')
            ->where('key', 'flash_sale')
            ->first()?->value;

        $flashSaleEndsAt = null;
        if (is_array($flashSaleSetting) && !empty($flashSaleSetting['enabled']) && !empty($flashSaleSetting['ends_at'])) {
            try {
                $startsAt = !empty($flashSaleSetting['starts_at']) ? Carbon::parse($flashSaleSetting['starts_at']) : null;
                $endsAt = Carbon::parse($flashSaleSetting['ends_at']);
                if ((!$startsAt || $startsAt->isPast()) && $endsAt->isFuture()) {
                    $flashSaleEndsAt = $endsAt->toIso8601String();
                }
            } catch (\Throwable) {
                $flashSaleEndsAt = null;
            }
        }

        $featuredProducts = Product::with(['categoryRelation', 'productVariants'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('id')
            ->take(4)
            ->get()
            ->map(fn (Product $p) => $p->toStorefrontArray())
            ->values()
            ->all();

        $excludeIds = array_values(array_unique(array_merge($flashSaleIds, collect($featuredProducts)->pluck('id')->all())));

        $bestsellerProducts = Product::with(['categoryRelation', 'productVariants'])
            ->where('is_active', true)
            ->where('best_seller', true)
            ->whereNotIn('id', $excludeIds)
            ->orderBy('id')
            ->take(4)
            ->get()
            ->map(fn (Product $p) => $p->toStorefrontArray())
            ->values()
            ->all();

        if (empty($bestsellerProducts)) {
            $bestsellerProducts = Product::with(['categoryRelation', 'productVariants'])
                ->where('is_active', true)
                ->whereNotIn('id', $flashSaleIds)
                ->orderByDesc('review_count')
                ->take(4)
                ->get()
                ->map(fn (Product $p) => $p->toStorefrontArray())
                ->values()
                ->all();
        }

        $newArrivalsProducts = Product::with(['categoryRelation', 'productVariants'])
            ->where('is_active', true)
            ->latest()
            ->orderByDesc('id')
            ->take(4)
            ->get()
            ->map(fn (Product $p) => $p->toStorefrontArray())
            ->values()
            ->all();

        $categories = Category::where('is_active', true)
            ->withCount(['products as item_count' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->get()
            ->toArray();

        $testimonials = Review::with(['product', 'user'])
            ->where('status', 'Approved')
            ->whereNotNull('product_id')
            ->orderByDesc('is_featured_testimonial')
            ->latest()
            ->take(3)
            ->get()
            ->map(function (Review $r) {
                $avatarPath = $r->user?->avatar;
                $avatarUrl = null;
                if (!empty($avatarPath)) {
                    if (\Illuminate\Support\Str::startsWith($avatarPath, ['http://', 'https://'])) {
                        $avatarUrl = $avatarPath;
                    } elseif (file_exists(public_path(ltrim($avatarPath, '/')))) {
                        $avatarUrl = asset(ltrim($avatarPath, '/'));
                    }
                }

                return [
                    'id' => $r->id,
                    'author' => $r->author,
                    'avatar' => $avatarUrl,
                    'location' => $r->location ?? 'Verified Ritual',
                    'product' => $r->product?->name,
                    'product_slug' => $r->product?->slug,
                    'rating' => (int) $r->rating,
                    'quote' => $r->body,
                    'date' => $r->date,
                    'verified' => (bool) $r->verified,
                ];
            })
            ->all();

        $dbBrands = Brand::active()
            ->withCount(['products as active_products_count' => fn ($q) => $q->where('is_active', true)])
            ->take(6)
            ->get();

        if ($dbBrands->isNotEmpty()) {
            $brands = $dbBrands->map(function (Brand $brand) {
                $logo = $brand->resolvedLogo();
                if ($logo === '') {
                    $firstProd = $brand->products()->where('is_active', true)->first();
                    $images = $firstProd ? $firstProd->resolvedImages() : [];
                    $logo = $images[0] ?? 'products/placeholder.jpg';
                }

                return [
                    'name' => $brand->name,
                    'slug' => $brand->slug,
                    'image' => $logo,
                    'count' => (int) $brand->active_products_count,
                    'url' => route('shop.brand', $brand->slug),
                ];
            })->values()->all();
        } else {
            $brands = Product::where('is_active', true)
                ->whereNotNull('brand')
                ->where('brand', '!=', '')
                ->orderBy('id')
                ->get()
                ->groupBy('brand')
                ->take(6)
                ->map(function ($items, $brandName) {
                    $first = $items->first();
                    $images = $first ? $first->resolvedImages() : [];

                    return [
                        'name' => $brandName,
                        'image' => $images[0] ?? 'products/placeholder.jpg',
                        'count' => $items->count(),
                        'url' => url('/shop?brand=' . urlencode((string) $brandName)),
                    ];
                })
                ->values()
                ->all();
        }

        return view('frontend.home', [
            'flashSaleProducts' => $flashSaleProducts,
            'flashSaleEndsAt' => $flashSaleEndsAt,
            'featuredProducts' => $featuredProducts,
            'bestsellerProducts' => $bestsellerProducts,
            'newArrivalsProducts' => $newArrivalsProducts,
            'categories' => $categories,
            'testimonials' => $testimonials,
            'brands' => $brands,
        ]);
    }
}
