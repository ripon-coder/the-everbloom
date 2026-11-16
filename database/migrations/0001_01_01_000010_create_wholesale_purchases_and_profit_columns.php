<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('company_name', 150)->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('email', 191)->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('purchase_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_code', 60)->unique();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->string('vendor_name', 150);
            $table->string('reference_no', 100)->nullable();
            $table->date('purchased_at');
            $table->integer('total_qty')->default(0);
            $table->decimal('total_cost', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_batch_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_batch_id')->constrained('purchase_batches')->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->string('batch_lot', 60)->nullable();
            $table->integer('purchased_qty');
            $table->integer('remaining_qty');
            $table->decimal('unit_buy_price', 10, 2);
            $table->decimal('line_total_cost', 12, 2);
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->decimal('avg_buy_price', 10, 2)->default(0)->after('compare_at_price');
            $table->decimal('last_buy_price', 10, 2)->default(0)->after('avg_buy_price');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->decimal('avg_buy_price', 10, 2)->default(0)->after('compare_at_price');
            $table->decimal('last_buy_price', 10, 2)->default(0)->after('avg_buy_price');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('unit_buy_price', 10, 2)->default(0)->after('price');
            $table->decimal('line_cost_total', 10, 2)->default(0)->after('line_total');
            $table->decimal('unit_profit', 10, 2)->default(0)->after('line_cost_total');
            $table->decimal('line_profit', 10, 2)->default(0)->after('unit_profit');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total_cost', 10, 2)->default(0)->after('total');
            $table->decimal('total_profit', 10, 2)->default(0)->after('total_cost');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['total_cost', 'total_profit']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['unit_buy_price', 'line_cost_total', 'unit_profit', 'line_profit']);
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['avg_buy_price', 'last_buy_price']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['avg_buy_price', 'last_buy_price']);
        });

        Schema::dropIfExists('purchase_batch_items');
        Schema::dropIfExists('purchase_batches');
        Schema::dropIfExists('vendors');
    }
};

