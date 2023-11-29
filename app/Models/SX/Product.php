<?php

namespace App\Models\SX;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public $connection = 'sx';

    protected $table = 'icsp';

    protected $primaryKey = 'prod';

}
