<?php

namespace App\Models\Equipment\FloorModelInventory;

use App\Models\Core\Comment;
use App\Models\Core\Operator;
use App\Models\Core\Warehouse;
use App\Models\Product\Product;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FloorModelInventory extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['whse','product','qty','sx_operator_id', 'created_at', 'updated_at'];
    protected $table = 'floor_model_inventory';
    const LOG_FIELD_MAPS = [

        'product' => [
            'field_label' => 'Product',
        ],
        'whse' => [
            'field_label' => 'WareHouse',
            'resolve' => 'resolveWarehouse'
        ],
        'qty' => [
            'field_label' => 'Quantity',
        ]
    ];
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'whse');
    }

    public function products()
    {
        return $this->belongsTo(Product::class, 'product', 'prod');
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class,'sx_operator_id', 'operator');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function resolveWarehouse($value)
    {
      $warehouse = Warehouse::find($value);
      return $warehouse->title.' ('.$warehouse->short.')' ?? 'N/A';
    }
}
