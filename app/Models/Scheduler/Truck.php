<?php

namespace App\Models\Scheduler;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Core\Location;
use App\Models\Core\User;
use App\Models\Core\Warehouse;
use Illuminate\Database\Eloquent\SoftDeletes;

class Truck extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'trucks';

    protected $fillable = [
        'truck_name',
        'vin_number',
        'model_and_make',
        'year',
        'color',
        'notes',
        'whse',
        'cubic_storage_space',
        'baseline_date',
        'service_type',
        'shift_type',
    ];


    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'whse');
    }

    public function rotations()
    {
        return $this->hasMany(Rotation::class, 'truck_id');
    }

    public function schedules()
    {
        return $this->hasMany(TruckSchedule::class, 'truck_id');
    }
}
