<?php

namespace App\Imports;

use App\Rules\ValidAssigneeForSMS;
use App\Rules\ValidateOfficeForSMS;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Validators\Failure;

class SMSMarketingImport implements ToCollection, WithValidation, WithHeadingRow, SkipsOnFailure, WithEvents
{
    use Importable, SkipsFailures;

    public $data = [];
    protected $failures = [];

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function(BeforeImport $event) {
                $this->fileBaseValidation($event);
            },
        ];
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function rules(): array
    {
        $rules = [
            'phone' =>  ['required', 'digits:10'],
            'message' =>  ['required', 'string', 'max:300'],
            'office' =>  ['required', new ValidateOfficeForSMS()],
            'assignee' =>  ['required', new ValidAssigneeForSMS()],
        ];
        return $rules;
    }

    public function getData()
    {
        return $this->data;
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

    protected function fileBaseValidation(BeforeImport $event)
    {
        $requiredHeaders = ['PHONE', 'MESSAGE', 'OFFICE', 'ASSIGNEE'];

        $worksheet = $event->reader->getActiveSheet();
        $headerRow = $worksheet->getRowIterator()->current();
        $cellIterator = $headerRow->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);
        $totalRows = $worksheet->getHighestDataRow();
        $actualHeaders = [];
        foreach ($cellIterator as $cell) {
            $actualHeaders[] = $cell->getValue();
        }

        $missingHeaders = array_diff($requiredHeaders, $actualHeaders);
        $extraHeaders = array_diff($actualHeaders, $requiredHeaders);

        if ( ! empty($missingHeaders)) {
            throw new \Exception('Missing required headers: ' . implode(', ', $missingHeaders) . '. ');
        }

        if ( ! empty($extraHeaders)) {
            throw new \Exception('Unexpected headers found: ' . implode(', ', $extraHeaders) . '.');
        }

        if ($totalRows <= 1) {
            throw new \Exception('Uploaded file is empty, please upload a file with data');
        }

        if ($totalRows-1 > 10000 ) {
            throw new \Exception('Maximum number of rows allowed is 10000. please adjust the import file.');
        }
    }
}
