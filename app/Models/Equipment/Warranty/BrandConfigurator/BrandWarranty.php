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

    protected $fillable = ['brand_id','account_id', 'alt_name', 'prefix'];

    public function brand(){
        return $this->belongsTo(Brand::class);
    }
}
