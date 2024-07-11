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

class ProcessWarrantyRecords implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $records;
    public $warrantyImports;

    /**
     * Create a new job instance.
     */

     //pass warranty import instance in constructor
    public function __construct($records, $WarrantyImports)
    {
        $this->records = $records;
        $this->warrantyImports = $WarrantyImports;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (config('sx.mock'))
        {
            sleep(5);
            foreach($this->records as $row)
            {

            }
        }
        else
        {
            foreach($this->records as $row)
            {
                $brand = Brand::whereRaw('LOWER(name) = ?', [strtolower($row['brand'])])->first();

                $brand_config = BrandWarranty::where('brand_id', $brand->id)->first();

                SerializedProduct::where('cono', 10)
                    ->where('whseto', '')->where('currstatus', 's')
                    ->whereIn('prod',[$brand_config->prefix.$row['model'],strtolower($brand_config->prefix.$row['model']), strtoupper($brand_config->prefix.$row['model'])])
                    ->whereIn('serialno', [$row['serial'],strtolower($row['serial']), strtoupper($row['serial'])])
                    ->update(['user9' => date("m/d/y", strtotime($row['reg_date'])), 'user4', auth()->user()->sx_operator_id]);
            }
        }
    }
}
