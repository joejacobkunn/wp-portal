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
        Schema::table('warranty_brand_configurations', function (Blueprint $table) {
            $table->unsignedBigInteger('account_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warranty_brand_configurations', function (Blueprint $table) {
            $table->dropColumn('account_id');
        });
    }
};