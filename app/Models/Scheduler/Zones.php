<?php

namespace App\Models\Scheduler;

use App\Enums\Scheduler\ScheduleTypeEnum;
use App\Models\Core\Comment;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zones extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;
    protected $fillable = [
        'whse_id',
        'name',
        'description',
        'is_active',
        'service'
    ];

    protected $casts = [
        'schedule_days' => 'array',
        'service' => ScheduleTypeEnum::class,

    ];

    const LOG_FIELD_MAPS = [

        'name' => [
            'field_label' => 'Zone Name',
        ],
        'description' => [
            'field_label' => 'Description',
        ],
        'is_active' => [
            'field_label' => 'Active',
            'resolve' => 'resolveActive'

        ],
        'service' => [
            'field_label' => 'Service'
        ]
    ];
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function resolveActive($value)
    {
        return $value ? 'YES' : 'NO';
    }

    public function zipcodes()
    {
        return $this->belongsToMany(Zipcode::class, 'zipcode_zone', 'zone_id', 'scheduler_zipcode_id');
    }

    public function truckSchedules()
    {
        return $this->hasMany(TruckSchedule::class, 'zone_id');
    }
}
