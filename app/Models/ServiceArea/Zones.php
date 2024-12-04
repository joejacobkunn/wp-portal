<?php

namespace App\Models\ServiceArea;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zones extends Model
{
    use HasFactory;
    protected $fillable = [
        'whse_id',
        'name',
        'description',
        'schedule_days',
    ];

    protected $casts = [
        'schedule_days' => 'array',
    ];
}
