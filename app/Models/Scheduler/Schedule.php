<?php

namespace App\Models\Scheduler;

use App\Enums\Scheduler\ScheduleEnum;
use App\Enums\Scheduler\ScheduleStatusEnum;
use App\Models\Core\Comment;
use App\Models\Core\User;
use App\Models\Core\Warehouse;
use App\Models\Order\Order;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'type',
        'sx_ordernumber',
        'order_number_suffix',
        'schedule_date',
        'truck_schedule_id',
        'line_item',
        'status',
        'service_address',
        'created_by',
        'schedule_type',
        'cancel_reason',
        'reschedule_reason',
        'sro_number',
        'cancelled_by',
        'cancelled_at',
        'completed_at',
        'completed_by',
        'confirmed_by',
        'confirmed_at',
        'expected_arrival_time',
        'travel_prio_number',
        'not_purchased_via_weingartz',
        'whse',
        'serial_no'
    ];

    protected $casts = [
        'schedule_date' => 'date',
        'line_item' => 'array',
        'completed_at' => 'datetime'
    ];
    const LOG_FIELD_MAPS = [

        'type' => [
            'field_label' => 'Type',
        ],
        'sx_ordernumber' => [
            'field_label' => 'Order Number',
        ],
        'order_number_suffix' => [
            'field_label' => 'Suffix',
        ],
        'schedule_date' => [
            'field_label' => 'Schedule Date',
        ],
        'truck_schedule_id' => [
            'field_label' => 'Truck Schedule ID',
        ],
        'line_item' => [
            'field_label' => 'Line Item',
            'resolve' => 'resolveLineItem'
        ],
        'status' => [
            'field_label' => 'Status',
        ],
        'service_address' => [
            'field_label' => 'Service Address',
        ],
        'created_by' => [
            'field_label' => 'Created By',
        ],
        'schedule_type' => [
            'field_label' => 'Schedule Type',
        ],
        'cancel_reason' => [
            'field_label' => 'Cancel Reason',
        ],
        'reschedule_reason' => [
            'field_label' => 'Reschedule Reason',
        ],
        'sro_number' => [
            'field_label' => 'SRO Number',
        ],
        'cancelled_by' => [
            'field_label' => 'Cancelled By',
        ],
        'cancelled_at' => [
            'field_label' => 'Cancelled At',
        ],
        'confirmed_by' => [
            'field_label' => 'Confirmed By',
        ],
        'confirmed_at' => [
            'field_label' => 'Confirmed At',
        ],
        'not_purchased_via_weingartz' => [
            'field_label' => 'Not purchased via Weingartz',
        ],
        'whse' => [
            'field_label' => 'Warehouse',
        ],
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

    public function scheduleId()
    {
        $unique_id = 1000000 + $this->id;
        return strtoupper(substr($this->truckSchedule->truck->warehouse->short,0,1)).$unique_id;
    }

    public function getStatusColorAttribute(): string
    {
        return ScheduleStatusEnum::from($this->status)->color();
    }

    public function getStatusColorClassAttribute(): string
    {
        return ScheduleStatusEnum::from($this->status)->colorClass();
    }

    public function cancelledUser()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function completedUser()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function confirmedUser()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function latestComment()
    {
        return $this->morphOne(Comment::class, 'commentable')->latest();
    }

    public function resolveLineItem($value)
    {
        return reset($value);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'whse', 'short');
    }
}
