<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SXAccount extends Model
{
    use HasFactory;

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
