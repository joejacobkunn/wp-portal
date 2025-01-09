<?php

namespace Database\Seeders;

use App\Models\SalesRepOverride\SalesRepOverride;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class SalesRepOverrideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sales_rep_overrides =  DB::connection('sx')->select("SELECT
                                    z.cono ,
                                    z.codeiden ,
                                    z.primarykey AS 'arscskey' ,
                                    z.secondarykey AS 'prod_line' ,
                                    z.codeval[1] AS 'sales_rep' ,
                                    z.operinit AS 'change_by'
                                FROM
                                    pub.sastaz z
                                WHERE
                                    z.cono = 40
                                    AND z.codeiden = 'Sales Rep Override'
                                WITH(nolock)");


        foreach($sales_rep_overrides as $override)
        {
            $customer_info = explode('@', $override->arscskey);

            if(isset($customer_info[0]) && isset($customer_info[1]))
            {
                SalesRepOverride::updateOrCreate([
                    'customer_number' => $customer_info[0], 'ship_to' => $customer_info[1] ?? '', 'prod_line' => $override->prod_line
                ],[
                    'sales_rep' => strtolower($override->sales_rep),
                    'last_updated_by' => strtolower($override->change_by)
                ]);
            }
        }
    }
}
