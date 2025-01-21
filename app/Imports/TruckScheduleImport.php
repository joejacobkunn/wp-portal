<?php

namespace App\Imports;

use App\Rules\ValidTimeslotsforTruckSchedule;
use App\Rules\ValidTrucksforSchedule;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Validators\Failure;

class TruckScheduleImport implements ToCollection,WithValidation, WithHeadingRow, SkipsOnFailure, WithEvents
{
    use Importable, SkipsFailures;

    public $current_row = [];
    protected $data = [];
    protected $failures = [];
    public $truckName;
    public $zones;

    public function __construct($data, $zones)
    {
        $this->truckName = $data;
        $this->zones = $zones;
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function rules(): array
    {
        $rules = [
            'truck' =>  ['required', new ValidTrucksforSchedule($this->truckName)],
            'date' =>  ['required'],
            'timeslots' => ['required', new ValidTimeslotsforTruckSchedule()],
            'slots' => 'required|integer',
            'zone' => 'required|exists:zones,name',
        ];
        return $rules;
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function(BeforeImport $event) {
                $this->fileBaseValidation($event);
            },
        ];
    }

    public function prepareForValidation($data, $index)
    {
        $this->current_row = $data;

        return $data;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            $zoneId = collect($this->zones)->firstWhere('zone', $row['zone'])['id'] ?? null;
            $this->data[] = array_merge($row->toArray(), ['zone_id' => $zoneId]);

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

    protected function fileBaseValidation(BeforeImport $event)
    {
        $requiredHeaders = ['Truck', 'Date', 'Timeslots', 'Slots', 'Zone'];

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

    }
}
