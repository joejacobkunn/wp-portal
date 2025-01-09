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
        Schema::table('scheduler_notification_templates', function (Blueprint $table) {
            $table->dropColumn([
                'account_id',
                'created_by',
                'is_active'
            ]);

            $table->string('slug')->after('name')->index();
            $table->string('description')->after('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scheduler_notification_templates', function (Blueprint $table) {
            $table->string('account_id');
            $table->string('created_by');
            $table->boolean('is_active');
        });
    }
};
