<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailTemplate extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'order_email_templates';

    protected $fillable = [
        'account_id',
        'name',
        'email_content',
        'sms_content',
        'created_by',
        'is_active',
    ];

    public $hidden = [
        'account_id',
        'deleted_at',
        'created_by',
    ];
}
