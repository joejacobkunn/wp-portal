<?php

namespace App\Exports;

use App\Models\SRO\Customer;
use App\Models\SRO\Equipment;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerEquipmentExport implements FromQuery, WithHeadings,WithMapping
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
        return ['id', 'sx_equipment_order_no', 'brand', 'model', 'type', 'serial_no', 'purchase_date', 'sales_rep', '7yepp status', '7yepp year', '7yepp last service'];
    }

    public function map($equipment): array
    {
        $seven_year_status = $this->SevenYeppStatus($equipment->model,$equipment->serial_no);

        return [
            $equipment->id,
            $equipment->sx_equipment_order_no,
            $equipment->brand,
            $equipment->model,
            $equipment->type,
            $equipment->serial_no,
            $equipment->purchase_date,
            $equipment->sales_rep,
            $seven_year_status['status'],
            $seven_year_status['year'],
            $seven_year_status['last_service'],
        ];
    }

    private function SevenYeppStatus($model_number, $serial_number)
    {
        $status = DB::connection('sx')->select("SELECT
                                                    CASE
                                                    WHEN s.user5 = 'Y' THEN 'Active'
                                                    ELSE 'Inactive'
                                                    END AS 'YEPP_Status',
                                                    s.user6 AS 'YEPP_Year',
                                                    s.user8 AS 'YEPP_LastService'
                                                    FROM pub.icses s
                                                    WHERE s.cono = 10
                                                    AND s.prod = '".$model_number."'
                                                    AND s.serialno = '".$serial_number."'
                                                    AND s.custno <> 0
                                                WITH(NOLOCK)");

        if(is_null($status) || empty($status)) return ['status' => 'Inactive', 'year' => 'n/a', 'last_service' => 'n/a'];


        return ['status' => $status[0]->YEPP_Status, 'year' => $status[0]->YEPP_Year, 'last_service' => $status[0]->YEPP_LastService];
    }

}
