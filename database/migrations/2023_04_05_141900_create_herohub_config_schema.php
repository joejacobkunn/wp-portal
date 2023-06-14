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
        Schema::create('herohub_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('account_id');
            $table->string('client_id');
            $table->string('client_key');
            $table->string('organization_guid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('herohub_configs');
    }
};
