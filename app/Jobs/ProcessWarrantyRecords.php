<?php

namespace App\Jobs;

use App\Exports\WarrantyExport;
use App\Models\Equipment\Warranty\BrandConfigurator\BrandWarranty;
use App\Models\Product\Brand;
use App\Models\SX\SerializedProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

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
    public function __construct($records, $WarrantyImport, $faild)
    {
        $this->records = $records;
        $this->warrantyImport = $WarrantyImport;
        $this->failedRecords = $faild;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->warrantyImport->update(['status' => 'processing']);

        if (config('sx.mock'))
        {
            foreach($this->records as $key => $row)
            {
                $value = mt_rand(0,1);

                if($value)
                {
                    $this->warrantyImport->increment('processed_count');
                }else{
                    $this->failedRecords[] = $row;
                    unset($this->records[$key]);
                }
            }
        }
        else
        {
            foreach($this->records as $key => $row)
            {
                $brand_config = BrandWarranty::whereHas('brand', function($q) use($row){
                    $q->where('name', 'like',$row['brand'].'%');
                 })->orWhere('alt_name', 'like', $row['brand'].'%')->first();


                $serialized_product = SerializedProduct::where('cono', 10)
                    ->whereIn('whse', ['wate','utic','ann','livo','ceda','farm'])
                    ->where('serialno', trim($row['serial']))
                    ->where('prod', 'like', $brand_config->prefix.'%')
                    ->where('currstatus', 's')
                    ->whereRaw("custno <> ''")
                    ->first();

                if($serialized_product)
                {
                    DB::connection('sx')->statement("UPDATE pub.icses SET user9 = '".date("m/d/y", strtotime($row['reg_date']))."', user4 = '".$this->warrantyImport->uploader->sx_operator_id."' where cono = 10 AND whse IN('wate','utic','ann','livo','ceda','farm') and serialno = '".trim($row['serial'])."' and prod like '".$brand_config->prefix."%' and custno <> '' and currstatus = 's'");

                    $this->warrantyImport->increment('processed_count');
                }else{
                    $this->failedRecords[] = $row;
                    unset($this->records[$key]);
                }

            }
        }
        $failedFile = $this->saveRecords(config('warranty.failed_file_location'), $this->failedRecords);
        $processedFile = $this->saveRecords(config('warranty.valid_file_location'), $this->records);

        $this->warrantyImport->update([
            'status' => 'complete',
            'failed_records' => $failedFile,
            'valid_records' => $processedFile
        ]);
    }

    public function saveRecords($path, $records)
    {
        $recordPath =  $path. uniqid() . '.csv';
        if (!empty($records)) {
            $exportRecord = new WarrantyExport($records);
            Excel::store($exportRecord,  $recordPath, 'public');
            return $recordPath;
        }
        return null;
    }
}
