<?php

namespace App\Imports;

use App\Rules\BrandValidationforWarrantyReg;
use App\Rules\ValidProductForWarrantyRegistration;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class WarrantyImport implements ToCollection,WithValidation, WithHeadingRow, SkipsOnFailure
{
    use Importable, SkipsFailures;
    protected $data = [];
    protected $brands = [];
    protected $failures = [];
    public $current_row = [];
    public function headingRow(): int
    {
        return 1; // Assuming headers are in the first row
    }

    public function __construct($data = [])
    {
        $this->brands = $data;

    }

    public function prepareForValidation($data, $index)
    {
        $this->current_row = $data;
        
        return $data;
    }

    public function rules(): array
    {
        $rules = [
            'brand' =>  ['required', new BrandValidationforWarrantyReg($this->brands)],
            'model' =>  ['required', new ValidProductForWarrantyRegistration($this->current_row)],
            'serial' => 'required',
            'reg_date' => 'required|date',
        ];
        return $rules;
    }

    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            $this->data[] = $row->toArray();
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
