<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class NotificationTemplate extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'order_notification_templates';

    protected $fillable = [
        'account_id',
        'name',
        'email_subject',
        'email_content',
        'sms_content',
        'created_by',
        'type',
        'is_active',
    ];

    public $hidden = [
        'account_id',
        'deleted_at',
        'created_by',
    ];

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', 1);
    }
}
