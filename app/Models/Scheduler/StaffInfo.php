<?php

namespace App\Models\Scheduler;

use App\Models\Core\User;
use App\Traits\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

class StaffInfo extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;
    protected $table = 'scheduler_staff_info';
    protected $fillable = ['user_id', 'description'];
    const DOCUMENT_COLLECTION = 'user_image';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
