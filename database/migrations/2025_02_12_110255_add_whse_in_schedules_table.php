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
        Schema::table('schedules', function (Blueprint $table) {
            $table->string('whse')->after('truck_schedule_id');
        });
        DB::statement("
        UPDATE schedules
        JOIN truck_schedules ON schedules.truck_schedule_id = truck_schedules.id
        JOIN trucks ON truck_schedules.truck_id = trucks.id
        SET schedules.whse = trucks.warehouse_short
    ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('whse');
        });
    }
};
