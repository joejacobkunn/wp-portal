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
        Schema::create('smsmarketing', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('file');
            $table->string('failed_file')->nullable();
            $table->integer('processed')->nullable();
            $table->integer('processed_count')->default(0);
            $table->integer('total_count')->default(0);
            $table->unsignedBigInteger('created_by');
            $table->string('status');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smsmarketing');
    }
};
