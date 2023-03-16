<?php

namespace Database\Seeders;

use App\Models\Core\Module;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            [
                'name' => 'Orders',
                'description' => 'Integrates SX to account and lets users see SX Orders'
            ],
            [
                'name' => 'Vehicles',
                'description' => 'Enables Vehicle Management and Inspections'
            ],
            [
                'name' => 'POS',
                'description' => 'Enables Fortis Point of Sale Management'
            ]

            ];

            foreach ($modules as $module) {
                Module::updateOrCreate(['name' => $module['name']], $module);
            }
    }
}
