<?php

namespace App\Models\Scheduler;

use App\Models\Core\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TruckSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'truck_id',
        'zone_id',
        'schedule_date',
        'slots',
        'start_time',
        'end_time',
        'driver_id',
        'is_pickup',
        'is_delivery',
    ];

    public function truck()
    {
        return $this->belongsTo(Truck::class, 'truck_id');
    }

    public function zone()
    {
        return $this->belongsTo(Zones::class, 'zone_id');
    }

    public function orderSchedule()
    {
        return $this->hasMany(Schedule::class, 'truck_schedule_id');
    }

    public function getScheduleCountAttribute()
    {
        return $this->orderSchedule()->where('status', '!=', 'cancelled')->count();
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function scheduleType()
    {
        if($this->is_pickup) return 'pickup';
        if($this->is_delivery) return 'delivery';
        else return 'at_home_maintenance';
    }

}
