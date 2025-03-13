<?php

namespace App\Models\Equipment\FloorModelInventory;

use App\Models\Core\User;
use App\Models\Core\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FloorModelInventoryNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'note',
        'user_id',
        'warehouse_short',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_short', 'short');
    }
}
