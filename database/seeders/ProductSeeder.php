<?php

namespace Database\Seeders;

use App\Models\Core\Account;
use App\Models\Product\Brand;
use App\Models\Product\Category;
use App\Models\Product\Line;
use App\Models\Product\Product;
use App\Models\Product\Vendor;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $account = Account::where('subdomain', 'weingartz')->first();

        DB::connection()->getPdo()->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);

        echo "Starting Products...\n";
        $recordsRemaining = true;
        $lookupsCompleted = 0;
        $chunkSize = 1000;

    while($recordsRemaining){
        $products = DB::connection('sx')->select($this->fetchProductQuery($chunkSize,$chunkSize*$lookupsCompleted));

        $batch_products = [];

        if(count($products) < $chunkSize){
            $recordsRemaining = false;
        }
        
        foreach($products as $product){
            if(!empty($product->Prod)){

                $brand = Brand::without(['products' ])->where('name', $product->Brand ?: 'Unknown')->first();

                //fetch or create line
                $line = Line::firstOrCreate([
                    'name' => $product->ProdLine ?? 'Unknown',
                    'brand_id' => $brand->id
                ]);


                $batch_products[] = [
                    'account_id' => $account->id,
                    'prod' => trim($product->Prod),
                    'description' => $product->Description ?? '',
                    'look_up_name' => $product->LookupNm ?? '',
                    'brand_id' => $brand->id ?? '',
                    'vendor_id' => Vendor::without(['products' ])->where('vendor_number',$product->VendNo ?: 0)->first()->id ?? '',
                    'category_id' => Category::without(['products' ])->where('name',$product->ProdCat ?: 'Unknown')->first()->id ?? '',
                    'product_line_id' => $line->id ?? '',
                    'active' => $this->translateActive($product->Active),
                    'status' => $this->translateStatus($product->Status),
                    'list_price' => $product->ListPrice ?? '',
                    'usage' => $product->Usage ?? '',
                    'entered_date' => $product->EnterDt ? Carbon::parse($product->EnterDt)->format('Y-m-d') : '',
                    'last_sold_date' => $product->LastSold ? Carbon::parse($product->LastSold)->format('Y-m-d') : '',
                    'unit_sell' => json_encode([$product->UnitSell ?: 'EA']),
                ];
    
            }
        }

        Product::upsert($batch_products, ['prod'], ['last_sold_date', 'active', 'status']);
        


        $lookupsCompleted++;
        }
        DB::connection()->getPdo()->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
    }

    private function fetchProductQuery($limit,$offset)
    {
        return "SELECT
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
        OFFSET ".$offset." ROWS 
        FETCH NEXT ".$limit." ROWS ONLY
        WITH(nolock)";
    }

    private function translateActive($char)
    {
        if(empty($char)) return 'N/A';

        $sx_active_values = [
            'A' => 'active', 
            'I' => 'inactive',
            'L' => 'labor', 
            'S' => 'supercede' 
        ];

        return $sx_active_values[$char];
    }

    private function translateStatus($char)
    {
        if(empty($char)) return 'N/A';

        $sx_status_values = [
            'D' => 'direct ship', 
            'O' => 'order as needed',
            'S' => 'stock', 
            'X' => 'do not reorder' 
        ];

        return $sx_status_values[$char];
    }

    private function getUnitsForProduct($prod, $default_unit)
    {
        $data = [$default_unit];
        $units = DB::connection('sx')->select("select units 
                    FROM pub.icseu u
                    where u.cono = 10
                    AND UPPER(u.prod) = '".strtoupper($prod)."'
                    with(nolock)");

        if(empty($default_unit) && empty($units)) return ['EA'];



        foreach($units as $unit)
        {
            $data[] = $unit->units;
        }

        return array_unique($data);
    }




}
