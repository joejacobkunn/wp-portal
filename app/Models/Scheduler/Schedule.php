<?php

namespace App\Models\Scheduler;

use App\Enums\Scheduler\ScheduleEnum;
use App\Models\Core\User;
use App\Models\Order\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'sx_ordernumber',
        'order_number_suffix',
        'schedule_date',
        'truck_schedule_id',
        'line_items',
        'status',
        'recommended_address',
        'created_by'
    ];

    protected $casts = [
        'line_items' => 'array',
        'recommended_address' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'sx_ordernumber', 'order_number');
    }

    public function truckSchedule()
    {
        return $this->belongsTo(TruckSchedule::class, 'truck_schedule_id');
    }
}
