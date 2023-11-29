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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('account_id')->index();
            $table->string('prod')->index();
            $table->string('description');
            $table->string('look_up_name');
            $table->string('brand');
            $table->string('vend_no');
            $table->string('vendor');
            $table->string('category');
            $table->string('product_line');
            $table->string('active');
            $table->string('status');
            $table->string('list_price');
            $table->string('usage');
            $table->string('entered_date');
            $table->string('last_sold_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
