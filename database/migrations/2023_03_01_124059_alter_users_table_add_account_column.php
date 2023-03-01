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
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('account_id')->nullable()->after('id');
            $table->softDeletes();
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->integer('admin_user')->nullable()->after('is_active');
        });

        Schema::create('user_metadata', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('invited_by')->nullable();
            $table->text('user_token')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('account_metadata', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id');
            $table->bigInteger('created_by');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['account_id']);
            $table->dropSoftDeletes();
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('admin_user');
        });

        Schema::dropIfExists('user_metadata');

        Schema::dropIfExists('account_metadata');
    }
};
