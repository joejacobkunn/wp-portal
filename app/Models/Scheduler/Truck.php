<?php

namespace App\Models\Scheduler;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Core\Location;

class Truck extends Model
{
    use HasFactory;

    protected $table = 'trucks';

    protected $fillable = [
        'truck_name',
        'location_id',
        'vin_number',
        'model_and_make',
        'year',
        'color',
        'notes',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

}
