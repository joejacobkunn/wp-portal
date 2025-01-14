<?php

namespace App\Models\Scheduler;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rotation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'truck_rotations';

    protected $fillable = [
        'truck_id',
        'zone_id',
        'sort_order',
    ];

    public function truck()
    {
        return $this->belongsTo(Truck::class, 'truck_id');
    }

    public function zone()
    {
        return $this->hasOne(Zones::class, 'id', 'zone_id');
    }
}
