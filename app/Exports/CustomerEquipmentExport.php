<?php

namespace App\Exports;

use App\Models\SRO\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\SRO\Equipment;
use Maatwebsite\Excel\Concerns\FromQuery;

class CustomerEquipmentExport implements FromQuery
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
        return Equipment::query()->where('customer_id', $this->customer->id)->whereIn('id',$this->equipment_array);
    }
}
