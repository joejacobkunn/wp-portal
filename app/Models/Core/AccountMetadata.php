<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountMetadata extends Model
{
    use SoftDeletes;

    protected $table = 'account_metadata';

    protected $fillable = [
        'account_id',
        'created_by',
    ];

    protected $hidden = [
        'deleted_at',
    ];
}
