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
            'SRO SX Number',
            'SRO Staus',
            'Equipment',
            'Serial Number',
            'Note',
            'ETA',
            'Created By',
            'Created Date/Time',
            'Parts Ready User',
            'Parts Ready Date/Time',
            'Completed User',
            'Completed Date/Time',
            'Cancelled User',
            'Cancelled Date/Time',
            'SRO Linked User',
            'SRO Linked At',
            'Tech In Progress By',
            'Tech In Progress Date/Time'
        ];
    }
}
