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
        Schema::table('trucks', function (Blueprint $table) {
            $table->unsignedBigInteger('whse')->after('location_id')->index();
            $table->unsignedBigInteger('driver')->after('whse');
            $table->string('cubic_storage_space')->after('driver');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trucks', function (Blueprint $table) {
            $table->dropColumn([
                'whse',
                'driver',
                'cubic_storage_space',
                'deleted_at'
            ]);
        });
    }
};
