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
            $table->dropColumn('zone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scheduler_zipcodes', function (Blueprint $table) {
            $table->json('zone')->after('zip_code');
        });
    }
};