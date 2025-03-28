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
        Schema::table('truck_cargo_configurator', function (Blueprint $table) {
            $table->unsignedBigInteger('sro_equipment_category_id')->nullable();
            $table->foreign('sro_equipment_category_id')
            ->references('id')
            ->on('sro_equipment_categories')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('truck_cargo_configurator', function (Blueprint $table) {
            $table->dropForeign(['sro_equipment_category_id']);
            $table->dropColumn('sro_equipment_category_id');
        });
    }
};
