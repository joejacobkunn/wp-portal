<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WarrantyExport implements FromArray, WithHeadings
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
        return ['Brand', 'Model', 'Serial', 'Reg Date'];
    }
}
