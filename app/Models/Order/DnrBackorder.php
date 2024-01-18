<?php

namespace App\Models\Order;

use App\Models\Core\Comment;
use App\Enums\Order\BackOrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DnrBackorder extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'dnr_backorders';

    protected $fillable = [
        'cono',
        'order_number',
        'order_number_suffix',
        'whse',
        'status',
        'order_date',
        'stage_code',
        'sx_customer_number',
        'last_updated_by'
    ];

    protected $casts = [
        'order_date' => 'date',
        'status' => BackOrderStatus::class
    ];
    
    public function isPendingReview()
    {
        return $this->status == BackOrderStatus::PendingReview;
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
