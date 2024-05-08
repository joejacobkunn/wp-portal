<?php

namespace App\Models\Product;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory,Cachable;

    protected $table = 'products';

    protected $guarded = ['id'];

    protected $casts = ['unit_sell' => 'array'];

    public function getDefaultUnitSellAttribute()
    {
        if (is_array($this->unit_sell) && !empty ($this->unit_sell)) {
            return current($this->unit_sell);
        }
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function line()
    {
        return $this->belongsTo(Line::class, 'product_line_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
