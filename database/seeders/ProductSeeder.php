<?php

namespace Database\Seeders;

use App\Models\Core\Account;
use App\Models\Product\Product;
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
        $recordsRemaining = true;
        $lookupsCompleted = 0;
        $chunkSize = 1000;

    while($recordsRemaining){
        $products = DB::connection('sx')->select($this->fetchProductQuery($chunkSize,$chunkSize*$lookupsCompleted));

        if(count($products) < $chunkSize){
            $recordsRemaining = false;
        }
        foreach($products as $product){
            if(!empty($product->Prod)){
                Product::updateOrCreate(
                    ['prod' => $product->Prod],
                    [
                        'account_id' => $account->id,
                        'prod' => $product->Prod ?? '',
                        'description' => $product->Description ?? '',
                        'look_up_name' => $product->LookupNm ?? '',
                        'brand' => $product->Brand ?? '',
                        'vend_no' => $product->VendNo ?? '',
                        'vendor' => $product->Vendor ?? '',
                        'category' => $product->ProdCat ?? '',
                        'product_line' => $product->ProdLine ?? '',
                        'active' => $this->translateActive($product->Active),
                        'status' => $this->translateStatus($product->Status),
                        'list_price' => $product->ListPrice ?? '',
                        'usage' => $product->Usage ?? '',
                        'entered_date' => $product->EnterDt ? Carbon::parse($product->EnterDt)->format('Y-m-d') : '',
                        'last_sold_date' => $product->LastSold ? Carbon::parse($product->LastSold)->format('Y-m-d') : ''
                    ]
                );
    
            }
        }

        $lookupsCompleted++;
        }
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
                w.lastinvdt 'LastSold'
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

}
