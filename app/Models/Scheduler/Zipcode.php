<?php

namespace App\Models\Scheduler;

use App\Models\Core\Comment;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zipcode extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'scheduler_zipcodes';
    protected $fillable = [
        'whse_id',
        'zip_code',
        'service',
        'zone',
        'delivery_rate',
        'pickup_rate',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'service' => 'array',
    ];

    const LOG_FIELD_MAPS = [

        'zip_code' => [
            'field_label' => 'Zip Code',
        ],
        'description' => [
            'field_label' => 'Service',
        ],
        'zone' => [
            'field_label' => 'Zone',
        ],
        'delivery_rate' => [
            'field_label' => 'Delivery Rate',
        ],
        'pickup_rate' => [
            'field_label' => 'Pickup Rate',
        ],
        'notes' => [
            'field_label' => 'Notes',
        ],
        'is_active' => [
            'field_label' => 'Active',
            'resolve' => 'resolveActive'
        ],
    ];

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function getZone()
    {
        return $this->belongsTo(Zones::class, 'zone');
    }

    public function resolveActive($value)
    {
        return $value ? 'Yes' : 'No';
    }

}
