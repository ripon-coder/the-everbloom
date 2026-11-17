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
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
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

            $table->index(['product_id', 'remaining_qty'], 'purchase_batch_items_product_rem_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_batch_items');
        Schema::dropIfExists('purchase_batches');
        Schema::dropIfExists('vendors');
    }
};

