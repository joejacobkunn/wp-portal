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
        Schema::create('reporting_dashboards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('reports');
            $table->boolean('is_active')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->string('tally_column')->after('group_by');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reporting_dashboards');

        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('tally_column');
        });

    }
};
