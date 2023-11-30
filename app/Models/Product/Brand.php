<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $table = 'product_brands';

    protected $fillable = ['name'];

    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id');
    }
}
