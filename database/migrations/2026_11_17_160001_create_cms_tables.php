<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 160)->unique();
            $table->string('title', 180);
            $table->longText('content')->nullable();
            $table->string('meta_title', 180)->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title', 180);
            $table->string('subtitle', 255)->nullable();
            $table->string('image')->nullable();
            $table->string('image_source', 40)->default('local');
            $table->string('link_url', 255)->nullable();
            $table->string('button_text', 80)->nullable();
            $table->string('position', 40)->default('hero');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('homepage_sections', function (Blueprint $table) {
            $table->id();
            $table->string('key', 80)->unique();
            $table->string('title', 180)->nullable();
            $table->string('subtitle', 255)->nullable();
            $table->json('payload')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question', 255);
            $table->text('answer');
            $table->string('category', 80)->default('General');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('contact_inquiries', function (Blueprint $table) {
            $table->text('admin_reply')->nullable()->after('status');
            $table->timestamp('replied_at')->nullable()->after('admin_reply');
            $table->foreignId('replied_by')->nullable()->after('replied_at')->constrained('admins')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('contact_inquiries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('replied_by');
            $table->dropColumn(['admin_reply', 'replied_at']);
        });

        Schema::dropIfExists('faqs');
        Schema::dropIfExists('homepage_sections');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('pages');
    }
};

