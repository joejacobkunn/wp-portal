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
        Schema::table('floor_model_inventory_notes', function (Blueprint $table) {
            $table->string('warehouse_short')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('floor_model_inventory_notes', function (Blueprint $table) {
            $table->dropColumn('warehouse_short');
        });
    }
};
