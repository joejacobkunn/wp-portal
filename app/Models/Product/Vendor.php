<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Vendor extends Model
{
    use HasFactory;

    protected $table = 'product_vendors';

    protected $fillable = ['name', 'vendor_number'];

    protected $appends = ['full_name'];

    public function products()
    {
        return $this->hasMany(Product::class, 'vendor_id');
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn (string $value, array $attributes) => $attributes['name'].'('.$attributes['vendor_number'].')',
        );
    }
}
