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
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('country');
            $table->string('region')->nullable()->after('address_1');
            $table->string('province')->nullable()->after('region');
            $table->string('barangay')->nullable()->after('city');
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn(['region', 'province', 'barangay']);
            $table->string('country')->default('PH');
        });
    }
};
