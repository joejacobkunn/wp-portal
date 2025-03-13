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
        Schema::table('floor_model_inventory', function (Blueprint $table) {
            $table->boolean('is_on_hold')->default(0)->after('sx_operator_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('floor_model_inventory', function (Blueprint $table) {
            $table->dropColumn('is_on_hold');
        });
    }
};
