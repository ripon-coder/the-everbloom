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
        // Add indexes to products table
        Schema::table('products', function (Blueprint $table) {
            $table->index('status');
            $table->index('is_featured');
            $table->index('created_at');
        });

        // Add indexes to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->index('status');
            $table->index('payment_status');
            $table->index('created_at');
        });

        // Add indexes to categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->index('status');
        });

        // Add indexes to users table
        Schema::table('users', function (Blueprint $table) {
            $table->index('is_active');
        });

        // Add indexes to product_variants table
        Schema::table('product_variants', function (Blueprint $table) {
            $table->index('status');
        });

        // Add indexes to pages table
        Schema::table('pages', function (Blueprint $table) {
            $table->index('is_active');
        });

        // Add indexes to sliders table
        Schema::table('sliders', function (Blueprint $table) {
            $table->index('status');
            $table->index('sort_order');
        });

        // Add indexes to flash_sales table
        Schema::table('flash_sales', function (Blueprint $table) {
            $table->index('status');
            $table->index('start_date');
            $table->index('end_date');
        });

        // Add index to flash_sale_trackers table (preserving typo in table name if exists)
        if (Schema::hasTable('falsh_sale_trackers')) {
            Schema::table('falsh_sale_trackers', function (Blueprint $table) {
                $table->index('flash_sale_slug');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['is_featured']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('sliders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['sort_order']);
        });

        Schema::table('flash_sales', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['start_date']);
            $table->dropIndex(['end_date']);
        });

        if (Schema::hasTable('falsh_sale_trackers')) {
            Schema::table('falsh_sale_trackers', function (Blueprint $table) {
                $table->dropIndex(['flash_sale_slug']);
            });
        }
    }
};
