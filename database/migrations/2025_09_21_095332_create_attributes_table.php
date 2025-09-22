<?php

use App\Constants\AttributeStatus;
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
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_image')->default(0);
            $table->enum('status', [AttributeStatus::ACTIVE, AttributeStatus::INACTIVE])->default(AttributeStatus::ACTIVE);
            $table->timestamps();
            $table->softDeletes();
            // Add indexes
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }
};
