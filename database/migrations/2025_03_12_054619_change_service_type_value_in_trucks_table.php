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
        Schema::table('trucks', function (Blueprint $table) {
            $table->dropColumn('baseline_date');
            $table->dropColumn('whse');
        });
        DB::statement("
            UPDATE trucks
            SET service_type = 'at_home_maintenance'
            WHERE service_type IN ('ahm', 'AHM')
        ");
        DB::statement("
            UPDATE trucks
            SET service_type = 'pickup_delivery'
            WHERE service_type IN ('pickup-delivery', 'Pickup/Delivery', 'Delivery / Pickup')
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trucks', function (Blueprint $table) {
            $table->unsignedBigInteger('whse')->index();
            $table->dateTime('baseline_date')->nullable();
        });
        DB::statement("
            UPDATE trucks
            SET service = 'AHM'
            WHERE service IN ('at_home_maintenance')
        ");

        DB::statement("
            UPDATE trucks
            SET service = 'Delivery / Pickup'
            WHERE service IN ('pickup_delivery')
        ");
    }
};
