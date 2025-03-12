<?php

namespace Database\Seeders;

use App\Models\Core\Warehouse;
use App\Models\Scheduler\Zipcode;
use App\Models\Scheduler\Zones;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class SchedulerAhmZoneZipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public $warehouses;

    public function run(): void
    {
        $this->warehouses = Warehouse::where('cono',10)->get();

        $reader = Reader::createFromPath(base_path() . '/database/seeders/seeds/scheduler-ahm-zone-zip-data.csv', 'r');

        $reader->setHeaderOffset(0);
        $records = $reader->getRecords();
        foreach ($records as $offset => $record) {
            $warehouse = $this->getWarehouse($record['zone']);
            $zone = Zones::updateOrCreate(['name' => $record['zone']],['service' => 'ahm', 'whse_id' => $warehouse->id, 'is_active' => 1]);
            $zip_code = Zipcode::updateOrCreate(['zip_code' => $record['zip']],['whse_id' => $warehouse->id, 'delivery_rate' => str_replace('$','',$record['delivery_rate']), 'pickup_rate' => str_replace('$','',$record['pickup_rate']), 'is_active' => 1]);
            $zone->zipcodes()->attach($zip_code->id);
        }

    }

    private function getWarehouse($zoneName)
    {
        foreach($this->warehouses as $warehouse)
        {
            if(str_contains(strtolower($zoneName),strtolower($warehouse->title)))
            {
                return $warehouse;
            }
        }
    }
}
