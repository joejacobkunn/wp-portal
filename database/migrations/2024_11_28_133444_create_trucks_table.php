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

        Schema::create('trucks', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('truck_name');
            $table->unsignedBigInteger('location_id'); // Foreign key
            $table->string('vin_number')->unique();
            $table->string('model_and_make');
            $table->integer('year');
            $table->string('color');
            $table->text('notes')->nullable();
            $table->timestamps(); // Created at & Updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trucks');
    }
};
