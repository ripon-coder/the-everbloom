<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('author', 120);
            $table->string('author_email', 191)->nullable();
            $table->string('location', 120)->nullable();
            $table->string('variant', 80)->nullable();
            $table->unsignedTinyInteger('rating');
            $table->string('headline');
            $table->text('body');
            $table->boolean('verified')->default(true);
            $table->boolean('is_featured_testimonial')->default(false);
            $table->string('status', 30)->default('Approved');
            $table->timestamps();

            $table->index(['product_id', 'status'], 'reviews_product_status_idx');
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 60)->default('general');
            $table->string('key', 100)->unique();
            $table->json('value');
            $table->timestamps();
        });

        Schema::create('contact_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('email', 191);
            $table->text('message');
            $table->string('status', 30)->default('unread');
            $table->timestamps();
        });

        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email', 191)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('contact_inquiries');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('reviews');
    }
};
