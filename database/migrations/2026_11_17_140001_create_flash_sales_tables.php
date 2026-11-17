<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flash_sales', function (Blueprint $table) {
            $table->id();
            $table->string('title', 180);
            $table->string('slug', 200)->unique();
            $table->text('description')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('image_source', 40)->default('local');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->string('status', 20)->default('draft');
            $table->boolean('show_on_homepage')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'starts_at', 'ends_at'], 'flash_sales_status_window_idx');
        });

        Schema::create('flash_sale_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flash_sale_id')->constrained('flash_sales')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->cascadeOnDelete();
            $table->decimal('sale_price', 10, 2);
            $table->unsignedInteger('sale_stock');
            $table->unsignedInteger('sold_qty')->default(0);
            $table->unsignedInteger('max_qty_per_customer')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['flash_sale_id', 'product_id', 'product_variant_id'], 'fsp_sale_prod_var_unique');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('flash_sale_id')->nullable()->after('product_variant_id')->constrained('flash_sales')->nullOnDelete();
            $table->foreignId('flash_sale_product_id')->nullable()->after('flash_sale_id')->constrained('flash_sale_products')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('flash_sale_product_id');
            $table->dropConstrainedForeignId('flash_sale_id');
        });

        Schema::dropIfExists('flash_sale_products');
        Schema::dropIfExists('flash_sales');
    }
};

