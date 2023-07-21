<?php

namespace App\Models\SX;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public $connection = 'sx';

    protected $table = 'oeeh';

    protected $primaryKey = 'orderno';

    private $stage_codes = [
        0 => 'Quoted',
        1 => 'Ordered',
        2 => 'Picked',
        3 => 'Shipped',
        4 => 'Invoiced',
        5 => 'Paid',
        9 => 'Cancelled',
    ];

    private $shipping_stages = [
        1 => 'Reserved',
        2 => 'Committed',
        3 => 'Shipped',
    ];

    protected $casts = [
        'enterdt' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function shipping()
    {

    }

    public function scopeOpenOrders(Builder $query)
    {
        $query->whereIn('stagecd', [1, 2]);
    }

    public function getStageCode($stage_code)
    {
        return $this->stage_codes[$stage_code];
    }

    public function getShippingStage($stage_code)
    {
        if ($stage_code > 3) {
            return 'Delivered';
        }

        return $this->shipping_stages[$stage_code];
    }
}
