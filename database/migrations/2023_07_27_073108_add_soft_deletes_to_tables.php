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
        Schema::table('account-modules', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('account_access_tokens', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('account_api_keys', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('herohub_configs', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('sx_accounts', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('webhook_calls', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account-modules', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('account_access_tokens', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('account_api_keys', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('herohub_configs', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('sx_accounts', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('webhook_calls', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
