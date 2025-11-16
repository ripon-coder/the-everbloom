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
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->nullOnDelete();

            $table->foreignId('order_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->unsignedTinyInteger('rating'); // 1–5
            $table->string('title')->nullable();
            $table->text('review')->nullable();

            $table->json('images')->nullable();

            $table->unsignedInteger('likes')->default(0);
            $table->unsignedInteger('dislikes')->default(0);

            $table->boolean('is_verified')->default(false);
            $table->boolean('is_approved')->default(true);
            $table->boolean('is_reported')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
