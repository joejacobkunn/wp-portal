<?php

namespace App\Models\Equipment\Warranty;

use App\Models\Product\Brand;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BrandWarranty extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'warranty_brand_configurations';

    protected $fillable = ['brand_id', 'product_lines_id', 'registration_url','require_proof_of_reg'];
    protected $casts = [
        'product_lines_id' => 'array'];
    public function brand(){
        return $this->belongsTo(Brand::class);
    }
}
