<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create specific sample products
        $products = [
            [
                'name' => 'Wireless Bluetooth Headphones',
                'description' => 'High-quality wireless headphones with noise cancellation and 30-hour battery life. Perfect for music lovers and professionals.',
                'price' => 199.99,
                'sku' => 'WBH-001',
                'slug' => 'wireless-bluetooth-headphones',
                'is_active' => true,
            ],
            [
                'name' => 'Smart Fitness Watch',
                'description' => 'Advanced fitness tracking with heart rate monitor, GPS, and waterproof design. Track your health goals effectively.',
                'price' => 299.99,
                'sku' => 'SFW-002',
                'slug' => 'smart-fitness-watch',
                'is_active' => true,
            ],
            [
                'name' => 'Portable Laptop Stand',
                'description' => 'Ergonomic aluminum laptop stand that is adjustable and portable. Improve your workspace comfort and posture.',
                'price' => 49.99,
                'sku' => 'PLS-003',
                'slug' => 'portable-laptop-stand',
                'is_active' => true,
            ],
            [
                'name' => 'USB-C Hub Multi-Port Adapter',
                'description' => '7-in-1 USB-C hub with HDMI, USB 3.0, SD card reader, and power delivery. Essential for modern laptops.',
                'price' => 79.99,
                'sku' => 'UCH-004',
                'slug' => 'usb-c-hub-multi-port-adapter',
                'is_active' => true,
            ],
            [
                'name' => 'Wireless Charging Pad',
                'description' => 'Fast wireless charging pad compatible with all Qi-enabled devices. Sleek design with LED indicator.',
                'price' => 39.99,
                'sku' => 'WCP-005',
                'slug' => 'wireless-charging-pad',
                'is_active' => true,
            ],
            [
                'name' => 'Bluetooth Mechanical Keyboard',
                'description' => 'Premium mechanical keyboard with blue switches, RGB backlighting, and wireless connectivity. Perfect for gaming and typing.',
                'price' => 149.99,
                'sku' => 'BMK-006',
                'slug' => 'bluetooth-mechanical-keyboard',
                'is_active' => true,
            ],
            [
                'name' => '4K Webcam',
                'description' => 'Ultra HD webcam with auto-focus and built-in microphone. Ideal for video calls and content creation.',
                'price' => 129.99,
                'sku' => '4KW-007',
                'slug' => '4k-webcam',
                'is_active' => true,
            ],
            [
                'name' => 'Smartphone Camera Lens Kit',
                'description' => 'Professional camera lens kit for smartphones including wide-angle, macro, and fisheye lenses.',
                'price' => 89.99,
                'sku' => 'SCL-008',
                'slug' => 'smartphone-camera-lens-kit',
                'is_active' => true,
            ],
            [
                'name' => 'Wireless Gaming Mouse',
                'description' => 'High-precision gaming mouse with customizable RGB lighting and programmable buttons. Ergonomic design for long gaming sessions.',
                'price' => 79.99,
                'sku' => 'WGM-009',
                'slug' => 'wireless-gaming-mouse',
                'is_active' => true,
            ],
            [
                'name' => 'Portable Power Bank',
                'description' => '20000mAh power bank with fast charging and multiple USB ports. Keep your devices charged on the go.',
                'price' => 59.99,
                'sku' => 'PPB-010',
                'slug' => 'portable-power-bank',
                'is_active' => true,
            ],
        ];

        // Insert the specific products
        foreach ($products as $product) {
            Product::create($product);
        }

        // Create additional random products using the factory
        Product::factory()->count(20)->create();
        
        // Create some inactive products
        Product::factory()->count(5)->inactive()->create();
        
        // Create some premium products
        Product::factory()->count(10)->priceRange(500, 2000)->active()->create();
        
        // Create some budget products
        Product::factory()->count(15)->priceRange(10, 50)->active()->create();
    }
}
