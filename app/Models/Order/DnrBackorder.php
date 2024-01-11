<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DnrBackorder extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'dnr_backorders';

    protected $fillable = [
        'cono',
        'order_number',
        'order_number_suffix',
        'whse',
        'status',
        'order_date',
        'last_updated_by'
    ];

    protected $casts = [
        'order_date' => 'date'
    ];
}
