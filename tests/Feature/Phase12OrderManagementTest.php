<?php

namespace Tests\Feature;

use App\Mail\OrderStatusChangedMail;
use App\Models\Admin;
use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class Phase12OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_order_view_array_omits_cost_and_profit_keys(): void
    {
        $this->seed();
        $order = Order::with('items')->firstOrFail();

        $customerArray = $order->toViewArray();
        $this->assertArrayNotHasKey('total_cost', $customerArray);
        $this->assertArrayNotHasKey('total_profit', $customerArray);
        $this->assertArrayNotHasKey('unit_buy_price', $customerArray['items'][0]);
        $this->assertArrayNotHasKey('line_profit', $customerArray['items'][0]);

        $adminArray = $order->toAdminArray();
        $this->assertArrayHasKey('total_cost', $adminArray);
        $this->assertArrayHasKey('total_profit', $adminArray);
        $this->assertArrayHasKey('unit_buy_price', $adminArray['items'][0]);
    }

    public function test_order_status_state_machine_history_emails_and_coupon_restore(): void
    {
        Mail::fake();
        $this->seed();

        $admin = Admin::where('email', 'admin@the-everbloom.com')->firstOrFail();
        $product = Product::where('is_active', true)->firstOrFail();
        $coupon = Coupon::where('code', 'WELCOME10')->firstOrFail();
        $coupon->update(['used_count' => 1]);

        $order = Order::create([
            'order_number' => 'EVB-SM-1001',
            'coupon_id' => $coupon->id,
            'customer_email' => 'buyer@example.com',
            'customer_phone' => '+8801711000001',
            'status' => 'Placed',
            'current_step' => 'Placed',
            'subtotal' => 500,
            'total' => 500,
            'delivery_method' => 'Standard Delivery',
            'payment_method' => 'Cash on Delivery (Demo)',
            'payment_status' => 'Pending',
            'shipping_address' => ['first_name' => 'A', 'last_name' => 'B', 'address_line1' => 'Dhaka', 'country' => 'Bangladesh', 'phone' => '+8801711000001'],
            'placed_at' => now(),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'qty' => 1,
            'price' => 500,
            'original_price' => 500,
            'line_total' => 500,
        ]);

        // Invalid jump Placed -> Delivered rejected
        $this->actingAs($admin, 'admin')
            ->from("/admin/orders/{$order->order_number}")
            ->post("/admin/orders/{$order->order_number}/status", ['status' => 'Delivered'])
            ->assertSessionHasErrors('status');

        // Valid Placed -> Processing -> Shipped queues email & records history
        $this->actingAs($admin, 'admin')
            ->post("/admin/orders/{$order->order_number}/status", ['status' => 'Processing'])
            ->assertRedirect();

        $this->actingAs($admin, 'admin')
            ->post("/admin/orders/{$order->order_number}/status", ['status' => 'Shipped', 'tracking_number' => 'TRK-999'])
            ->assertRedirect();

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'from_status' => 'Processing',
            'to_status' => 'Shipped',
        ]);
        Mail::assertQueued(OrderStatusChangedMail::class);

        // Shipped -> Cancelled blocked
        $this->actingAs($admin, 'admin')
            ->post("/admin/orders/{$order->order_number}/status", ['status' => 'Cancelled'])
            ->assertSessionHasErrors('status');

        // Separate Placed order can be Cancelled and restores coupon used_count
        $order2 = Order::create([
            'order_number' => 'EVB-SM-1002',
            'coupon_id' => $coupon->id,
            'customer_email' => 'buyer2@example.com',
            'status' => 'Placed',
            'current_step' => 'Placed',
            'subtotal' => 500,
            'total' => 500,
            'delivery_method' => 'Standard Delivery',
            'payment_method' => 'Cash on Delivery (Demo)',
            'payment_status' => 'Pending',
            'shipping_address' => ['first_name' => 'A', 'last_name' => 'B', 'address_line1' => 'Dhaka', 'country' => 'Bangladesh', 'phone' => '+8801711000001'],
            'placed_at' => now(),
        ]);

        $this->actingAs($admin, 'admin')
            ->post("/admin/orders/{$order2->order_number}/status", ['status' => 'Cancelled'])
            ->assertRedirect();

        $this->assertSame(0, $coupon->fresh()->used_count);
    }

    public function test_admin_orders_filters_notes_real_invoice_no_and_no_hardcoded_labels(): void
    {
        $this->seed();
        $admin = Admin::where('email', 'admin@the-everbloom.com')->firstOrFail();
        $order = Order::with('invoice')->firstOrFail();

        $this->actingAs($admin, 'admin')
            ->get('/admin/orders?search=' . $order->order_number)
            ->assertOk()
            ->assertSee($order->order_number);

        $this->actingAs($admin, 'admin')
            ->post("/admin/orders/{$order->order_number}/notes", [
                'notes' => 'Fragile glass bottle — double wrap.',
            ])
            ->assertRedirect();

        $this->assertSame('Fragile glass bottle — double wrap.', $order->fresh()->notes);

        $this->actingAs($admin, 'admin')
            ->get("/admin/orders/{$order->order_number}")
            ->assertOk()
            ->assertSee('Fragile glass bottle — double wrap.')
            ->assertDontSee('Portland Botanical Studio')
            ->assertDontSee('Everbloom Client #428')
            ->assertDontSee('Captured');

        $this->actingAs($admin, 'admin')
            ->get("/admin/invoices/{$order->order_number}")
            ->assertOk()
            ->assertSee($order->invoice->invoice_no)
            ->assertDontSee('Portland, OR 97201');
    }
}

