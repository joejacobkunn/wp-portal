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
        Schema::table('dnr_backorders', function (Blueprint $table) {
            $table->string('dnr_items')->after('stage_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dnr_backorders', function (Blueprint $table) {
            $table->dropColumn('dnr_items');
        });
    }
};
