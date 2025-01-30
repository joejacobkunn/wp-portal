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
            $table->index('order_number');
            $table->index('sx_customer_number');
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->index('sx_customer_number');
        });
        Schema::table('schedules', function (Blueprint $table) {
            $table->index('sx_ordernumber');
            $table->index('truck_schedule_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('order_number');
            $table->dropIndex('sx_customer_number');
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('sx_customer_number');
        });
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropIndex('sx_ordernumber');
            $table->dropIndex('truck_schedule_id');
        });
    }
};
