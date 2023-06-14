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
        Schema::create('account_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id');
            $table->string('access_token')->index();
            $table->dateTime('expires_at')->nullable();
            $table->tinyInteger('is_revoked')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_access_tokens');
    }
};
