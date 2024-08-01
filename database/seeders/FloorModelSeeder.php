<?php

namespace Database\Seeders;

use App\Models\Core\Warehouse;
use App\Models\Equipment\FloorModelInventory\FloorModelInventory;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FloorModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $floor_plans = DB::connection('sx')->select("SELECT z.cono ,
                            z.primarykey AS 'whse' ,
                            z.secondarykey AS 'prod' ,
                            z.codeval[1] AS 'qty' ,
                            z.operinit ,
                            z.transdt ,
                            z.transtm
                        FROM   pub.sastaz z
                        WHERE  z.cono = 10
                        AND    z.codeiden = '*Floor Model Inventory'
                        AND    z.labelfl = 0 with(nolock)");

        foreach($floor_plans as $floor_plan)
        {
            FloorModelInventory::updateOrCreate(
                ['whse' => Warehouse::where('short', strtolower($floor_plan->whse))->first()->id, 'product' => $floor_plan->prod],
                ['qty' => $floor_plan->qty, 'sx_operator_id' => $floor_plan->operinit, 'created_at' => Carbon::parse($floor_plan->transdt.' '.substr_replace($floor_plan->transtm, ':', 2, 0))->toDateTimeString(), 'updated_at' => Carbon::parse($floor_plan->transdt.' '.substr_replace($floor_plan->transtm, ':', 2, 0))->toDateTimeString()]
            );
        }

    }
}
