<?php

namespace App\Models\Scheduler;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SroEquipmentCategory extends Model
{
    use HasFactory;

    protected $fillable = ['sro_category_id', 'name'];
}
