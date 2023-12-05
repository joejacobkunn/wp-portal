<?php

namespace Database\Seeders;

use App\Models\Product\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductWarehouseSeeder extends Seeder
{
    public $warehouses = [
        0 => [
            'short' => 'ceda',
            'title' => 'Chesterfield'
        ],
        1 => [
            'short' => 'utic',
            'title' => 'Utica'
        ],
        2 => [
            'short' => 'ann',
            'title' => 'Ann Arbor'
        ],
        3 => [
            'short' => 'farm',
            'title' => 'Farmington Hills'
        ],
        3 => [
            'short' => 'livo',
            'title' => 'Livonia'
        ],
        3 => [
            'short' => 'clark',
            'title' => 'Clarkston'
        ],
        3 => [
            'short' => 'ricj',
            'title' => 'Richmond'
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->warehouses as $warehouse) {
            Warehouse::updateOrCreate([
                'short' => $warehouse['short']
            ], [
                'short' => $warehouse['short'],
                'title' => $warehouse['title']
            ]);
        }
    }
}
