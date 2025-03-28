<?php

namespace App\Models\Scheduler;

use App\Models\Core\Warehouse;
use App\Models\Product\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CargoConfigurator extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'truck_cargo_configurator';

    protected $fillable = [
        'product_category_id',
        'whse',
        'length',
        'width',
        'height',
        'weight',
        'sro_equipment_category_id'
    ];

    public function productCategory()
    {
        return $this->belongsTo(Category::class, 'product_category_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'whse', 'short');
    }

    public function sroEquipment()
    {
        return $this->belongsTo(SroEquipmentCategory::class, 'sro_equipment_category_id');
    }

}
