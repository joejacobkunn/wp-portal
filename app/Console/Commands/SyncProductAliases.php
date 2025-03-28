<?php

namespace App\Console\Commands;

use App\Models\Product\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncProductAliases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sx:sync-product-aliases';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to sync product aliases from icsec to local products table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::connection()->getPdo()->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
        echo "Starting Aliases...\n";
        $recordsRemaining = true;
        $lookupsCompleted = 0;
        $chunkSize = 1000;
        $count = 0;

        while($recordsRemaining){
            $aliases = DB::connection('sx')->select($this->fetchAliasQuery($chunkSize,$chunkSize*$lookupsCompleted));
            
            if(count($aliases) < $chunkSize){
                $recordsRemaining = false;
            }

            foreach($aliases as $alias)
            {
                $count++;
                echo "alias count is ".$count;
                $product = Product::where('prod', trim($alias->altprod))->first();

                if($product)
                {
                    echo "Product found ".trim($alias->altprod);
                    $product_aliases = $product->aliases;

                    if(!is_null($product_aliases))
                    {
                        array_push($product_aliases,$alias->prod);

                        $product->update(['aliases' => array_unique($product_aliases)]);
    
                    }

                }else{
                    echo "Product not found ".trim($alias->altprod);
                }
            }

            $lookupsCompleted++;
    
    
        }

        DB::connection()->getPdo()->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
    }

    private function fetchAliasQuery($limit,$offset)
    {
        return "SELECT prod,altprod FROM pub.icsec where cono = 10 and rectype = 'i' OFFSET ".$offset." ROWS 
                    FETCH NEXT ".$limit." ROWS ONLY WITH(nolock)";
    }
}
