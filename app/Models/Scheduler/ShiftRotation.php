<?php

namespace App\Models\Scheduler;

use App\Models\Scheduler\Shifts;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShiftRotation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'truck_rotation_shifts';

    protected $fillable = [
        'truck_id',
        'zone_id',
        'shift_id',
        'scheduled_date',
    ];

    public function truck()
    {
        return $this->belongsTo(Truck::class, 'truck_id');
    }

    public function zone()
    {
        return $this->belongsTo(Zones::class, 'id', 'zone_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shifts::class, 'id', 'shift_id');
    }
}
