<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('order_number')->unique();
            $table->decimal('before_discount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('coupon_discount_amount', 10, 2)->default(0);
            $table->decimal('flash_discount_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->nullable();
            $table->decimal('shipping_amount', 10, 2)->default(0);
            $table->decimal('weight', 10, 2)->default(0);
            $table->string('coupon_used')->nullable()->after('weight');
            $table->string('status')->default('pending');
            $table->string('payment_status')->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('payment_account')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
