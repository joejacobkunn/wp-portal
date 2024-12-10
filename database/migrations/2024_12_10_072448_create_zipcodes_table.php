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
        Schema::create('scheduler_zipcodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('whse_id')->constrained('warehouses')->onDelete('cascade');
            $table->integer('zip_code');
            $table->string('service');
            $table->foreignId('zone')->constrained('zones')->onDelete('cascade');
            $table->integer('delivery_rate');
            $table->integer('pickup_rate');
            $table->integer('notes');
            $table->boolean('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduler_zipcodes');
    }
};
