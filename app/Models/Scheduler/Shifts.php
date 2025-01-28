<?php

namespace App\Models\Scheduler;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shifts extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'scheduler_shifts';
    protected $fillable  = [
        'whse',
        'type',
        'shift',
    ];
    protected $casts = [
        'shift' => 'array',
    ];

}
