<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operator extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'operators';

    protected $fillable = ['cono', 'name', 'operator', 'email'];
}
