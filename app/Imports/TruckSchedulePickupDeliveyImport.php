<?php

namespace App\Imports;

use App\Rules\ValidTimeslotsforTruckSchedule;
use App\Rules\ValidTrucksforSchedule;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Events\BeforeImport;

class TruckSchedulePickupDeliveyImport extends TruckScheduleImport
{

    public function __construct($data, $zones)
    {
        $this->truckName = $data;
        $this->zones = $zones;
    }


    public function rules(): array
    {
        $rules = [
            'truck' =>  ['required', new ValidTrucksforSchedule($this->truckName)],
            'date' =>  ['required'],
            'timeslots' => ['required', new ValidTimeslotsforTruckSchedule()],
            'slots' => 'required|integer',
            'zone' => 'required|exists:zones,name',
            'is_pickup' => 'required| in:Yes,No',
            'is_delivery' => 'required| in:Yes,No',
        ];
        return $rules;
    }


    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            $zoneId = collect($this->zones)->firstWhere('zone', $row['zone'])['id'] ?? null;
            $is_pickup = $row['is_pickup'] == 'Yes' ? 1 : 0;
            $is_delivery = $row['is_delivery'] == 'Yes' ? 1 : 0;
            $this->data[] = array_merge($row->toArray(), [
                'zone_id' => $zoneId,
                'is_pickup' => $is_pickup,
                'is_delivery' => $is_delivery,
            ]);

        }
    }

    protected function fileBaseValidation(BeforeImport $event)
    {
        $requiredHeaders = ['Truck', 'Date', 'Timeslots', 'Slots', 'Zone', 'Is Pickup', 'Is Delivery'];

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
