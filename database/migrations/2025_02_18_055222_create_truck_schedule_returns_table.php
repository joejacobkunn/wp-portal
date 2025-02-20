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
        Schema::create('truck_schedule_returns', function (Blueprint $table) {
            $table->id();
            $table->string('whse');
            $table->unsignedBigInteger('truck_id');
            $table->date('schedule_date');
            $table->string('expected_arrival_time');
            $table->string('last_scheduled_address');
            $table->string('distance');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['schedule_date', 'truck_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('truck_schedule_returns');
    }
};
