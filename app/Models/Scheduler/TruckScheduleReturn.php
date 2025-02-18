<?php

namespace App\Models\Scheduler;

use App\Models\Core\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TruckScheduleReturn extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'whse',
        'truck_id',
        'schedule_date',
        'expected_arrival_time',
        'last_scheduled_address',
        'distance',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'whse', 'short');
    }

    public function truck()
    {
        return $this->belongsTo(Truck::class, 'truck_id');
    }
}
