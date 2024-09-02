<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderExport implements FromArray, WithHeadings
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
            'id',
            'cono',
            'order_number',
            'order_number_suffix',
            'whse',
            'taken_by',
            'is_dnr',
            'order_date',
            'promise_date',
            'warehouse_transfer_available',
            'partial_warehouse_transfer_available',
            'wt_transfers',
            'is_web_order',
            'last_line_added_at',
            'golf_parts',
            'non_stock_line_items',
            'is_sro',
            'last_followed_up_at',
            'ship_via',
            'line_items',
            'is_sales_order',
            'qty_ship',
            'qty_ord',
            'stage_code',
            'dnr_items',
            'sx_customer_number',
            'status',
            'last_updated_by'
        ];
    }
    public function chunkSize(): int
    {
        return 1000; // Adjust the chunk size as needed
    }
}
