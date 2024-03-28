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
            $table->date('promise_date')->nullable()->after('order_date');
            $table->boolean('is_sro')->default(0)->after('promise_date');
            $table->string('ship_via')->nullable()->after('is_sro');
            $table->tinyInteger('qty_ship')->nullable()->after('ship_via');
            $table->tinyInteger('qty_ord')->nullable()->after('qty_ship');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'promise_date',
                'is_sro',
                'ship_via',
                'qty_ship',
                'qty_ord'
            ]);
        });
    }
};
