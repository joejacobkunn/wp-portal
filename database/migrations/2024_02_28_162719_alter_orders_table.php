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
        Schema::rename('dnr_backorders','orders');

        Schema::table('orders', function (Blueprint $table) {
            $table->string('taken_by',10)->after('whse');
            $table->boolean('is_dnr')->after('taken_by')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('taken_by');
            $table->dropColumn('is_dnr');
        });

        Schema::rename('orders','dnr_backorders');


    }
};
