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
        Schema::table('scheduler_zipcodes', function (Blueprint $table) {
            $table->decimal('delivery_rate')->change();
            $table->decimal('pickup_rate')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scheduler_zipcodes', function (Blueprint $table) {
            $table->integer('delivery_rate')->change();
            $table->integer('pickup_rate')->change();

        });
    }
};
