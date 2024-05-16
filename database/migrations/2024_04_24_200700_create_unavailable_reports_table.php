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
        Schema::create('unavailable_reports', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('cono');
            $table->unsignedInteger('user_id');
            $table->date('report_date');
            $table->json('data')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->string('note')->nullable();
            $table->string('status');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unavailable_reports');
    }
};
