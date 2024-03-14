<?php

namespace App\Models\Order;

use App\Models\Core\Comment;
use App\Enums\Order\OrderStatus;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;


class Order extends Model
{
    use HasFactory,SoftDeletes,LogsActivity;

    protected $table = 'orders';

    protected $fillable = [
        'cono',
        'order_number',
        'order_number_suffix',
        'whse',
        'status',
        'order_date',
        'stage_code',
        'sx_customer_number',
        'last_updated_by',
        'dnr_items',
        'taken_by',
        'is_dnr',
        'promise_date',
        'is_sro',
        'ship_via',
        'qty_ship',
        'qty_ord'
    ];

    protected $casts = [
        'order_date' => 'date',
        'promise_date' => 'date',
        'status' => OrderStatus::class,
        'dnr_items' => 'array'
    ];

    const LOG_FIELD_MAPS = [
        'created_at' => [
            'field_label' => 'Created At',
        ],
        'order_date' => [
            'field_label' => 'Order Date',
        ],
        'stage_code' => [
            'field_label' => 'Stage Code',
        ],
        'status' => [
            'field_label' => 'Status',
        ],
    ];

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


    
    public function isPendingReview()
    {
        return $this->status == OrderStatus::PendingReview;
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function getStageCode()
    {
        return $this->stage_codes[$this->stage_code];
    }

    public function scopeOpenOrders(Builder $query)
    {
        $query->whereIn('stage_code', [1, 2]);
    }


    public function getShippingStage($stage_code)
    {
        if ($stage_code > 3) {
            return 'Delivered';
        }

        return $this->shipping_stages[$stage_code];
    }

}
