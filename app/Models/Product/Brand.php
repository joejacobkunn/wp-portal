<?php

namespace App\Models\Product;

use App\Models\Equipment\Warranty\BrandWarranty;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory,Cachable;

    protected $table = 'product_brands';

    protected $fillable = ['name'];

    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id');
    }
    public function warranty(){
        return $this->hasMany(BrandWarranty::class);
    }
    public function productLines(){
        return $this->hasMany(Line::class,'brand_id');
    }
}
