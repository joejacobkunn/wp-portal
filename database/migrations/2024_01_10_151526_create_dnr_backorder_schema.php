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
        Schema::create('dnr_backorders', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('cono');
            $table->string('order_number');
            $table->string('order_number_suffix');
            $table->string('whse');
            $table->date('order_date');
            $table->unsignedInteger('stage_code');
            $table->string('sx_customer_number');
            $table->string('status');
            $table->unsignedInteger('last_updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dnr_backorders');
    }
};
