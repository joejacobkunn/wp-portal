<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Line extends Model
{
    use HasFactory;

    protected $table = 'product_lines';

    protected $fillable = ['name', 'brand_id'];

    public function products()
    {
        return $this->hasMany(Product::class, 'product_line_id');
    }
}
