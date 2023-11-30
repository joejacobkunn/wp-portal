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

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('category');
            $table->dropColumn('product_line');
            $table->dropColumn('brand');
            $table->dropColumn('vendor');
            $table->dropColumn('vend_no');
            $table->unsignedInteger('category_id')->after('look_up_name');
            $table->unsignedInteger('product_line_id')->after('category_id');
            $table->unsignedInteger('brand_id')->after('product_line_id');
            $table->unsignedInteger('vendor_id')->after('brand_id');
        });

        Schema::create('product_brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('product_category', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('product_lines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('brand_id')->index();
            $table->timestamps();
        });

        Schema::create('product_vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('vendor_number');
            $table->timestamps();
        });



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_brands');
        Schema::dropIfExists('product_category');
        Schema::dropIfExists('product_lines');
        Schema::dropIfExists('product_vendors');

        Schema::table('products', function (Blueprint $table) {
            $table->string('category');
            $table->string('product_line');
            $table->string('brand');
            $table->string('vendor');
            $table->string('vend_no');
            $table->dropColumn('category_id');
            $table->dropColumn('product_line_id');
            $table->dropColumn('brand_id');
            $table->dropColumn('vendor_id');
        });

    }
};
