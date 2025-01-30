<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("UPDATE schedules SET line_items = '[]'");

        Schema::table('schedules', function (Blueprint $table) {
            $table->string('service_address')->after('status');
            $table->dropColumn('recommended_address');
            $table->json('line_items')->change();

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('service_address');
            $table->json('recommended_address')->nullable()->after('status');
            $table->string('line_items')->change();
        });
    }

};
