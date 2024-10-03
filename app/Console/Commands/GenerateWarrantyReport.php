<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GenerateWarrantyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sx:generate-warranty-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to generate warranty report and cache in json for reporting';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $rows = [];
        $non_registered_count = 0;
        $data = DB::connection('zxt')->select($this->constructQuery());

        foreach($data as $row){
            if(is_null($row->registration_date)) $non_registered_count++;
            $rows[] = (array)$row;
        }

        Storage::disk('public')->put('reports/warranty-report.json', json_encode($rows));

        //store last time in Cache

        Cache::put('warranty_registration_report_sync_timestamp', now());
        Cache::put('warranty_registration_non_registered_count', $non_registered_count);

    }

    private function constructQuery()
    {
        return "SELECT LTRIM(RTRIM(UPPER(icses.whse))) AS 'store'
            ,CAST(icses.invno AS VARCHAR(8)) + '-' + (
                CASE 
                    WHEN icses.invsuf <= 9 THEN '0' + CAST(icses.invsuf AS NCHAR(1))
                    ELSE CAST(icses.invsuf AS NCHAR(2))
                    END
                ) AS 'order_no'
            ,UPPER(icsl.user3) AS 'brand'
            ,icsl.prodline AS 'prodline'
            ,UPPER(icses.prod) AS 'model'
            ,LTRIM(RTRIM(UPPER(icses.serialno))) AS 'serial'
            ,REPLACE(icsp.descrip,';',' ') AS 'description'
            ,icses.invoicedt AS 'sold'
            ,icses.user9 AS 'registration_date'
            ,icses.user4 AS 'registered_by'
            ,arsc.custno AS 'cust_no'
            ,UPPER(arsc.name) AS 'customer_name'
        FROM pub.icses
        LEFT JOIN pub.arsc
            ON arsc.cono = icses.cono
            AND arsc.custno = icses.custno
        LEFT JOIN pub.icsp
            ON icsp.cono = icses.cono
            AND icsp.prod = icses.prod
        LEFT JOIN pub.icsw
            ON icsw.cono = icses.cono
            AND icsw.prod = icses.prod
            AND icsw.whse = icses.whse
        LEFT JOIN pub.icsl
            ON icsl.cono = icsw.cono
            AND icsl.whse = icsw.whse
            AND icsl.vendno = icsw.arpvendno
            AND icsl.prodline = icsw.prodline
        WHERE icses.cono = 10
            AND icses.whse IN ('ANN','CEDA','FARM','LIVO','UTIC','WATE')
            AND icses.currstatus = 's'
            AND icses.invno <> 0
            AND icses.invoicedt IS NOT NULL
            AND icses.invoicedt > '".Carbon::now()->subMonth(24)->format('Y-m-d')."' WITH(NOLOCK)";
    }

}
