<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('slug', 160)->unique();
            $table->string('logo', 255)->nullable();
            $table->string('image_source', 40)->default('local');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'sort_order'], 'brands_active_sort_idx');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->after('category_id')->constrained('brands')->nullOnDelete();
        });

        // Backfill distinct existing products.brand values into brands and link products.brand_id
        $distinctBrands = DB::table('products')
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->select('brand')
            ->distinct()
            ->orderBy('brand')
            ->pluck('brand');

        $sort = 1;
        foreach ($distinctBrands as $brandName) {
            $baseSlug = Str::slug((string) $brandName) ?: 'brand-' . $sort;
            $slug = $baseSlug;
            while (DB::table('brands')->where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . Str::lower(Str::random(4));
            }

            $brandId = DB::table('brands')->insertGetId([
                'name' => $brandName,
                'slug' => $slug,
                'is_active' => true,
                'sort_order' => $sort++,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('products')
                ->where('brand', $brandName)
                ->update(['brand_id' => $brandId]);
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('brand_id');
        });

        Schema::dropIfExists('brands');
    }
};

