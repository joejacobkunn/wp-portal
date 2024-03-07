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
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('order_number')->change();
            $table->unsignedTinyInteger('order_number_suffix')->change();
            $table->index(['order_number', 'order_number_suffix', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['order_number', 'order_number_suffix', 'status']);
            $table->string('order_number')->change();
            $table->string('order_number_suffix')->change();
        });
    }
};
