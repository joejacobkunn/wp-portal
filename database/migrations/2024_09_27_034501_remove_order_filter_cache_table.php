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
        Schema::dropIfExists('order_filter_caches');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('order_filter_caches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->json('filters')->nullable();
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }
};
