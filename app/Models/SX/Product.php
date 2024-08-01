<?php

namespace App\Models\SX;

use App\Models\Product\Brand;
use App\Models\Product\Category;
use App\Models\Product\Line;
use App\Models\Product\Vendor;
use App\Models\Scopes\WithnolockScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    public $connection = 'sx';

    protected $table = 'icsp';

    protected static function booted(): void
    {
        parent::boot();
        static::addGlobalScope(new WithnolockScope);
    }

    public function getMetaData()
    {
        return DB::connection('sx')->select("SELECT TOP 1
        upper(w.prod) 'Prod' ,
        upper(p.descrip[1] + ' ' + p.descrip[2]) 'Description' ,
        upper(p.lookupnm) 'LookupNm',
        upper(l.user3) 'Brand',
        w.arpvendno 'VendNo',
        upper(v.name) 'Vendor',
        upper(p.prodcat) 'ProdCat',
        upper(w.prodline) 'ProdLine' ,
        upper(p.statustype) 'Active',
        upper(w.statustype) 'Status',
        w.listprice 'ListPrice',
        u.normusage[18] AS 'Usage',
        w.enterdt 'EnterDt',
        w.lastinvdt 'LastSold',
        p.unitsell 'UnitSell'
    FROM
        pub.icsp p
    LEFT JOIN pub.icsw W 
    ON
        w.cono = p.cono
        AND w.whse = 'utic'
        AND w.prod = p.prod
    LEFT JOIN pub.icsl L 
    ON
        l.cono = w.cono
        AND l.whse = w.whse
        AND l.vendno = w.arpvendno
        AND l.prodline = w.prodline
    LEFT JOIN pub.apsv V 
    ON
        v.cono = w.cono
        AND v.vendno = w.arpvendno
    LEFT JOIN pub.icswu u 
    ON
        u.cono = w.cono
        AND u.prod = w.prod
        AND u.whse = w.whse
    WHERE
        p.cono = 10
        and p.prod = '".$this->prod."'
        WITH(nolock)");
    }



}
