<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('slug', 160)->unique();
            $table->string('sku', 60)->unique();
            $table->string('name', 180);
            $table->string('subtitle', 255)->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('compare_at_price', 10, 2)->nullable();
            $table->decimal('rating', 3, 2)->default(5.00);
            $table->unsignedInteger('review_count')->default(0);
            $table->text('short_description')->nullable();
            $table->text('description')->nullable();
            $table->text('ingredients')->nullable();
            $table->json('key_ingredients')->nullable();
            $table->json('benefits')->nullable();
            $table->json('skin_types')->nullable();
            $table->text('how_to_use')->nullable();
            $table->json('sizes')->nullable();
            $table->json('images')->nullable();
            $table->string('image_source', 40)->default('local');
            $table->integer('stock_count')->default(25);
            $table->integer('min_threshold')->default(10);
            $table->string('stock_status', 30)->default('in_stock');
            $table->string('batch_lot', 60)->nullable();
            $table->date('last_restocked_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('best_seller')->default(false);
            $table->string('badge', 60)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('image');
            $table->string('image_source', 40)->default('s3');
            $table->boolean('is_primary')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('size', 80);
            $table->string('sku', 80)->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('compare_at_price', 10, 2)->nullable();
            $table->integer('stock_count')->default(25);
            $table->string('stock_status', 30)->default('in_stock');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
    }
};
