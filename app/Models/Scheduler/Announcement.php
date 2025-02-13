<?php

namespace App\Models\Scheduler;

use App\Models\Core\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'scheduler_announcements';
    protected $fillable = [
        'whse',
        'message',
        'created_by'
    ];


    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'whse', 'short');
    }
}
