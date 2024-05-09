<?php

namespace App\Models\Equipment;

use App\Models\Core\Comment;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnavailableUnit extends Model
{
    use HasFactory,SoftDeletes,LogsActivity;

    protected $table = 'unavailable_equipments';

    protected $fillable = ['cono', 'whse', 'possessed_by', 'product_code', 'product_name', 'serial_number', 'base_price', 'is_unavailable', 'current_location'];

    const LOG_FIELD_MAPS = [
        'created_at' => [
            'field_label' => 'Entered At',
        ],
        'current_location' => [
            'field_label' => 'Current Location',
        ],
    ];

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }



}
