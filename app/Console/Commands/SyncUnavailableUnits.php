<?php

namespace App\Console\Commands;

use App\Models\Equipment\UnavailableUnit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncUnavailableUnits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sx:sync-unavailable-units';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to fetch and sync demo/unavailable units to mysql database';

    /**
 * Execute the console command.
     */
    public function handle()
    {
        $units = DB::connection('sx')->select("select cono, whse, prod, reasunavty from pub.icsou where cono = 40 and whse in ('mcdo', 'rich') with (nolock)");
        $unavailable_ids = [];
        
        foreach($units as $unit)
        {
            $icsw = DB::connection('sx')->select("select top 1 * from pub.icsw where cono = ? and whse = ? and prod = ? with (nolock)", [40, $unit->whse, $unit->prod]);
            $icses = DB::connection('sx')->select("select top 1 * from pub.icses where cono = ? and whse = ? and prod = ? and currstatus = ?  and reasunavty = ? with (nolock)", [40, $unit->whse, $unit->prod, 'U', $unit->reasunavty]);
            //strtolower($icsw[0]->serlottype) == 's'
                //$sasta = DB::connection('sx')->select("select top 1 * from pub.sasta where cono = ? and codeiden = ? AND codeval = ? with (nolock)", [40, 'l',$unit->reasunavty]);
            $icsp = DB::connection('sx')->select("select top 1 * from pub.icsp where cono = ? and prod = ? with (nolock)", [40, $unit->prod]);

            $serial_no = (!empty($icses)) ? trim($icses[0]->serialno) : '';

            $unavailable_unit = UnavailableUnit::updateOrCreate(
                ['cono' => 40, 'whse' => $unit->whse, 'possessed_by' => strtolower($unit->reasunavty), 'product_code' => $unit->prod, 'serial_number' => $serial_no],
                [
                    'cono' => 40,
                    'whse' => strtolower($unit->whse),
                    'possessed_by' => strtolower($unit->reasunavty),
                    'product_code' => $unit->prod,
                    'product_name' => explode(";",$icsp[0]->descrip)[0].' '.explode(";",$icsp[0]->descrip)[1],
                    'serial_number' => (!empty($icses)) ? trim($icses[0]->serialno) : '',
                    'base_price' => $icsw[0]->baseprice,
                    'is_unavailable' => 1
                ]
            );

            $unavailable_ids[] = $unavailable_unit->id;    

        }

        //now make sure avaialable products are in table are synced

        $unavailable_equipments = UnavailableUnit::all();

        foreach($unavailable_equipments as $equipment)
        {
            if(!in_array($equipment->id,$unavailable_ids))
            {
                $equipment->update(['is_unavailable' => 0]);
            }
        }
    }
}