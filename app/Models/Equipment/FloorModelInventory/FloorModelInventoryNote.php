<?php

namespace App\Models\Equipment\FloorModelInventory;

use App\Models\Core\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FloorModelInventoryNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'note',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
