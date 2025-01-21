<?php

namespace App\Models\Scheduler;

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
    ];

    public function truck()
    {
        return $this->belongsTo(Truck::class, 'truck_id');
    }

    public function zone()
    {
        return $this->belongsTo(Zones::class, 'zone_id');
    }
}
