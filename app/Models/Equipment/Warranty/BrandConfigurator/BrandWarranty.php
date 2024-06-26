<?php

namespace App\Models\Equipment\Warranty\BrandConfigurator;

use App\Models\Product\Brand;
use App\Models\Product\Line;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BrandWarranty extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'warranty_brand_configurations';

    protected $fillable = ['brand_id', 'product_lines_id', 'registration_url','require_proof_of_reg'];

    public function brand(){
        return $this->belongsTo(Brand::class);
    }

    public function productLines()
    {
        return $this->belongsToMany(Line::class, 'brand_warranty_lines', 'brand_warranty_id', 'product_line_id');
    }
}
