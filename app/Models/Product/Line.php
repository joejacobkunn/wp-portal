<?php

namespace App\Models\Product;

use App\Models\Equipment\Warranty\BrandConfigurator\BrandWarranty;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Line extends Model
{
    use HasFactory,Cachable;

    protected $table = 'product_lines';

    protected $fillable = ['name', 'brand_id'];

    public function products()
    {
        return $this->hasMany(Product::class, 'product_line_id');
    }

    public function warranties()
    {
        return $this->belongsToMany(BrandWarranty::class, 'brand_warranty_lines');
    }
}
