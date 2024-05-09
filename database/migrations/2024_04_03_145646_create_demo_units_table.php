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
        Schema::create('unavailable_equipments', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('cono')->index();
            $table->string('whse')->index();
            $table->string('possessed_by')->index();
            $table->string('product_code');
            $table->string('product_name');
            $table->string('serial_number')->nullable();
            $table->string('base_price')->nullable();
            $table->boolean('is_unavailable')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unavailable_equipments');
    }
};
