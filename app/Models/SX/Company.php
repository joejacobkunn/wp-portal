<?php

namespace App\Models\SX;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $connection = 'sx';

    protected $table = 'sasc';

    protected $primaryKey = 'cono';
}
