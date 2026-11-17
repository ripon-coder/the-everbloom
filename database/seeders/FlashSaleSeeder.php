<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class FlashSaleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Schedule an active Flash Sale window in storefront settings
        Setting::updateOrCreate(
            [
                'group' => 'storefront',
                'key' => 'flash_sale',
            ],
            [
                'value' => [
                    'enabled' => true,
                    'starts_at' => now()->subHour()->toIso8601String(),
                    'ends_at' => now()->addDays(3)->toIso8601String(),
                ],
            ]
        );

        // 2. Ensure at least 4 active discounted products have distinct boutique brands
        $brands = [
            "Maison de L'Aube",
            'Terra Apothecary',
            'Soleil Beauty',
            'Luna Skincare',
            'Aura Botanicals',
            'Kōan Rituals',
            'Verve Botanica',
        ];

        $discountedProducts = Product::where('is_active', true)
            ->whereNotNull('compare_at_price')
            ->whereColumn('compare_at_price', '>', 'price')
            ->orderBy('id')
            ->get();

        foreach ($discountedProducts as $index => $product) {
            $product->brand = $brands[$index % count($brands)];
            $product->saveQuietly();
        }
    }
}

