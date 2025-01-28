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
            'phone' => '6166962913',
            'address' => '11875 Northland Dr, Cedar Springs, MI 49319'
        ],
        1 => [
            'short' => 'utic',
            'title' => 'Utica',
            'cono' => 10,
            'phone' => '5867317240',
            'address' => '46061 Van Dyke Ave, Utica, MI 48317'
        ],
        2 => [
            'short' => 'ann',
            'title' => 'Ann Arbor',
            'cono' => 10,
            'phone' => '7342398200',
            'address' => '5436 Jackson Rd, Ann Arbor, MI 48103'
        ],
        3 => [
            'short' => 'farm',
            'title' => 'Farmington Hills',
            'cono' => 10,
            'phone' => '2484713050',
            'address' => '39050 Grand River Ave, Farmington Hills, MI 48335'
        ],
        4 => [
            'short' => 'livo',
            'title' => 'Livonia',
            'cono' => 10,
            'phone' => '8007381388',
            'address' => '32098 Plymouth Rd, Livonia, MI 48150'
        ],
        5 => [
            'short' => 'rich',
            'title' => 'Richmond',
            'cono' => 40,
            'phone' => '8107272400',
            'address' => '69250 Burke Dr, Richmond, MI 48062'
        ],
        6 => [
            'short' => 'wate',
            'title' => 'Clarkston',
            'cono' => 10,
            'phone' => '2486205258',
            'address' => '6585 Dixie Hwy, Clarkston, MI 48346'
        ],
        7 => [
            'short' => 'mcdo',
            'title' => 'McDonough',
            'cono' => 40,
            'phone' => '8006242932',
            'address' => '500 Jerry Steele Ln, McDonough, GA 30253'
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
                'title' => $warehouse['title'],
                'cono' => $warehouse['cono'],
                'phone' => $warehouse['phone'],
                'address' => $warehouse['address']
            ]);
        }
    }
}
