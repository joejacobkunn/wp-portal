<?php

namespace App\Models\Scheduler;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'sx_ordernumber',
        'schedule_date',
        'schedule_time',
        'line_items',
        'status',
        'created_by'
    ];

    protected $casts = [
        'line_items' => 'array',
    ];
}
