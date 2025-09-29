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
        Schema::create('order_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('status'); // pending, processing, shipped, delivered, cancelled, returned
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('carrier')->nullable(); // FedEx, UPS, DHL, etc.
            $table->timestamp('estimated_delivery')->nullable();
            $table->timestamp('actual_delivery')->nullable();
            $table->json('tracking_details')->nullable(); // Store additional tracking data
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('order_id');
            $table->index('status');
            $table->index('tracking_number');
            $table->index('carrier');
            $table->index('estimated_delivery');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_trackings');
    }
};
