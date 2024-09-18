<?php

namespace App\Models\Equipment\Warranty;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Report extends Model
{
    use \Sushi\Sushi;

    public function getRows()
    {
        $rows = [];
        $data = DB::connection('zxt')->select($this->constructQuery());

        foreach($data as $row){
            $rows[] = (array)$row;
        }

        return $rows;
    }

    protected function sushiShouldCache()
    {
        return false;
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
            ,REPLACE(icsp.descrip,';',' ') AS 'Description'
            ,icses.invoicedt AS 'Sold'
            ,icses.user9 AS 'registration_date'
            ,icses.user4 AS 'registered_by'
            ,arsc.custno AS 'Customer#'
            ,UPPER(arsc.name) AS 'Name'
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
            AND icses.invoicedt > '".Carbon::now()->subMonth(12)->format('Y-m-d')."' WITH(NOLOCK)";
    }

}
