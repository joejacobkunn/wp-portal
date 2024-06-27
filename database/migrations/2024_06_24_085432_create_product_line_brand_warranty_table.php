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
        Schema::create('brand_warranty_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('brand_warranty_id');
            $table->unsignedBigInteger('product_line_id');
            $table->timestamps();

            $table->foreign('brand_warranty_id')->references('id')->on('warranty_brand_configurations')->onDelete('cascade');
            $table->foreign('product_line_id')->references('id')->on('product_lines')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_warranty_lines');
    }
};
