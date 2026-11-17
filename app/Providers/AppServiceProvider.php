<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\Setting;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Password::defaults(function () {
            return Password::min(8)->letters()->numbers();
        });

        RateLimiter::for('login', function (Request $request) {
            $identifier = strtolower(trim((string) ($request->input('email') ?: $request->input('phone') ?: 'guest')));

            return Limit::perMinute(5)->by($identifier . '|' . $request->ip());
        });

        RateLimiter::for('password-reset', function (Request $request) {
            $email = strtolower(trim((string) $request->input('email', 'guest')));

            return Limit::perMinute(5)->by($email . '|' . $request->ip());
        });

        try {
            if (Schema::hasTable('settings')) {
                $siteSettings = Cache::rememberForever('site_settings', function () {
                    return Setting::where('key', 'site')->first()?->value;
                });

                if (is_array($siteSettings) && !empty($siteSettings)) {
                    config(['demo.site' => array_replace_recursive(config('demo.site', []), $siteSettings)]);
                    if (! empty($siteSettings['timezone']) && in_array($siteSettings['timezone'], timezone_identifiers_list(), true)) {
                        config(['app.timezone' => $siteSettings['timezone']]);
                        @date_default_timezone_set($siteSettings['timezone']);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Safe fallback before database/migrations are initialized
        }

        $currencySymbol = config('demo.site.currency.symbol', '$');
        $currencyCode = config('demo.site.currency.code', 'USD');
        View::share('currencySymbol', $currencySymbol);
        View::share('currencyCode', $currencyCode);

        View::composer('components.admin.topbar', function ($view) {
            try {
                $pendingOrders = Schema::hasTable('orders')
                    ? Order::whereIn('status', ['Placed', 'Processing'])->latest('placed_at')->take(3)->get(['id', 'order_number', 'total', 'placed_at'])
                    : collect();
                $pendingReviews = Schema::hasTable('reviews')
                    ? Review::with('product:id,name')->where('status', 'Pending')->latest()->take(2)->get(['id', 'product_id', 'author'])
                    : collect();
                $lowStockItems = Schema::hasTable('products')
                    ? Product::whereColumn('stock_count', '<=', 'min_threshold')->orderBy('stock_count')->take(2)->get(['id', 'name', 'stock_count'])
                    : collect();

                $view->with([
                    'adminNotifOrders' => $pendingOrders,
                    'adminNotifReviews' => $pendingReviews,
                    'adminNotifLowStock' => $lowStockItems,
                    'adminNotifCount' => $pendingOrders->count() + $pendingReviews->count() + $lowStockItems->count(),
                ]);
            } catch (\Throwable) {
                $view->with([
                    'adminNotifOrders' => collect(),
                    'adminNotifReviews' => collect(),
                    'adminNotifLowStock' => collect(),
                    'adminNotifCount' => 0,
                ]);
            }
        });

        View::composer(['components.storefront.navigation', 'components.storefront.mobile-navigation', 'components.storefront.footer'], function ($view) {
            try {
                if (Schema::hasTable('categories')) {
                    $dbCategories = Cache::remember('storefront_categories_nav', 3600, function () {
                        return Category::where('is_active', true)
                            ->orderBy('sort_order')
                            ->orderBy('id')
                            ->get(['id', 'parent_id', 'name', 'short_name', 'slug'])
                            ->map(fn (Category $c) => [
                                'id' => $c->id,
                                'parent_id' => $c->parent_id,
                                'name' => $c->name,
                                'short_name' => $c->short_name ?: $c->name,
                                'slug' => $c->slug,
                            ])
                            ->all();
                    });

                    if (! empty($dbCategories)) {
                        $topCategories = array_values(array_filter($dbCategories, fn ($c) => $c['parent_id'] === null));
                        if (empty($topCategories)) {
                            $topCategories = $dbCategories;
                        }

                        $mainItems = array_merge(
                            [['label' => 'Shop All', 'url' => '/shop']],
                            array_map(fn ($c) => [
                                'label' => $c['short_name'],
                                'url' => '/shop/category/' . $c['slug'],
                            ], array_slice($topCategories, 0, 5)),
                            [
                                ['label' => 'About', 'url' => '/about'],
                                ['label' => 'Contact', 'url' => '/contact'],
                            ]
                        );

                        $categoryLinks = array_map(fn ($c) => [
                            'label' => $c['name'],
                            'url' => '/shop/category/' . $c['slug'],
                        ], $topCategories);

                        $view->with('dbNavItems', $mainItems);
                        $view->with('dbNavCategories', $categoryLinks);
                    }
                }
            } catch (\Throwable $e) {
                // Safe fallback to config/demo/navigation
            }
        });

        View::composer(['components.storefront.cart-drawer', 'components.storefront.search-modal', 'frontend.cart.index', 'layouts.checkout'], function ($view) {
            try {
                if (Schema::hasTable('products')) {
                    $products = Cache::remember('storefront_catalog_json', 3600, function () {
                        return Product::with(['categoryRelation', 'productVariants'])
                            ->where('is_active', true)
                            ->orderBy('id')
                            ->get()
                            ->map(fn (Product $product) => $product->toStorefrontArray())
                            ->values()
                            ->all();
                    });
                    $view->with('storefrontProductsJson', $products);
                }

                if (Schema::hasTable('coupons')) {
                    $discounts = Cache::remember('storefront_discounts_json', 3600, function () {
                        return Coupon::where('is_active', true)
                            ->where(function ($q) {
                                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                            })
                            ->get()
                            ->mapWithKeys(fn (Coupon $coupon) => [
                                strtoupper($coupon->code) => [
                                    'type' => $coupon->type,
                                    'value' => (float) $coupon->value,
                                    'label' => $coupon->label,
                                    'min_order_amount' => (float) $coupon->min_order_amount,
                                ],
                            ])
                            ->all();
                    });
                    $view->with('storefrontDiscountsJson', $discounts);
                }
            } catch (\Throwable $e) {
                // Safe fallback to config/demo/*
            }
        });
    }
}
