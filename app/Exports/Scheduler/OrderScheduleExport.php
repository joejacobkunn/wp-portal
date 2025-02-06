<?php

namespace App\Exports\Scheduler;

use App\Models\SMSMarketing;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderScheduleExport implements FromArray, WithHeadings
{
    protected $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'ID',
            'SX Order No',
            'Schedule Date',
            'Time Slot',
            'Type',
            'Zone',
            'Truck',
            'Status',
            'Customer',
            'SX Customer No',
            'Shipping Address Line 1',
            'Address Line 2',
            'City',
            'State',
            'Zip Code',
        ];
    }
}
