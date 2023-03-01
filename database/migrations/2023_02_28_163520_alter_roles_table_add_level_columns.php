<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->string('label')->nullable();
            $table->integer('reporting_role')->after('name')->nullable();
            $table->integer('level')->after('reporting_role');
            $table->integer('is_preset')->after('level')->default(0);
            $table->integer('created_by')->after('is_preset')->nullable();
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->string('label')->nullable();
            $table->string('group_name')->after('label')->nullable();
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['label', 'reporting_role', 'level', 'is_preset', 'created_by']);
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('label', 'group_name', 'description');
        });
    }
};
