<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductTempSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

       $faker = \Faker\Factory::create();

       for($i=0; $i<150000;$i++)
       {
        $product_name = 'MX-001-'.$i;

        DB::connection('zxt')->statement("INSERT INTO pub.productSync (cono,icsc,icsp,icsw,whse,prod,vendno,prodcat,prodline,descrip,standardpack,weight,LENGTH,width,height,listprice,baseprice,costprice,pricecode,operinit,transdt,transtm,transproc)
        VALUES ('10',1,1,1,'all','".$product_name."',56500,'PART','CC-P','Washer locking ring-type putty color ID','EA',1,2,2,2,1.25,1.15,0.9,'<5','mtm','01/03/24','2040','pim')");

       }
    }
}

// INSERT INTO zxt.pub.productsync (cono,icsc,icsp,icsw,whse,prod,vendno,prodcat,prodline,descrip,standardpack,weight,LENGTH,width,height,listprice,baseprice,costprice,pricecode,operinit,transdt,transtm,transproc)
// VALUES ('10',1,1,1,'all','MM001-0010016',56500,'PART','CC-P','Washer locking ring-type putty color 1/4" ID','EA',1,2,2,2,1.25,1.15,0.9,'<5','mtm','01/03/24','2040','pim');
