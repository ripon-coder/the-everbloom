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
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('cascade');
            
            // Drop the admin_id column as it's not in the requirements
            $table->dropForeign(['admin_id']);
            $table->dropColumn('admin_id');
            
            // Drop the sku column as it should be on variants only
            $table->dropColumn('sku');
            
            // Add indexes for performance
            $table->index('brand_id');
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropForeign(['category_id']);
            $table->dropColumn(['brand_id', 'category_id']);
            
            $table->foreignId('admin_id')->nullable()->constrained('admins');
            $table->string('sku')->unique()->nullable();
        });
    }
};
