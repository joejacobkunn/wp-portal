<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Product\Product;


class SyncProductSupercedes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sx:sync-product-supercedes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to sync supersedes from SX to mysql';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Product::latest()->chunk(500, function ($products) {
            foreach ($products as $product) {
                echo 'Checking product '.$product->prod;
                $existing_supersedes = $product->supersedes;
                $updated_supersedes = [];
                $prod = $product->prod;
                
                do{

                    $supersede = $this->fetchSupersede($prod);

                    if(!empty($supersede)){
                        $updated_supersedes[] = $supersede;
                        $prod = $supersede;
                    }

                }while($supersede);

                if($existing_supersedes != $updated_supersedes){
                    $product->update(['supersedes' => $updated_supersedes]);
                }
            }
        });
    }

    private function fetchSupersede($prod)
    {
        $supersede = DB::connection('sx')->select("SELECT top 1 altprod 
                FROM pub.icsec c
                WHERE c.cono = 10
                AND c.rectype = 'p'
                AND c.prod = '".$prod."'
                with(nolock)");

        if(empty($supersede)) return false;

        return $supersede[0]->altprod;
    }

}
