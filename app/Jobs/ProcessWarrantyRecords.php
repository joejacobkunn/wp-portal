<?php

namespace App\Jobs;

use App\Models\Equipment\Warranty\BrandConfigurator\BrandWarranty;
use App\Models\Product\Brand;
use App\Models\SX\SerializedProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;


class ProcessWarrantyRecords implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $records;
    public $warrantyImport;
    public $failedRecords = [];

    /**
     * Create a new job instance.
     */

     //pass warranty import instance in constructor
    public function __construct($records, $WarrantyImport)
    {
        $this->records = $records;
        $this->warrantyImport = $WarrantyImport;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->warrantyImport->update(['status' => 'processing']);

        if (config('sx.mock'))
        {
            
            foreach($this->records as $row)
            {
                $value = mt_rand(0,1);
                
                if($value)
                {
                    $this->warrantyImport->increment('processed_count');
                }else{
                    $this->failedRecords[] = $row;
                }
            }
        }
        else
        {
            foreach($this->records as $row)
            {
                $brand_config = BrandWarranty::whereHas('brand', function($q) use($row){
                    $q->where('name', 'like','%'.$row['brand'].'%');
                 })->first();

                $serialized_product = SerializedProduct::where('cono', 10)
                    ->whereIn('whse', ['wate','utic','ann','livo','ceda','farm'])
                    ->where('serialno', $row['serial'])
                    ->where('prod', 'like', $brand_config->prefix.'%')
                    ->where('whseto', '')
                    ->where('currstatus', 's')
                    ->first();

                if($serialized_product)
                {
                    DB::connection('sx')->statement("UPDATE pub.icses SET user9 = '".date("m/d/y", strtotime($row['reg_date']))."', user4 = '".$this->warrantyImport->uploader->sx_operator_id."' where cono = 10 AND whse IN('wate','utic','ann','livo','ceda','farm') and serialno = '".$row['serial']."' and prod like '".$brand_config->prefix."%' and whseto = '' and currstatus = 's'");

                    //$serialized_product->update(['user9' => date("m/d/y", strtotime($row['reg_date'])), 'user4' => $this->warrantyImport->uploader->sx_operator_id]);
                    $this->warrantyImport->increment('processed_count');
                }else{
                    $this->failedRecords[] = $row;
                }
                    
            }
        }

        $this->warrantyImport->update(['status' => 'complete']);
    }
}
