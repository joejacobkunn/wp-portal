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
            $table->unsignedBigInteger('whse_id');
            $table->integer('zip_code');
            $table->json('service')->nullable();
            $table->unsignedBigInteger('zone');
            $table->integer('delivery_rate');
            $table->integer('pickup_rate');
            $table->string('notes')->nullable();
            $table->boolean('is_active')->default(0);
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
