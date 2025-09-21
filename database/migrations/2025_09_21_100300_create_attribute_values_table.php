<?php

use App\Constants\AttributeValueStatus;
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
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained()->onDelete('cascade');
            $table->string('value')->nullable(); // e.g., Red, Blue, S, M
            $table->enum('status', [AttributeValueStatus::ACTIVE, AttributeValueStatus::INACTIVE])->default(AttributeValueStatus::ACTIVE);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index('attribute_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_values');
    }
};
