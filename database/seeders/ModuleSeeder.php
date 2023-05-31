<?php

namespace Database\Seeders;

use App\Models\Core\Module;
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
                'name' => 'Customers',
                'slug' => 'customers',
                'description' => 'Displays customer information from sx',
            ],

            [
                'name' => 'Orders',
                'slug' => 'orders',
                'description' => 'Integrates SX to account and lets users see SX Orders',
            ],
            [
                'name' => 'Vehicles',
                'slug' => 'vehicles',
                'description' => 'Enables Vehicle Management and Inspections',
            ],
            [
                'name' => 'POS',
                'slug' => 'pos',
                'description' => 'Enables Fortis Point of Sale Management',
            ],
            [
                'name' => 'HeroHub',
                'slug' => 'herohub',
                'description' => 'Enables HeroHub integration for Orders and Shipping',
            ],
        ];

        foreach ($modules as $module) {
            Module::updateOrCreate(['name' => $module['name']], $module);
        }
    }
}
