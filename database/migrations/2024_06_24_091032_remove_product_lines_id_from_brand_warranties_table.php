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
            $table->dropColumn('product_lines_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warranty_brand_configurations', function (Blueprint $table) {
            $table->json('product_lines_id')->nullable();
        });
    }
};
