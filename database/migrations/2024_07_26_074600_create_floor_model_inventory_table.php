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
        Schema::create('floor_model_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('whse')->constrained('warehouses');
            $table->string('product');
            $table->foreign('product')->references('prod')->on('products');
            $table->integer('qty');
            $table->string('sx_operator_id');
            $table->timestamps();

            $table->unique(['whse', 'product']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('floor_model_inventory');
    }
};
