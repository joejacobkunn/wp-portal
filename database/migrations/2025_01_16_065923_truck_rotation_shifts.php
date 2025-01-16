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
        Schema::create('truck_rotation_shifts', function (Blueprint $table) {
            $table->id();
            $table->integer('truck_id');
            $table->integer('zone_id');
            $table->integer('shift_id');
            $table->date('scheduled_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('truck_rotation_shifts');
    }
};
