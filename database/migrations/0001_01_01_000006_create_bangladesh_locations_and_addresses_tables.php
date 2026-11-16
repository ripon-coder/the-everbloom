<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('bn_name', 120)->nullable();
            $table->string('slug', 120)->unique();
            $table->decimal('delivery_charge', 10, 2)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('thanas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained('districts')->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('bn_name', 120)->nullable();
            $table->string('slug', 140)->index();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained('districts')->cascadeOnDelete();
            $table->foreignId('thana_id')->constrained('thanas')->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('bn_name', 150)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_default')->default(false);
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('company', 120)->nullable();
            $table->string('country', 60)->default('Bangladesh');
            $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete();
            $table->foreignId('thana_id')->nullable()->constrained('thanas')->nullOnDelete();
            $table->foreignId('area_id')->nullable()->constrained('areas')->nullOnDelete();
            $table->string('district_name', 120)->nullable();
            $table->string('thana_name', 120)->nullable();
            $table->string('area_name', 150)->nullable();
            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->string('postal_code', 30)->nullable();
            $table->string('phone', 40)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('areas');
        Schema::dropIfExists('thanas');
        Schema::dropIfExists('districts');
    }
};

