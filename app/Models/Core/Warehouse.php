<?php

namespace App\Models\Core;

use App\Models\Scheduler\Zones;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $table = 'warehouses';

    protected $guarded = ['id'];

    public function zones()
    {
        return $this->hasMany(Zones::class, 'whse_id');
    }
}
