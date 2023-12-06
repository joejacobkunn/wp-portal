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
            'title' => 'Cedar Springs',
            'cono' => 10
        ],
        1 => [
            'short' => 'utic',
            'title' => 'Utica',
            'cono' => 10
        ],
        2 => [
            'short' => 'ann',
            'title' => 'Ann Arbor',
            'cono' => 10
        ],
        3 => [
            'short' => 'farm',
            'title' => 'Farmington Hills',
            'cono' => 10
        ],
        4 => [
            'short' => 'livo',
            'title' => 'Livonia',
            'cono' => 10
        ],
        5 => [
            'short' => 'rich',
            'title' => 'Richmond',
            'cono' => 40
        ],
        6 => [
            'short' => 'wate',
            'title' => 'Waterford',
            'cono' => 10
        ],
        7 => [
            'short' => 'mcdo',
            'title' => 'McDonough',
            'cono' => 40
        ]

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
                'title' => $warehouse['title'],
                'cono' => $warehouse['cono']
            ]);
        }
    }
}
