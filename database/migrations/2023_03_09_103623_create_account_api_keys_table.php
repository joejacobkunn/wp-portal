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
        Schema::create('account_api_keys', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id');
            $table->string('label');
            $table->string('client_key');
            $table->text('client_secret');
            $table->string('client_secret_last4');
            $table->tinyInteger('is_revoked')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_api_keys');
    }
};
