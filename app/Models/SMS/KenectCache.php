<?php

namespace App\Models\SMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KenectCache extends Model
{
    use HasFactory;
    protected $fillable = ["email", "phone", "user_id"];
}
