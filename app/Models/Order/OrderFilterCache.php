<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderFilterCache extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'filters', 'status'];

    protected $casts = [
        'filters' => 'array'
    ];
}
