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
        Schema::create('warranty_brand_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')
            ->constrained('product_brands')
            ->onDelete('cascade');
            $table->json('product_lines_id');
            $table->string('registration_url')->nullable();
            $table->boolean('require_proof_of_reg')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warranty_brand_configurations');
    }
};
