<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;


class OrdersImport implements ToCollection, WithValidation, WithHeadingRow, SkipsOnFailure
{
    use Importable, SkipsFailures;
    protected $data = [];
    protected $importFile = [];
    protected $failures = [];
    protected $seenOrderNumbers = [];
    public $current_row = [];

    public function headingRow(): int
    {
        return 1;
    }

    public function __construct()
    {
    }

    public function prepareForValidation($data, $index)
    {
        $this->current_row = $data;

        return $data;
    }

    public function rules(): array
    {
        $rules = [
            'cono' =>  ['required'],
            'order_number' =>  ['required', 'unique:orders,order_number'],
            'order_number_suffix' => 'required',
            'whse' => 'required',
            'taken_by' => 'required',
            'is_dnr' => 'required',
            'order_date' => ['required', 'date'],
            'promise_date' => ['nullable', 'date'],
            'warehouse_transfer_available' => 'required',
            'partial_warehouse_transfer_available' => 'required',
            'wt_transfers' => 'required',
            'is_web_order' => 'required',
            'last_line_added_at' => 'nullable',
            'golf_parts' => 'nullable',
            'non_stock_line_items' => 'nullable',
            'is_sro' => 'required',
            'last_followed_up_at' => 'nullable',
            'ship_via' => 'nullable',
            'line_items' => 'nullable',
            'is_sales_order' => 'required',
            'qty_ship' => 'nullable',
            'qty_ord' => 'nullable',
            'stage_code' => 'required',
            'dnr_items' => 'nullable',
            'sx_customer_number' => 'required',
            'status' => 'required',
            'last_updated_by' => 'nullable',
        ];
        return $rules;
    }

    public function collection(Collection $collection)
    {
        foreach ($collection as $key => $row) {
            $orderNumber = $row['order_number'];
            if (!in_array($orderNumber, $this->seenOrderNumbers)) {

                $this->seenOrderNumbers[] = $orderNumber;
                $this->data[] = $row->toArray();
            } else {
                $this->failures[] = new Failure(
                    $key,
                    'order_number',
                    ['Duplicate order number detected.'],
                    $row->toArray()
                );
            }
        }

    }

    public function onFailure(Failure ...$failures)
    {
        $this->failures = array_merge($this->failures, $failures);
    }

    /**
     * Get the failures.
     *
     * @return array
     */
    public function getFailures()
    {
        return $this->failures;
    }
    public function getData()
    {
        return $this->data;
    }
}
