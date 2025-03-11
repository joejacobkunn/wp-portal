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
        Schema::table('truck_schedules', function (Blueprint $table) {
            $table->boolean('is_pickup')->after('end_time')->default(0);
            $table->boolean('is_delivery')->after('is_pickup')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('truck_schedules', function (Blueprint $table) {
            $table->dropColumn('is_pickup');
            $table->dropColumn('is_delivery');
        });
    }
};
