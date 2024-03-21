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
        Schema::rename("order_email_templates", "order_notification_templates");

        Schema::table('order_notification_templates', function (Blueprint $table) {
            $table->string('type')->after('is_active');
            $table->string('email_subject')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_notification_templates', function (Blueprint $table) {
            $table->dropColumn(['type', 'email_subject']);
        });

        Schema::rename("order_notification_templates", "order_email_templates");
    }
};
