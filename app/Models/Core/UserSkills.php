<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSkills extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'skills'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
