<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->unsignedTinyInteger('level')->default(1);
            $table->string('slug', 120)->unique();
            $table->string('name', 120);
            $table->string('short_name', 60)->nullable();
            $table->string('tagline', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('image_source', 40)->default('local');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
