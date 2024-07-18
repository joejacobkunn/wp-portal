<?php

namespace App\Models\SMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KenectCache extends Model
{
    use HasFactory;
    protected $fillable = ["first_name", "location_id", "last_name", "email", "phone", "user_id"];
}
