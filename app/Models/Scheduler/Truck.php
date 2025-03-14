<?php

namespace App\Models\Scheduler;

use App\Enums\Scheduler\ScheduleTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Core\Location;
use App\Models\Core\User;
use App\Models\Core\Warehouse;
use App\Traits\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

class Truck extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $table = 'trucks';

    protected $fillable = [
        'truck_name',
        'vin_number',
        'model_and_make',
        'year',
        'color',
        'notes',
        'service_type',
        'shift_type',
        'height',
        'width',
        'length',
        'warehouse_short',
    ];

    protected $casts = [
        'service_type' => ScheduleTypeEnum::class,
    ];

    const DOCUMENT_COLLECTION = 'truck_image';

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_short', 'short');
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
