<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderReceipt extends Model
{
    use HasFactory;

    protected $table = 'purchase_order_receipts';

    protected $fillable = ['po_number','people_vox_reference_no','receipt_date','line_items','is_processed'];

    protected $casts = ['receipt_date' => 'date', 'line_items' => 'array'];
}
