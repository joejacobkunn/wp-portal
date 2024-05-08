<?php

namespace App\Models\Product;

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
}
