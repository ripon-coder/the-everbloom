<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderTracking;
use App\Models\Payment;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear existing data
        Order::query()->delete();
        OrderProduct::query()->delete();
        Payment::query()->delete();
        OrderTracking::query()->delete();
        
        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Get or create test users
        $users = $this->getOrCreateTestUsers();
        
        // Get products
        $products = Product::all();
        
        if ($products->count() === 0) {
            $this->command->warn('No products found. Please run ProductSeeder first.');
            return;
        }

        // Create sample orders
        $orders = [];
        $orderStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        $paymentStatuses = ['pending', 'paid', 'failed', 'refunded', 'partially_paid'];
        $paymentMethods = ['credit_card', 'paypal', 'bank_transfer', 'cash_on_delivery'];
        $carriers = ['fedex', 'ups', 'dhl', 'usps'];

        for ($i = 1; $i <= 20; $i++) {
            $user = $users->random();
            $status = $orderStatuses[array_rand($orderStatuses)];
            $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
            
            // Generate order data
            $orderData = [
                'user_id' => $user->id,
                'order_number' => Order::generateOrderNumber(),
                'subtotal' => 0,
                'coupon_discount_amount' => rand(0, 50),
                'tax_amount' => rand(5, 30),
                'shipping_amount' => rand(0, 20),
                'total_amount' => 0,
                'status' => $status,
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentMethod,
                'payment_account' => "0454567418574",
                'notes' => $this->generateOrderNotes($status),
                'created_at' => now()->subDays(rand(1, 60)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ];

            // Select random products for the order
            $selectedProducts = $products->random(rand(1, 5));
            $orderProducts = [];
            $subtotal = 0;

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 3);
                $unitPrice = $product->price;
                $totalPrice = $quantity * $unitPrice;
                
                $subtotal += $totalPrice;
                
                $orderProducts[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'buying_price' => $unitPrice * 0.6,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'discount_amount' => 0,
                    'notes' => null,
                ];
            }

            // Calculate totals
            $orderData['subtotal'] = $subtotal;
            $orderData['total_amount'] = $subtotal + $orderData['tax_amount'] + $orderData['shipping_amount'] - $orderData['coupon_discount_amount'];

            // Create the order
            $order = Order::create($orderData);

            // Create order products
            foreach ($orderProducts as $productData) {
                $order->orderProducts()->create($productData);
            }

            // Create payments
            $this->createPaymentsForOrder($order, $paymentStatus, $paymentMethod);

            // Create tracking information
            if (in_array($status, ['shipped', 'delivered'])) {
                $this->createTrackingForOrder($order, $status, $carriers);
            }

            $orders[] = $order;
        }

        $this->command->info(count($orders) . ' orders seeded successfully!');
    }

    /**
     * Get or create test users.
     */
    private function getOrCreateTestUsers()
    {
        $users = User::limit(10)->get();
        
        if ($users->count() < 5) {
            // Create additional test users if needed
            for ($i = 1; $i <= 10; $i++) {
                User::firstOrCreate([
                    'email' => "user{$i}@example.com",
                ], [
                    'name' => "Test User {$i}",
                    'password' => bcrypt('password'),
                ]);
            }
            
            $users = User::limit(10)->get();
        }
        
        return $users;
    }

    /**
     * Generate a sample address.
     */
    private function generateAddress(): array
    {
        $addresses = [
            [
                'name' => 'John Doe',
                'address' => '123 Main Street',
                'city' => 'New York',
                'state' => 'NY',
                'zip' => '10001',
                'country' => 'United States',
                'phone' => '+1 (555) 123-4567',
            ],
            [
                'name' => 'Jane Smith',
                'address' => '456 Oak Avenue',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'zip' => '90210',
                'country' => 'United States',
                'phone' => '+1 (555) 987-6543',
            ],
            [
                'name' => 'Bob Johnson',
                'address' => '789 Pine Road',
                'city' => 'Chicago',
                'state' => 'IL',
                'zip' => '60601',
                'country' => 'United States',
                'phone' => '+1 (555) 456-7890',
            ],
        ];

        return $addresses[array_rand($addresses)];
    }

    /**
     * Generate order notes based on status.
     */
    private function generateOrderNotes(string $status): ?string
    {
        $notes = [
            'pending' => 'Order received and awaiting processing.',
            'processing' => 'Order is being prepared for shipment.',
            'shipped' => 'Order has been shipped and is on its way.',
            'delivered' => 'Order has been successfully delivered.',
            'cancelled' => 'Order was cancelled by customer request.',
        ];

        return $notes[$status] ?? null;
    }

    /**
     * Create payments for an order.
     */
    private function createPaymentsForOrder(Order $order, string $paymentStatus, string $paymentMethod): void
    {
        $paymentCount = $paymentStatus === 'partially_paid' ? 2 : 1;
        $remainingAmount = $order->total_amount;
        
        for ($i = 0; $i < $paymentCount; $i++) {
            $amount = $i === $paymentCount - 1 ? $remainingAmount : $order->total_amount / 2;
            $remainingAmount -= $amount;
            
            $paymentStatusOptions = match($paymentStatus) {
                'paid' => 'completed',
                'failed' => 'failed',
                'refunded' => 'refunded',
                'partially_paid' => $i === 0 ? 'completed' : 'pending',
                default => 'pending',
            };
            
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $paymentMethod,
                'transaction_id' => 'TXN' . strtoupper(uniqid()) . rand(1000, 9999),
                'amount' => $amount,
                'currency' => 'USD',
                'status' => $paymentStatusOptions,
                'payment_details' => [
                    'gateway' => $paymentMethod,
                    'card_type' => in_array($paymentMethod, ['credit_card']) ? 'visa' : null,
                    'last_four' => in_array($paymentMethod, ['credit_card']) ? rand(1000, 9999) : null,
                ],
                'paid_at' => in_array($paymentStatusOptions, ['completed', 'refunded']) ? now()->subDays(rand(1, 30)) : null,
                'refunded_at' => $paymentStatusOptions === 'refunded' ? now()->subDays(rand(1, 15)) : null,
                'failure_reason' => $paymentStatusOptions === 'failed' ? 'Insufficient funds' : null,
                'notes' => null,
            ]);
        }
    }

    /**
     * Create tracking for an order.
     */
    private function createTrackingForOrder(Order $order, string $orderStatus, array $carriers): void
    {
        $carrier = $carriers[array_rand($carriers)];
        $trackingNumber = $this->generateTrackingNumber($carrier);
        
        $trackingStatuses = match($orderStatus) {
            'shipped' => ['shipped', 'in_transit'],
            'delivered' => ['shipped', 'in_transit', 'out_for_delivery', 'delivered'],
            default => [],
        };

        $estimatedDelivery = now()->addDays(rand(2, 7));
        $actualDelivery = $orderStatus === 'delivered' ? $estimatedDelivery->subDays(rand(0, 2)) : null;

        foreach ($trackingStatuses as $index => $status) {
            $createdAt = now()->subDays(count($trackingStatuses) - $index);
            
            OrderTracking::create([
                'order_id' => $order->id,
                'status' => $status,
                'location' => $this->generateTrackingLocation($status),
                'description' => $this->generateTrackingDescription($status),
                'tracking_number' => $trackingNumber,
                'carrier' => $carrier,
                'estimated_delivery' => $estimatedDelivery,
                'actual_delivery' => $status === 'delivered' ? $actualDelivery : null,
                'tracking_details' => [
                    'checkpoint' => $status,
                    'timestamp' => $createdAt->toISOString(),
                ],
                'notes' => null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }

    /**
     * Generate a tracking number based on carrier.
     */
    private function generateTrackingNumber(string $carrier): string
    {
        return match($carrier) {
            'fedex' => rand(1000000000, 9999999999),
            'ups' => '1Z' . rand(1000000000, 9999999999),
            'dhl' => rand(1000000000, 9999999999),
            'usps' => rand(1000000000, 9999999999),
            default => rand(1000000000, 9999999999),
        };
    }

    /**
     * Generate tracking location based on status.
     */
    private function generateTrackingLocation(string $status): string
    {
        $locations = [
            'shipped' => 'Warehouse Facility',
            'in_transit' => 'Distribution Center',
            'out_for_delivery' => 'Local Delivery Facility',
            'delivered' => 'Customer Address',
        ];

        return $locations[$status] ?? 'Unknown';
    }

    /**
     * Generate tracking description based on status.
     */
    private function generateTrackingDescription(string $status): string
    {
        $descriptions = [
            'shipped' => 'Package has been picked up by carrier',
            'in_transit' => 'Package is in transit to destination',
            'out_for_delivery' => 'Package is out for delivery',
            'delivered' => 'Package has been delivered successfully',
        ];

        return $descriptions[$status] ?? 'Status updated';
    }
}
