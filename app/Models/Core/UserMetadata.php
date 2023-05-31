<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserMetadata extends Model
{
    use SoftDeletes;

    protected $table = 'user_metadata';

    protected $fillable = [
        'user_id',
        'invited_by',
        'user_token',
    ];

    protected $hidden = [
        'deleted_at',
        'user_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
