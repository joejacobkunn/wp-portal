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
        Schema::table('schedules', function (Blueprint $table) {
            $table->renameColumn('via_weingartz', 'not_purchased_via_weingartz');
        });
        Schema::table('schedules', function (Blueprint $table) {
            $table->boolean('not_purchased_via_weingartz')->nullable()->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->boolean('not_purchased_via_weingartz')->nullable(false)->default(false)->change();
        });
        Schema::table('schedules', function (Blueprint $table) {
            $table->renameColumn('not_purchased_via_weingartz', 'via_weingartz');
        });
    }
};
