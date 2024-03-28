<?php

namespace Database\Seeders;

use App\Models\Core\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public $warehouses = [
        0 => [
            'short' => 'ceda',
            'title' => 'Cedar Springs',
            'cono' => 10,
            'phone' => '6166962913'
        ],
        1 => [
            'short' => 'utic',
            'title' => 'Utica',
            'cono' => 10,
            'phone' => '5867317240'
        ],
        2 => [
            'short' => 'ann',
            'title' => 'Ann Arbor',
            'cono' => 10,
            'phone' => '7342398200'
        ],
        3 => [
            'short' => 'farm',
            'title' => 'Farmington Hills',
            'cono' => 10,
            'phone' => '2484713050'
        ],
        4 => [
            'short' => 'livo',
            'title' => 'Livonia',
            'cono' => 10,
            'phone' => '8007381388'
        ],
        5 => [
            'short' => 'rich',
            'title' => 'Richmond',
            'cono' => 40,
            'phone' => '8107272400'
        ],
        6 => [
            'short' => 'wate',
            'title' => 'Waterford',
            'cono' => 10,
            'phone' => '2486205258'
        ],
        7 => [
            'short' => 'mcdo',
            'title' => 'McDonough',
            'cono' => 40,
            'phone' => '8006242932'
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
                'cono' => $warehouse['cono'],
                'phone' => $warehouse['phone']
            ]);
        }
    }
}
