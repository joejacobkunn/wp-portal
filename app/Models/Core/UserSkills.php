<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSkills extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'skills'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
