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
        Schema::table('schedules', function (Blueprint $table) {
            $table->string('sro_number')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->string('reschedule_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('sro_number');
            $table->dropColumn('cancel_reason');
            $table->dropColumn('reschedule_reason');
        });
    }
};
