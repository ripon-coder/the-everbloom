<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('addresses')->whereNull('phone')->update(['phone' => '']);

        Schema::table('addresses', function (Blueprint $table) {
            $columnsToDrop = array_values(array_filter(
                ['company', 'address_line2', 'postal_code'],
                fn (string $col) => Schema::hasColumn('addresses', $col)
            ));

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }

            $table->text('address_line1')->change();
            $table->string('phone', 40)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            if (!Schema::hasColumn('addresses', 'company')) {
                $table->string('company', 120)->nullable()->after('last_name');
            }
            if (!Schema::hasColumn('addresses', 'address_line2')) {
                $table->string('address_line2')->nullable()->after('address_line1');
            }
            if (!Schema::hasColumn('addresses', 'postal_code')) {
                $table->string('postal_code', 30)->nullable()->after('address_line2');
            }

            $table->string('address_line1')->change();
            $table->string('phone', 40)->nullable()->change();
        });
    }
};
