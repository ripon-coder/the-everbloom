<?php

use App\Constants\ProductVariantStatus;
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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('sku')->unique();
            $table->decimal('buying_price', 10, 2)->default(0)->comment('Buying price of the variant');
            $table->decimal('sell_price', 10, 2)->default(0)->comment('Selling price of the variant');
            $table->decimal('discount_price', 10, 2)->default(0)->comment('Discount price of the variant');
            $table->decimal('discount_amount', 10, 2)->default(0)->comment('Discount amount or percentage applied');
            $table->decimal( 'weight', 8, 2)->nullable()->after('stock')->comment('Weight in kg');
            $table->integer('stock')->default(0);
            $table->string('status')->default(ProductVariantStatus::ACTIVE);

            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index('product_id');
            $table->index('sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
