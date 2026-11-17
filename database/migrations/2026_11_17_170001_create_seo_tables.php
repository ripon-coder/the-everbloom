<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('slug_redirects')) {
            Schema::create('slug_redirects', function (Blueprint $table) {
                $table->id();
                $table->string('old_path', 255)->unique();
                $table->string('new_path', 255);
                $table->unsignedSmallInteger('status_code')->default(301);
                $table->unsignedInteger('hits')->default(0);
                $table->timestamps();
            });
        }

        foreach (['products', 'categories', 'pages'] as $tableName) {
            if (!Schema::hasColumn($tableName, 'og_image')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('og_image', 255)->nullable()->after('meta_description');
                    $table->string('canonical_url', 255)->nullable()->after('og_image');
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['products', 'categories', 'pages'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['og_image', 'canonical_url']);
            });
        }

        Schema::dropIfExists('slug_redirects');
    }
};

