<?php

namespace App\Models\SRO;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairOrders extends Model
{
    use HasFactory;

    protected $connection = 'sro';

    protected $table = 'repair_orders';

    protected $casts = [
        'job_created_date' => 'date',
    ];

    public function equipment()
    {
        return $this->hasOne(Equipment::class, 'equipment_id');
    }
}
