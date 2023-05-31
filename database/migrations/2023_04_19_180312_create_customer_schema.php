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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('account_id');
            $table->unsignedBigInteger('sx_customer_number');
            $table->string('name');
            $table->string('customer_type');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('address2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('look_up_name')->nullable();
            $table->string('sales_territory')->nullable();
            $table->string('last_sale_date')->nullable();
            $table->string('sales_rep_in')->nullable();
            $table->string('sales_rep_out')->nullable();
            $table->date('customer_since')->nullable();
            $table->boolean('is_active')->default(1);
            $table->boolean('has_open_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
