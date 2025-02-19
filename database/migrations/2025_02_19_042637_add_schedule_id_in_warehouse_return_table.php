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
        Schema::table('truck_schedule_returns', function (Blueprint $table) {
            $table->unsignedBigInteger('schedule_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('truck_schedule_returns', function (Blueprint $table) {
            $table->dropColumn('schedule_id');
        });
    }
};
