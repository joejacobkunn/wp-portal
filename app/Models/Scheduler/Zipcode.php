<?php

namespace App\Models\Scheduler;

use App\Models\Core\Comment;
use App\Models\ZipCode as GeneralZipcode;
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
        'delivery_rate',
        'pickup_rate',
        'notes',
        'is_active'
    ];

    const LOG_FIELD_MAPS = [

        'zip_code' => [
            'field_label' => 'Zip Code',
        ],
        'service' => [
            'field_label' => 'Service',
            'resolve' => 'resolveService'
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

    public function resolveActive($value)
    {
        return $value ? 'Yes' : 'No';
    }

    public function resolveService($value)
    {
        return implode(",", $value);
    }

    public function generalZipcode()
    {
        return $this->hasOne(GeneralZipcode::class, 'zipcode', 'zip_code');
    }

    public function zones()
    {
        return $this->belongsToMany(Zones::class, 'zipcode_zone', 'scheduler_zipcode_id', 'zone_id');
    }
}
