<?php

namespace App\Models\SX;

use App\Models\Product\Brand;
use App\Models\Product\Category;
use App\Models\Product\Line;
use App\Models\Product\Vendor;
use App\Models\Scopes\WithnolockScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public $connection = 'sx';

    protected $table = 'icsp';

    protected $primaryKey = 'prod';

    protected static function booted(): void
    {
        parent::boot();
        static::addGlobalScope(new WithnolockScope);
    }



}
