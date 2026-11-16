<?php

namespace App\Providers;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        try {
            if (Schema::hasTable('settings')) {
                $siteSettings = Cache::rememberForever('site_settings', function () {
                    return Setting::where('key', 'site')->first()?->value;
                });

                if (is_array($siteSettings) && !empty($siteSettings)) {
                    config(['demo.site' => array_replace_recursive(config('demo.site', []), $siteSettings)]);
                }
            }
        } catch (\Throwable $e) {
            // Safe fallback before database/migrations are initialized
        }

        View::composer(['components.storefront.cart-drawer', 'components.storefront.search-modal'], function ($view) {
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
