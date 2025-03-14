<?php
 namespace App\Http\Livewire\Scheduler\Truck\Form;

use App\Imports\TruckScheduleAHMImport;
use App\Imports\TruckScheduleImport;
use App\Imports\TruckSchedulePickupDeliveyImport;
use App\Jobs\ProcessTruckScheduleImport;
use App\Models\Scheduler\Truck;
use App\Models\Scheduler\Zones;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Form;
use Maatwebsite\Excel\Facades\Excel;

class ScheduleImportForm extends Form
{
    public ?Truck $truck;
    public  $csvFile;
    public  $validatedRows = [];
    public  $importErrorRows = [];
    public  $zones;

    protected function rules()
    {
        return [
            'csvFile' => 'required|file|mimes:csv,txt,xlsx|ends_with_csv'
        ];
    }

    protected $validationAttributes = [
        'csvFile' => 'Upload File',
    ];

    public function init($truck)
    {
        $this->truck = $truck;
        $this->zones = Zones::where(['whse_id' => $this->truck->warehouse->id])->get()
                ->map(function ($zone) {
                    return [
                        'id' => $zone->id,
                        'zone' => $zone->name,
                    ];
                })
                ->toArray();
    }

    public function store()
    {
        $this->validate();
        ProcessTruckScheduleImport::dispatch($this->truck, $this->validatedRows, $this->importErrorRows, Auth::user());
    }


    public function dataImport()
    {
        $this->validateOnly('csvFile');
        try {
            if($this->truck->service_type->value == 'pickup_delivery') {
                $import = new TruckSchedulePickupDeliveyImport($this->truck->truck_name, $this->zones);
            } else {
                $import = new TruckScheduleAHMImport($this->truck->truck_name, $this->zones);
            }
            Excel::import($import, $this->csvFile);
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
        $this->validatedRows = $import->getData();
        $failures = $import->failures();
        if (count($failures) > 0) {
            foreach ($failures as $failure) {
                $this->importErrorRows[$failure->row()] = $failure->values();
            }
        }
        return ['status' => true, 'message' => 'file import initiated'];

    }
}
