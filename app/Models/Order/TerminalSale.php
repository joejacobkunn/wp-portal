<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class TerminalSale extends Model
{
    protected $table = 'pwa_terminal_sales';

    protected $fillable = [
        'order_id',
        'transaction_amount',
        'location_id',
        'product_transaction_id',
        'customer_id',
        'terminal_id',
        'emv_receipt_data',
        'status_code',
        'status',
        'created_ts',
        'payload',
        'response_text',
        'txn_code',
    ];
}
