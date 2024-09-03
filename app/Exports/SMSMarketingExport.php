<?php

namespace App\Exports;

use App\Models\SMSMarketing;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SMSMarketingExport implements FromArray, WithHeadings
{
    protected $rows;

    public function __construct(array $rows)
    {
        $this->rows = array_map(function($row) {
            return array_intersect_key($row, array_flip(['phone', 'message', 'office', 'assignee']));
        }, $rows);
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return ['PHONE', 'MESSAGE', 'OFFICE', 'ASSIGNEE'];
    }
}
