<?php

namespace App\Http\Livewire\Scheduler\Truck;

use App\Http\Livewire\Scheduler\Truck\Form\ScheduleForm;
use App\Http\Livewire\Scheduler\Truck\Form\ScheduleImportForm;
use App\Models\Scheduler\TruckSchedule;
use App\Models\Scheduler\Zones;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class Schedule extends Component
{
    use LivewireAlert, WithFileUploads;
    public ScheduleForm $form;
    public TruckSchedule $truckSchedule;
    public ScheduleImportForm $importForm;
    public $truck;
    public $zone;
    public $showForm = false;
    public $showImportForm;
    public $csvFile;
    public $daySchedules;
    public $importIteration = 1245332;

    protected $listeners = [
        'closeImportForm' => 'closeImportForm'
    ];
    public $zones;

    public function mount()
    {
        $this->zones = Zones::where(['service' => $this->truck->service_type, 'whse_id' => $this->truck->whse])->get();
        $this->handleDateClick(Carbon::now());
    }

    public function render()
    {
        return view('livewire.scheduler.truck.schedule');
    }

    public function submit()
    {
        $truckSchedule = $this->form->store($this->truck);
        $timeStrng = $truckSchedule->start_time. ' - '. $truckSchedule->end_time;
        $slotsString = 'Slots : ' .$truckSchedule->slots;
        $this->alert('success', 'Schedule created');
        $this->handleDateClick($this->form->schedule_date);
        $this->dispatch('calendar-needs-update', $this->form->schedule_date, $truckSchedule->zone->name , $timeStrng, $slotsString);
        $this->closeUpdateForm();

    }

    public function save()
    {
        $truckSchedule =  $this->form->update($this->truck);
        $timeStrng = $truckSchedule->start_time. ' - '. $truckSchedule->end_time;
        $slotsString = 'Slots : ' .$truckSchedule->slots;
        $this->alert('success', 'Schedule Updated');
        $this->dispatch('calendar-needs-update', $this->form->schedule_date, $truckSchedule->zone->name , $timeStrng, $slotsString);
        $this->closeUpdateForm();
    }

    public function handleDateClick($date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');
        $this->daySchedules = TruckSchedule::where(['schedule_date' => $date, 'truck_id' => $this->truck->id])
        ->get()
        ->map(function($schedule) {
            return [
            'id' => $schedule->id,
            'zone' => $schedule->zone?->name,
            'start_time' => $schedule->start_time,
            'end_time' => $schedule->end_time,
            'slots' => $schedule->slots,
            'scheduleCount' => $schedule->scheduleCount,
            ];
        })
        ->toArray();
        $this->form->reset();
        $this->form->schedule_date = $date;
        $this->showForm = false;
    }

    public function showTruckScheudleForm(TruckSchedule $schedule)
    {
        $this->showForm = true;
        $this->form->init($schedule);
    }

    public function onDateRangeChanges($start,$end)
    {
        $schedules = TruckSchedule::with('zone')->where('truck_id', $this->truck->id)
        ->whereBetween('schedule_date', [Carbon::parse($start)->format('Y-m-d'), Carbon::parse($end)->format('Y-m-d')])
        ->get()->map(function ($truckSchedule) {
            return [
                'zoneName' => $truckSchedule->zone?->name ,
                'timeString' => $truckSchedule->start_time . ' - ' . $truckSchedule->end_time,
                'slotsString' => 'Slots : ' . $truckSchedule->slots,
                'schedule_date' => $truckSchedule->schedule_date,
            ];
        })
        ->toArray();
        $this->dispatch('calender-schedules-update', $schedules);
    }

    public function importDataModal()
    {
       $this->showImportForm = true;
       $this->importForm->init($this->truck);
    }

    public function updatedImportFormCsvFile()
    {
        $response = $this->importForm->dataImport();
        if(!$response['status']) {
            $this->addError('importForm.csvFile', $response['message']);
        }
    }

    public function closeImportForm()
    {
        $this->showImportForm = false;
        $this->resetValidation();
        $this->importForm->reset();
    }

    public function importTruckSchedule()
    {
        $this->importForm->store();
        $this->alert('success', 'Import initiated');
        $this->closeImportForm();
    }

    public function downloadDemo()
    {
        $filePath = public_path(config('scheduler.demo_file_path'));

        if (!file_exists($filePath)) {
            $this->alert('error', 'File not found.');
            return;
        }
        return response()->download($filePath);
    }

    public function closeUpdateForm()
    {
        $this->showForm = false;
        $this->resetValidation();
        $this->form->reset(['zone', 'start_time', 'end_time', 'timePeriod', 'timePeriodEnd', 'slots']);
        $this->handleDateClick($this->form->schedule_date);
    }

    public function createSchedule()
    {
        $this->showForm = true;
    }
}

