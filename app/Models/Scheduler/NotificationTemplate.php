<?php

namespace App\Models\Scheduler;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'scheduler_notification_templates';

    protected $fillable = [
        'email_subject',
        'email_content',
        'sms_content',
        'name',
        'slug',
        'description'
    ];
}
