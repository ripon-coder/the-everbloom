<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Constants\ProductStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing products
        Product::query()->delete();

        // Get categories and brands
        $categories = Category::all();
        $brands = Brand::all();

        if ($categories->count() === 0) {
            $this->command->warn('No categories found. Please run CategorySeeder first.');
            return;
        }

        if ($brands->count() === 0) {
            $this->command->warn('No brands found. Please run BrandSeeder first.');
            return;
        }

        // Sample products data
        $products = [
            [
                'name' => 'Wireless Bluetooth Headphones',
                'description' => 'High-quality wireless headphones with noise cancellation and 30-hour battery life.',
                'price' => 99.99,
                'status' => ProductStatus::ACTIVE,
            ],
            [
                'name' => 'Smartphone Case',
                'description' => 'Durable protective case for smartphones with shock absorption.',
                'price' => 19.99,
                'status' => ProductStatus::ACTIVE,
            ],
            [
                'name' => 'USB-C Charging Cable',
                'description' => 'Fast charging USB-C cable, 6 feet length, compatible with most devices.',
                'price' => 14.99,
                'status' => ProductStatus::ACTIVE,
            ],
            [
                'name' => 'Wireless Mouse',
                'description' => 'Ergonomic wireless mouse with precision tracking and long battery life.',
                'price' => 29.99,
                'status' => ProductStatus::ACTIVE,
            ],
            [
                'name' => 'Laptop Stand',
                'description' => 'Adjustable aluminum laptop stand for better ergonomics.',
                'price' => 39.99,
                'status' => ProductStatus::ACTIVE,
            ],
            [
                'name' => 'Power Bank 10000mAh',
                'description' => 'Portable power bank with fast charging and multiple USB ports.',
                'price' => 34.99,
                'status' => ProductStatus::ACTIVE,
            ],
            [
                'name' => 'Bluetooth Speaker',
                'description' => 'Waterproof Bluetooth speaker with 360-degree sound.',
                'price' => 49.99,
                'status' => ProductStatus::ACTIVE,
            ],
            [
                'name' => 'Phone Screen Protector',
                'description' => 'Tempered glass screen protector with easy installation.',
                'price' => 9.99,
                'status' => ProductStatus::ACTIVE,
            ],
            [
                'name' => 'Wireless Charger',
                'description' => 'Fast wireless charging pad compatible with Qi-enabled devices.',
                'price' => 24.99,
                'status' => ProductStatus::ACTIVE,
            ],
            [
                'name' => 'Gaming Keyboard',
                'description' => 'Mechanical gaming keyboard with RGB backlighting.',
                'price' => 79.99,
                'status' => ProductStatus::ACTIVE,
            ],
            [
                'name' => 'Webcam HD 1080p',
                'description' => 'High-definition webcam with built-in microphone.',
                'price' => 44.99,
                'status' => ProductStatus::ACTIVE,
            ],
            [
                'name' => 'Tablet Stylus Pen',
                'description' => 'Precision stylus pen compatible with most touchscreen devices.',
                'price' => 19.99,
                'status' => ProductStatus::ACTIVE,
            ],
        ];

        // Create products
        foreach ($products as $productData) {
            $category = $categories->random();
            $brand = $brands->random();
            
            $product = Product::create([
                "admin_id" =>1,
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']),
                'description' => $productData['description'],
                'price' => $productData['price'],
                'status' => $productData['status'],
                'category_id' => $category->id,
                'brand_id' => $brand->id,
            ]);

            // Add some product images (placeholder) - matching actual table structure
            $product->images()->create([
                'is_default' => true,
            ]);
        }

        $this->command->info(count($products) . ' products seeded successfully!');
    }
}
