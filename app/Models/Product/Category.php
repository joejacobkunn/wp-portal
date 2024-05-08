<?php

namespace App\Models\Product;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory,Cachable;

    protected $table = 'product_category';

    protected $fillable = ['name'];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}
