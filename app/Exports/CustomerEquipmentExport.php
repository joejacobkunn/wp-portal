<?php

namespace App\Exports;

use App\Models\SRO\Customer;
use App\Models\SRO\Equipment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerEquipmentExport implements FromQuery, WithHeadings
{
    protected Customer $customer;

    protected $equipment_array;

    public function __construct($customer, $equipment_array)
    {
        $this->customer = $customer;
        $this->equipment_array = $equipment_array;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        return Equipment::query()
            ->select(['id', 'sx_equipment_order_no', 'brand', 'model', 'type', 'serial_no', 'purchase_date', 'sales_rep'])
            ->where('customer_id', $this->customer->id)
            ->whereIn('id', $this->equipment_array);
    }

    public function headings(): array
    {
        return ['id', 'sx_equipment_order_no', 'brand', 'model', 'type', 'serial_no', 'purchase_date', 'sales_rep'];
    }
}
