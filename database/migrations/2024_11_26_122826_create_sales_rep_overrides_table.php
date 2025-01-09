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
        Schema::create('sales_rep_overrides', function (Blueprint $table) {
            $table->id();
            $table->string('customer_number');
            $table->string('ship_to');
            $table->string('prod_line');
            $table->string('sales_rep');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_rep_overrides');
    }
};
