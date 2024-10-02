<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $table = 'order_messages';

    protected $fillable = ['order_number', 'order_suffix', 'medium', 'contact', 'subject', 'content', 'status'];
}
