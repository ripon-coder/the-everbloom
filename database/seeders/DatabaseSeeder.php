<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Admin;
use App\Models\Area;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\District;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\PurchaseBatch;
use App\Models\PurchaseBatchItem;
use App\Models\Review;
use App\Models\Setting;
use App\Models\Thana;
use App\Models\User;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (app()->isProduction()) {
            throw new \RuntimeException('DatabaseSeeder cannot run in production. Use ProductionSeeder instead.');
        }

        // 1. Default Admin
        Admin::updateOrCreate(
            ['email' => 'admin@the-everbloom.com'],
            [
                'name' => 'Clara Vance (Apothecary Director)',
                'password' => Hash::make('password'),
                'avatar' => 'admin/assets/images/users/avatar-1.jpg',
                'avatar_source' => 'local',
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );

        // 2. Demo Customers
        $customersData = [
            [
                'first_name' => 'Eleanor',
                'last_name' => 'Vance',
                'email' => 'eleanor.vance@example.com',
                'phone' => '+1 (503) 555-0192',
                'avatar' => 'admin/assets/images/users/avatar-2.jpg',
                'status' => 'Active',
                'created_at' => Carbon::parse('2025-10-14 10:00:00'),
            ],
            [
                'first_name' => 'Sophia',
                'last_name' => 'Laurent',
                'email' => 'sophia.laurent@example.com',
                'phone' => '+1 (415) 555-0811',
                'avatar' => 'admin/assets/images/users/avatar-3.jpg',
                'status' => 'Active',
                'created_at' => Carbon::parse('2025-07-22 10:00:00'),
            ],
            [
                'first_name' => 'Emma',
                'last_name' => 'Chen',
                'email' => 'emma.chen@example.com',
                'phone' => '+1 (206) 555-0348',
                'avatar' => 'admin/assets/images/users/avatar-4.jpg',
                'status' => 'Active',
                'created_at' => Carbon::parse('2026-01-09 10:00:00'),
            ],
            [
                'first_name' => 'Lucas',
                'last_name' => 'Wright',
                'email' => 'lucas.wright@example.com',
                'phone' => '+1 (312) 555-0729',
                'avatar' => 'admin/assets/images/users/avatar-5.jpg',
                'status' => 'Inactive',
                'created_at' => Carbon::parse('2025-08-30 10:00:00'),
            ],
        ];

        $primaryUser = null;
        foreach ($customersData as $cData) {
            $user = User::updateOrCreate(
                ['email' => $cData['email']],
                [
                    'first_name' => $cData['first_name'],
                    'last_name' => $cData['last_name'],
                    'phone' => $cData['phone'],
                    'avatar' => $cData['avatar'],
                    'avatar_source' => 'local',
                    'status' => $cData['status'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
            $user->created_at = $cData['created_at'];
            $user->save();

            if ($cData['email'] === 'eleanor.vance@example.com') {
                $primaryUser = $user;
            }
        }

        // 3. Categories
        $categoryMap = [];
        foreach (config('demo.categories', []) as $index => $catData) {
            $category = Category::updateOrCreate(
                ['slug' => $catData['slug']],
                [
                    'name' => $catData['name'],
                    'short_name' => $catData['short_name'] ?? null,
                    'tagline' => $catData['tagline'] ?? null,
                    'description' => $catData['description'] ?? null,
                    'image' => $catData['image'] ?? null,
                    'image_source' => 'local',
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );
            $categoryMap[$category->slug] = $category->id;
        }

        // 4. Products, Variants & Product Images
        $productMap = [];
        foreach (config('demo.products', []) as $pIndex => $pData) {
            $catId = $categoryMap[$pData['category_slug'] ?? 'face-care'] ?? reset($categoryMap);
            $sku = 'EB-SKU-' . str_pad((string) ($pData['id'] ?? ($pIndex + 1)), 4, '0', STR_PAD_LEFT);
            $stockStatus = $pData['stock_status'] ?? 'in_stock';
            $stockCount = match ($stockStatus) {
                'out_of_stock' => 0,
                'low_stock' => 6,
                default => 35,
            };

            $product = Product::updateOrCreate(
                ['slug' => $pData['slug']],
                [
                    'category_id' => $catId,
                    'sku' => $sku,
                    'name' => $pData['name'],
                    'subtitle' => $pData['subtitle'] ?? null,
                    'price' => $pData['price'],
                    'compare_at_price' => $pData['compare_at_price'] ?? null,
                    'rating' => $pData['rating'] ?? 5.0,
                    'review_count' => $pData['review_count'] ?? 0,
                    'short_description' => $pData['short_description'] ?? null,
                    'description' => $pData['description'] ?? null,
                    'ingredients' => $pData['ingredients'] ?? null,
                    'key_ingredients' => $pData['key_ingredients'] ?? null,
                    'benefits' => $pData['benefits'] ?? null,
                    'skin_types' => $pData['skin_types'] ?? null,
                    'how_to_use' => $pData['how_to_use'] ?? null,
                    'sizes' => $pData['sizes'] ?? [],
                    'images' => $pData['images'] ?? [],
                    'image_source' => 'local',
                    'stock_count' => $stockCount,
                    'min_threshold' => 10,
                    'stock_status' => $stockStatus,
                    'batch_lot' => 'LOT-2026-B' . (($pIndex % 4) + 1),
                    'last_restocked_at' => now()->subDays(rand(5, 25))->toDateString(),
                    'is_featured' => (bool) ($pData['is_featured'] ?? false),
                    'best_seller' => (bool) ($pData['best_seller'] ?? false),
                    'badge' => $pData['badge'] ?? null,
                    'is_active' => true,
                ]
            );

            $productMap[$product->id] = $product;
            $productMap[$pData['slug']] = $product;

            // Sync Product Images
            $product->productImages()->delete();
            foreach ($pData['images'] ?? [] as $imgIdx => $imgPath) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $imgPath,
                    'image_source' => 'local',
                    'is_primary' => $imgIdx === 0,
                    'sort_order' => $imgIdx,
                ]);
            }

            // Sync Product Variants
            $product->productVariants()->delete();
            foreach ($pData['variants'] ?? [] as $vIdx => $vData) {
                $vStatus = $vData['stock_status'] ?? $stockStatus;
                $vCount = match ($vStatus) {
                    'out_of_stock' => 0,
                    'low_stock' => 5,
                    default => 25,
                };
                ProductVariant::create([
                    'product_id' => $product->id,
                    'size' => $vData['size'],
                    'sku' => $sku . '-V' . ($vIdx + 1),
                    'price' => $vData['price'],
                    'compare_at_price' => $vData['compare_at_price'] ?? null,
                    'stock_count' => $vCount,
                    'stock_status' => $vStatus,
                    'sort_order' => $vIdx,
                ]);
            }
        }

        // 5. Seed Bangladesh Locations (64 Districts -> Thanas -> Areas) & Saved Addresses
        $this->call(BangladeshLocationSeeder::class);

        if ($primaryUser && $primaryUser->addresses()->count() === 0) {
            $dhaka = District::where('slug', 'dhaka')->first();
            $gulshan = $dhaka ? Thana::where('district_id', $dhaka->id)->where('name', 'Gulshan')->first() : null;
            $gulshan2 = $gulshan ? Area::where('thana_id', $gulshan->id)->where('name', 'Gulshan 2')->first() : null;
            $dhanmondi = $dhaka ? Thana::where('district_id', $dhaka->id)->where('name', 'Dhanmondi')->first() : null;
            $dhanmondi27 = $dhanmondi ? Area::where('thana_id', $dhanmondi->id)->where('name', 'Dhanmondi Road 27')->first() : null;

            Address::create([
                'user_id' => $primaryUser->id,
                'is_default' => true,
                'first_name' => 'Eleanor',
                'last_name' => 'Vance',
                'country' => 'Bangladesh',
                'district_id' => $dhaka?->id,
                'thana_id' => $gulshan?->id,
                'area_id' => $gulshan2?->id,
                'district_name' => $dhaka?->name ?? 'Dhaka',
                'thana_name' => $gulshan?->name ?? 'Gulshan',
                'area_name' => $gulshan2?->name ?? 'Gulshan 2',
                'address_line1' => 'House 42, Road 71, Gulshan 2',
                'phone' => '+880 1711-000192',
            ]);

            Address::create([
                'user_id' => $primaryUser->id,
                'is_default' => false,
                'first_name' => 'Eleanor',
                'last_name' => 'Vance',
                'country' => 'Bangladesh',
                'district_id' => $dhaka?->id,
                'thana_id' => $dhanmondi?->id,
                'area_id' => $dhanmondi27?->id,
                'district_name' => $dhaka?->name ?? 'Dhaka',
                'thana_name' => $dhanmondi?->name ?? 'Dhanmondi',
                'area_name' => $dhanmondi27?->name ?? 'Dhanmondi Road 27',
                'address_line1' => 'House 18, Road 27, Dhanmondi',
                'phone' => '+880 1711-000144',
            ]);
        }

        // 6. Demo Coupons
        $siteConfig = config('demo.site', []);
        foreach ($siteConfig['demo_discounts'] ?? [] as $code => $discount) {
            Coupon::updateOrCreate(
                ['code' => strtoupper($code)],
                [
                    'type' => $discount['type'],
                    'value' => $discount['value'],
                    'label' => $discount['label'],
                    'min_order_amount' => 0,
                    'usage_limit' => null,
                    'used_count' => 0,
                    'is_active' => true,
                ]
            );
        }

        // 7. Site Settings
        Setting::updateOrCreate(
            ['key' => 'site'],
            [
                'group' => 'storefront',
                'value' => $siteConfig,
            ]
        );

        // 8. Demo Vendors & Initial Wholesale Purchase Batches
        $vendor = Vendor::firstOrCreate(
            ['name' => 'Pacific Botanical Labs'],
            [
                'company_name' => 'Pacific Botanical Labs Ltd.',
                'phone' => '+880 1711-550100',
                'email' => 'wholesale@pacificbotanicals.example.com',
                'address' => 'Tejgaon Industrial Area, Dhaka',
                'is_active' => true,
            ]
        );
        Vendor::firstOrCreate(
            ['name' => 'Alpine Herbal Extracts'],
            [
                'company_name' => 'Alpine Herbal Extracts Co.',
                'phone' => '+880 1711-550200',
                'email' => 'supply@alpineherbal.example.com',
                'address' => 'Chattogram EPZ, Chattogram',
                'is_active' => true,
            ]
        );
        Vendor::firstOrCreate(
            ['name' => 'Oregon Cold-Press Collective'],
            [
                'company_name' => 'Oregon Cold-Press Collective',
                'phone' => '+1 (503) 555-0199',
                'email' => 'orders@oregoncoldpress.example.com',
                'address' => 'Portland, OR',
                'is_active' => true,
            ]
        );

        if (PurchaseBatch::count() === 0) {
            $batch = PurchaseBatch::create([
                'batch_code' => 'BATCH-2026-0001',
                'vendor_id' => $vendor->id,
                'vendor_name' => $vendor->name,
                'reference_no' => 'PO-2026-INIT',
                'purchased_at' => now()->subDays(15)->toDateString(),
                'total_qty' => 0,
                'total_cost' => 0,
                'notes' => 'Initial botanical apothecary wholesale stock.',
            ]);

            $batchQty = 0;
            $batchCost = 0.0;
            foreach (Product::with('productVariants')->get() as $prod) {
                $unitBuy = round((float) $prod->price * 0.40, 2);
                $qty = max(1, (int) $prod->stock_count);
                $lineCost = round($unitBuy * $qty, 2);

                $prod->update([
                    'avg_buy_price' => $unitBuy,
                    'last_buy_price' => $unitBuy,
                ]);

                foreach ($prod->productVariants as $variant) {
                    $vBuy = round((float) $variant->price * 0.40, 2);
                    $variant->update([
                        'avg_buy_price' => $vBuy,
                        'last_buy_price' => $vBuy,
                    ]);
                }

                PurchaseBatchItem::create([
                    'purchase_batch_id' => $batch->id,
                    'vendor_id' => $vendor->id,
                    'product_id' => $prod->id,
                    'batch_lot' => $prod->batch_lot,
                    'purchased_qty' => $qty,
                    'remaining_qty' => $qty,
                    'unit_buy_price' => $unitBuy,
                    'line_total_cost' => $lineCost,
                ]);

                $batchQty += $qty;
                $batchCost += $lineCost;
            }

            $batch->update([
                'total_qty' => $batchQty,
                'total_cost' => round($batchCost, 2),
            ]);
        }

        // 9. Demo Orders, Order Items & Invoices
        foreach (config('demo.account.orders', []) as $oIdx => $oData) {
            $placedAt = Carbon::parse($oData['iso_date'] ?? '2026-11-01');
            $order = Order::updateOrCreate(
                ['order_number' => $oData['id']],
                [
                    'user_id' => $primaryUser?->id,
                    'customer_email' => $primaryUser?->email ?? 'eleanor.vance@example.com',
                    'customer_phone' => $oData['shipping_address']['phone'] ?? null,
                    'status' => $oData['status'],
                    'current_step' => $oData['current_step'] ?? $oData['status'],
                    'subtotal' => $oData['subtotal'],
                    'discount_amount' => 0,
                    'shipping_cost' => $oData['shipping'] ?? 0,
                    'tax_amount' => $oData['tax'] ?? 0,
                    'total' => $oData['total'],
                    'items_count' => $oData['items_count'] ?? count($oData['items'] ?? []),
                    'delivery_method' => $oData['delivery_method'],
                    'payment_method' => $oData['payment_method'],
                    'payment_status' => 'Paid',
                    'tracking_number' => $oData['tracking_number'] ?? 'Pending assignment',
                    'shipping_address' => $oData['shipping_address'],
                    'placed_at' => $placedAt,
                ]
            );

            if ($order->items()->count() === 0) {
                $sumCost = 0.0;
                foreach ($oData['items'] ?? [] as $itemData) {
                    $prod = $productMap[$itemData['slug']] ?? ($productMap[$itemData['product_id']] ?? null);
                    $unitPrice = (float) $itemData['price'];
                    $qty = (int) $itemData['qty'];
                    $lineTotal = round($unitPrice * $qty, 2);
                    $unitBuy = $prod ? (float) ($prod->avg_buy_price ?: round($unitPrice * 0.40, 2)) : round($unitPrice * 0.40, 2);
                    $lineCost = round($unitBuy * $qty, 2);
                    $unitProfit = round($unitPrice - $unitBuy, 2);
                    $lineProfit = round($lineTotal - $lineCost, 2);
                    $sumCost += $lineCost;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $prod?->id,
                        'name' => $itemData['name'],
                        'slug' => $itemData['slug'],
                        'variant' => $itemData['variant'] ?? null,
                        'image' => $itemData['image'] ?? null,
                        'image_source' => 'local',
                        'qty' => $qty,
                        'price' => $unitPrice,
                        'unit_buy_price' => $unitBuy,
                        'line_total' => $lineTotal,
                        'line_cost_total' => $lineCost,
                        'unit_profit' => $unitProfit,
                        'line_profit' => $lineProfit,
                    ]);
                }

                $order->update([
                    'total_cost' => round($sumCost, 2),
                    'total_profit' => round((float) $order->subtotal - $sumCost, 2),
                ]);
            }

            Invoice::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'invoice_no' => 'INV-2026-' . (1001 + $oIdx),
                    'customer_name' => $primaryUser?->name ?? 'Eleanor Vance',
                    'customer_email' => $primaryUser?->email ?? 'eleanor.vance@example.com',
                    'amount' => $order->total,
                    'status' => 'Paid',
                    'payment_method' => $order->payment_method,
                    'issued_at' => $placedAt,
                ]
            );
        }

        // 9. Reviews & Featured Testimonials
        if (Review::count() === 0) {
            $firstProduct = Product::first();
            if ($firstProduct) {
                foreach (config('demo.reviews', []) as $rev) {
                    $review = Review::create([
                        'product_id' => $firstProduct->id,
                        'user_id' => $primaryUser?->id,
                        'author' => $rev['author'],
                        'variant' => $rev['variant'] ?? null,
                        'rating' => $rev['rating'] ?? 5,
                        'headline' => $rev['headline'],
                        'body' => $rev['body'],
                        'verified' => true,
                        'is_featured_testimonial' => false,
                        'status' => 'Approved',
                    ]);
                    if (!empty($rev['date_iso'])) {
                        $review->created_at = Carbon::parse($rev['date_iso']);
                        $review->save();
                    }
                }
            }

            foreach (config('demo.testimonials', []) as $t) {
                $tProduct = Product::where('name', $t['product'] ?? '')->first() ?? $firstProduct;
                if ($tProduct) {
                    Review::create([
                        'product_id' => $tProduct->id,
                        'author' => $t['author'],
                        'location' => $t['location'] ?? null,
                        'rating' => $t['rating'] ?? 5,
                        'headline' => $tProduct->name,
                        'body' => $t['quote'],
                        'verified' => (bool) ($t['verified'] ?? true),
                        'is_featured_testimonial' => true,
                        'status' => 'Approved',
                    ]);
                }
            }
        }

        // 10. Default Multi-Attribute Variant Definitions (Brand, Size, Color / Vessel, Skin Type)
        if (Attribute::count() === 0) {
            $defaultAttributes = [
                'Brand' => ['The Everbloom', 'Apothecary Reserve', 'Botanical Lab'],
                'Size' => ['15ml / 0.5 fl oz', '30ml / 1.0 fl oz', '50ml / 1.7 fl oz', '100ml / 3.4 fl oz', '200ml / 6.8 fl oz'],
                'Color / Vessel' => ['Amber Glass', 'Frosted Ivory', 'Botanical Green', 'Rose Tint'],
                'Skin Type / Formula' => ['All Skin Types', 'Dry & Sensitive', 'Combination / Oily'],
            ];

            $aIdx = 1;
            foreach ($defaultAttributes as $attrName => $values) {
                $attr = Attribute::create([
                    'name' => $attrName,
                    'slug' => \Illuminate\Support\Str::slug($attrName),
                    'display_type' => 'button',
                    'sort_order' => $aIdx++,
                    'is_active' => true,
                ]);
                foreach ($values as $vIdx => $val) {
                    AttributeValue::create([
                        'attribute_id' => $attr->id,
                        'value' => $val,
                        'slug' => \Illuminate\Support\Str::slug($val),
                        'sort_order' => $vIdx + 1,
                        'is_active' => true,
                    ]);
                }
            }
        }

        // 11. Import products into Scout/TNTSearch if available
        try {
            if (method_exists(Product::class, 'makeAllSearchable')) {
                Artisan::call('scout:import', ['model' => Product::class]);
            }
        } catch (\Throwable $e) {
            // Safe fallback before Scout/TNTSearch is configured
        }
    }
}
