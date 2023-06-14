<?php

namespace App\Models\SRO;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $connection = 'sro';

    protected $table = 'equipment';

    protected $casts = ['purchase_date' => 'date'];
}
