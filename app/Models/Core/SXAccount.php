<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SXAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sx_accounts';

    protected $fillable = [
        'name',
        'cono',
        'address',
        'city',
        'state',
        'zip',
        'phoneno',
    ];
}
