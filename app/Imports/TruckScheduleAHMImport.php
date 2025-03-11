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

class TruckScheduleAHMImport extends TruckScheduleImport
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
        ];
        return $rules;
    }




}
