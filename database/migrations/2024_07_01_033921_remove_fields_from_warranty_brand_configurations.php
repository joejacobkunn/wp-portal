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
            // Remove fields
            $table->dropColumn('registration_url');
            $table->dropColumn('require_proof_of_reg');

            // Add fields
            $table->string('alt_name')->before('created_at');
            $table->string('prefix')->before('created_at');
        });

        Schema::dropIfExists('brand_warranty_lines');


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warranty_brand_configurations', function (Blueprint $table) {
            $table->string('registration_url')->nullable();
            $table->boolean('require_proof_of_reg')->default(0);

            $table->dropColumn('alt_name');
            $table->dropColumn('prefix');
        });

        Schema::create('brand_warranty_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('brand_warranty_id');
            $table->unsignedBigInteger('product_line_id');
            $table->timestamps();

            $table->foreign('brand_warranty_id')->references('id')->on('warranty_brand_configurations')->onDelete('cascade');
            $table->foreign('product_line_id')->references('id')->on('product_lines')->onDelete('cascade');
        });
    }
};
