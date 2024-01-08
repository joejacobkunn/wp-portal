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
            DB::connection('zxt')->table('pub.productsync')->insert([
                'cone' => 10,
                'icsc' => 1,
                'icsp' => 1,
                'whse' => 'all',
                'prod' => 'MX00-'.$i,
                'vendno' => rand(50000, 60000),
                'prodcat' => $faker->randomElement(['PART', 'EQUIPMENT', 'ACCESSORIES']),
                'prodline' => 'CC-P',
                'descrip' => 'Washer locking ring-type putty color 1/4',
                'standardpack' => 'EA',
                'weight' => 1,
                'length' => 2,
                'width' => 2,
                'height' => 4,
                'listprice' => $faker->randomFloat(2),
                'baseprice' => $faker->randomFloat(2),
                'costprice' => $faker->randomFloat(2),
                'pricecode' => '<5',
                'operinit' => 'mtm',
                'transdt' => date('Y/m/d'),
                'transtm' => '2040',
                'transproc' => 'pim'
            ]);
       }
    }
}

// INSERT INTO zxt.pub.productsync (cono,icsc,icsp,icsw,whse,prod,vendno,prodcat,prodline,descrip,standardpack,weight,LENGTH,width,height,listprice,baseprice,costprice,pricecode,operinit,transdt,transtm,transproc)
// VALUES ('10',1,1,1,'all','MM001-0010016',56500,'PART','CC-P','Washer locking ring-type putty color 1/4" ID','EA',1,2,2,2,1.25,1.15,0.9,'<5','mtm','01/03/24','2040','pim');
