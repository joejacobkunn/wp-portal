<?php

namespace App\Http\Livewire\Scheduler\Truck;

use App\Http\Livewire\Scheduler\Truck\Form\ScheduleForm;
use App\Models\Scheduler\TruckSchedule;
use App\Models\Scheduler\Zones;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Schedule extends Component
{
    use LivewireAlert;
    public ScheduleForm $form;
    public TruckSchedule $truckSchedule;
    public $truck;
    public $zone;
    public $showForm = false;

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
    }

    public function save()
    {
        $truckSchedule =  $this->form->update($this->truck);
        $timeStrng = $truckSchedule->start_time. ' - '. $truckSchedule->end_time;
        $slotsString = 'Slots : ' .$truckSchedule->slots;
        $this->alert('success', 'Schedule Updated');
        $this->dispatch('calendar-needs-update', $this->form->schedule_date, $truckSchedule->zone->name , $timeStrng, $slotsString);

    }

    public function handleDateClick($date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');
        $this->showForm = true;
        $schedule = TruckSchedule::where(['schedule_date' => $date, 'truck_id' => $this->truck->id])->first();
        $this->resetValidation();
        if($schedule) {
            $this->form->init($schedule);
            $this->truckSchedule = $schedule;
            return;
        }
        $this->form->reset();
        $this->form->setScheduleDate($date);
        $this->reset('truckSchedule');
    }

    public function onDateRangeChanges($start,$end)
    {
        $schedules = TruckSchedule::where('truck_id', $this->truck->id)
        ->whereBetween('schedule_date', [Carbon::parse($start)->format('Y-m-d'), Carbon::parse($end)->format('Y-m-d')])
        ->get()->map(function ($truckSchedule) {
            return [
                'zoneName' => $truckSchedule->zone->name ,
                'timeString' => $truckSchedule->start_time . ' - ' . $truckSchedule->end_time,
                'slotsString' => 'Slots : ' . $truckSchedule->slots,
                'schedule_date' => $truckSchedule->schedule_date,
            ];
        })
        ->toArray();
        $this->dispatch('calender-schedules-update', $schedules);
    }
}
