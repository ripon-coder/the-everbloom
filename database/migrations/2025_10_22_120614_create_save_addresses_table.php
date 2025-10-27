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
        Schema::create('save_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained("users")->onDelete("cascade");
            $table->string("name");
            $table->string("phone_number");
            $table->foreignId("district_id")->constrained("districts")->onDelete("cascade");
            $table->string("zone")->nullable();
            $table->text("address")->nullable();
            $table->enum("type_address",['home','office','other']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('save_addresses');
    }
};
