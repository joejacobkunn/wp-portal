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
        Schema::table('warranty_imports', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->smallInteger('total_records')->after('processed_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warranty_imports', function (Blueprint $table) {
            $table->dropColumn(['name','total_records']);
        });
    }
};
