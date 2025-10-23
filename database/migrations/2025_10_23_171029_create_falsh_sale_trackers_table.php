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
        Schema::create('falsh_sale_trackers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_variant_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('original_price', 10, 2);
            $table->decimal('discount_amount', 10, 2);
            $table->decimal('discounted_price', 10, 2);
            $table->string('discount_type');
            $table->string('quantity');
            $table->decimal('total_discounted_price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('falsh_sale_trackers');
    }
};
