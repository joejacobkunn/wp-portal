<?php

namespace App\Models\Equipment\FloorModelInventory;

use App\Models\Core\Warehouse;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FloorModelInventory extends Model
{
    use HasFactory;

    protected $fillable = ['whse','product','qty','sx_operator_id'];
    protected $table = 'floor_model_inventory';

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'whse');
    }

    public function products()
    {
        return $this->belongsTo(Product::class, 'product');
    }
}
