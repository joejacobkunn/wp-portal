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
        DB::table('schedules')
            ->where('status', 'Confirmed')
            ->update(['status' => 'confirmed']);

        DB::table('schedules')
            ->where('status', 'Cancelled')
            ->update(['status' => 'cancelled']);

        DB::table('schedules')
            ->where('status', 'Completed')
            ->update(['status' => 'completed']);

        DB::table('schedules')
            ->where('status', 'Scheduled')
            ->update(['status' => 'scheduled']);

        DB::table('schedules')
            ->where('status', 'Out for Delivery')
            ->update(['status' => 'out_for_delivery']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('schedules')
            ->where('status', 'confirmed')
            ->update(['status' => 'Confirmed']);

        DB::table('schedules')
            ->where('status', 'cancelled')
            ->update(['status' => 'Cancelled']);

        DB::table('schedules')
            ->where('status', 'completed')
            ->update(['status' => 'Completed']);

        DB::table('schedules')
            ->where('status', 'scheduled')
            ->update(['status' => 'Scheduled']);

        DB::table('schedules')
            ->where('status', 'out_for_delivery')
            ->update(['status' => 'Out for Delivery']);
    }
};
