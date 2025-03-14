<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            UPDATE zones
            SET service = 'at_home_maintenance'
            WHERE service IN ('ahm', 'AHM')
        ");
        DB::statement("
            UPDATE zones
            SET service = 'pickup_delivery'
            WHERE service IN ('pickup-delivery', 'Pickup/Delivery')
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            UPDATE zones
            SET service = 'AHM'
            WHERE service IN ('at_home_maintenance')
        ");

        DB::statement("
            UPDATE zones
            SET service = 'pickup-delivery'
            WHERE service IN ('pickup_delivery')
        ");
    }
};
