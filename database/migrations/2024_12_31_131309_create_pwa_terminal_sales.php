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
        Schema::create('pwa_terminal_sales', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->nullable();
            $table->string('transaction_amount')->nullable();
            $table->string('location_id')->nullable();
            $table->string('product_transaction_id')->nullable();
            $table->string('customer_id')->nullable();
            $table->string('terminal_id')->nullable();
            $table->string('emv_receipt_data')->nullable();
            $table->string('status_code')->nullable();
            $table->string('status')->nullable();
            $table->string('created_ts')->nullable();
            $table->string('txn_code')->nullable()->index();
            $table->longText('payload')->nullable();
            $table->longText('response_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pwa_terminal_sales');
    }
};
