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
        Schema::table('unavailable_equipments', function (Blueprint $table) {
            $table->integer('qty')->default(1)->after('whse');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unavailable_equipments', function (Blueprint $table) {
            $table->dropColumn('qty');
        });
    }
};
