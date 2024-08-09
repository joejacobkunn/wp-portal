<?php

namespace App\Models\Equipment;

use App\Models\Core\Comment;
use App\Models\Core\User;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnavailableUnit extends Model
{
    use HasFactory,SoftDeletes,LogsActivity;

    protected $table = 'unavailable_equipments';

    protected $fillable = ['cono', 'whse', 'possessed_by', 'product_code', 'product_name', 'serial_number', 'base_price', 'is_unavailable', 'current_location', 'hours'];

    const LOG_FIELD_MAPS = [
        'created_at' => [
            'field_label' => 'Entered At',
        ],
        'current_location' => [
            'field_label' => 'Current Location',
        ],
        'hours' => [
            'field_label' => 'Hours',
        ],
    ];

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function user() {
        return $this->belongsTo(User::class, 'possessed_by', 'unavailable_equipments_id');
    }

}
