<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 40)->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_email', 191);
            $table->string('customer_phone', 40)->nullable();
            $table->string('status', 30)->default('Placed');
            $table->string('current_step', 30)->default('Placed');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->string('discount_code', 60)->nullable();
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->unsignedInteger('items_count')->default(1);
            $table->string('delivery_method');
            $table->string('payment_method');
            $table->string('payment_status', 30)->default('Paid');
            $table->string('tracking_number', 80)->default('Pending assignment');
            $table->json('shipping_address');
            $table->text('notes')->nullable();
            $table->timestamp('placed_at');
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->string('name');
            $table->string('slug', 160);
            $table->string('variant', 80)->nullable();
            $table->string('image')->nullable();
            $table->string('image_source', 40)->default('local');
            $table->unsignedInteger('qty')->default(1);
            $table->decimal('price', 10, 2);
            $table->decimal('line_total', 10, 2);
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_no', 50)->unique();
            $table->string('customer_name', 160);
            $table->string('customer_email', 191);
            $table->decimal('amount', 10, 2);
            $table->string('status', 30)->default('Paid');
            $table->string('payment_method');
            $table->timestamp('issued_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
