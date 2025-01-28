<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Core\CalendarHoliday;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

     protected $holidayList = [
        [
            'label' => 'New Years Day',
            'value' => '1 Jan {YYYY}',
        ],
        [
            'label' => 'Memorial Day',
            'value' => 'Last Monday of May {YYYY}',
        ],
        [
            'label' => 'Independence Day',
            'value' => '1 JUL {YYYY}',
        ],
        [
            'label' => 'Labor Day',
            'value' => 'First Monday of September {YYYY}',
        ],
        [
            'label' => 'Thanksgiving Day',
            'value' => 'Fourth Thursday of November {YYYY}',
        ],
        [
            'label' => 'Christmas Day',
            'value' => '25 DEC {YYYY}',
        ],
     ];

    public function run(): void
    {
        foreach($this->holidayList as $holiday) {
            CalendarHoliday::updateOrCreate([
                'label' => $holiday['label'],
            ], [
                'label' => $holiday['label'],
                'date_value' => $holiday['value'],
                'custom' => $holiday['custom'] ?? 0,
            ]);
        }
    }
}
