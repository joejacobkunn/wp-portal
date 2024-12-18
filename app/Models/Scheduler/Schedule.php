<?php

namespace App\Models\Scheduler;

use App\Enums\Scheduler\ScheduleEnum;
use App\Models\Core\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'sx_ordernumber',
        'order_number_suffix',
        'schedule_date',
        'schedule_time',
        'line_items',
        'status',
        'created_by'
    ];

    protected $casts = [
        'line_items' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
