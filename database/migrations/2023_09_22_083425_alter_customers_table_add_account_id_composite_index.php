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
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['account_id', 'open_order_count']);
            $table->dropIndex(['sx_customer_number', 'name']);
            $table->index(['account_id', 'sx_customer_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['account_id', 'sx_customer_number']);
            $table->index(['account_id', 'open_order_count']);
            $table->index(['sx_customer_number', 'name']);
        });
    }
};
