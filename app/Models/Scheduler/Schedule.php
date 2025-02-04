<?php

namespace App\Models\Scheduler;

use App\Enums\Scheduler\ScheduleEnum;
use App\Enums\Scheduler\ScheduleStatusEnum;
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
        'line_item',
        'status',
        'service_address',
        'created_by',
        'schedule_type',
        'notes',
        'cancel_reason',
        'reschedule_reason',
        'sro_number',
        'cancelled_by',
        'cancelled_at',
        'completed_at',
        'completed_by',
        'confirmed_by',
        'confirmed_at'
    ];

    protected $casts = [
        'schedule_date' => 'date',
        'line_item' => 'array',
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

}
