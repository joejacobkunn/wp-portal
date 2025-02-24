<?php

namespace App\Http\Livewire\Scheduler\Schedule;

use App\Enums\Scheduler\ScheduleEnum;
use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Scheduler\Schedule\Forms\ScheduleForm;
use App\Models\Product\Product;
use App\Models\Scheduler\Schedule;
use App\Models\Scheduler\TruckSchedule;
use App\Models\SRO\RepairOrders;
use App\Traits\HasTabs;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ScheduleOrder extends Component
{
    use LivewireAlert, HasTabs;
    public ScheduleForm $form;

    public $page;
    public $scheduleOptions;
    public $selectedType;
    public $scheduledTruckInfo = [];
    public $activeWarehouse;
    public $showTypeLoader =false;
    public $serviceAddressModal = false;
    public $selectedSchedule;
    public $sro_number;
    public $sro_verified;
    public $sro_response;
    public $scheduledLineItem;
    public $actionStatus;
    public $schedulePriority = [
        'next_avail' => 'Next Available Date',
        'one_year' => 'One Year from Now',
    ];
    public $tabs = [
        'schedule-comment-tabs' => [
            'active' => 'comments',
            'links' => [
                'comments' => 'Comments',
                'activity' => 'Activity',
            ],
        ],
    ];

    protected $listeners = [
        'typeCheck' => 'typeCheck',
        'closeAddressValidation' => 'closeAddressValidation',
        'scheduleTypeChange' => 'scheduleTypeChange',
        'scheduleTypeDispatch' => 'scheduleTypeDispatch',
        'closeServiceAddressModal' => 'closeServiceAddressModal',
    ];

    public function mount()
    {
        $this->form->type = $this->selectedType;
        $this->scheduleOptions = collect(ScheduleEnum::cases())
        ->filter(fn($case) => $case->name === 'at_home_maintenance')
        ->mapWithKeys(fn($case) => [$case->name => $case->label()])
        ->toArray();
        if($this->page) {
            $this->form->init($this->selectedSchedule);
            $this->sro_number = $this->selectedSchedule->sro_number;
            if($this->sro_number) {
                $this->sro_verified = true;
                $this->sro_response = $this->getSROInfo($this->sro_number);
            }
        }
        if (Auth::user()->can('scheduler.can-schedule-override')) {
            $this->schedulePriority = $this->schedulePriority + ['schedule_override' => 'Schedule Override'];
        }
    }

    public function render()
    {
        return $this->renderView('livewire.scheduler.schedule.schedule-order');
    }

    public function typeCheck($field, $value)
    {
        $this->form->type = $value;
        $this->form->checkServiceValidity($value);
        if(!$this->form->ServiceStatus) {
            $this->reset('scheduledTruckInfo');
        }
    }

    public function submit()
    {

        $this->authorize('store', Schedule::class);
        $response = $this->form->store();
        if(!$response['status']) {
            $this->alert($response['class'], $response['message']);
            return;
        }
        $enumInstance = ScheduleEnum::tryFrom($response['schedule']->type);
        $icon = $enumInstance ? $enumInstance->icon() : null;
        $event = [
            'id' => $response['schedule']->id,
            'title' => 'Order #' . $response['schedule']->sx_ordernumber,
            'start' => $response['schedule']->schedule_date->format('Y-m-d'),
            'description' => 'schedule',
            'color' => $response['schedule']->status_color,
            'icon' => $icon,
        ];
        $this->alert($response['class'], $response['message']);
        $this->openEvent($response['schedule']);
        $this->dispatch('add-event-calendar', newEvent: $event);
    }

    public function updatedFormSuffix($value)
    {
        $this->form->reset([
            'orderInfo',
            'zipcodeInfo',
            'scheduleType',
            'schedule_date',
            'schedule_time',
            'line_item',
            'alertConfig',
            'ServiceStatus'
        ]);
        $this->reset('scheduledTruckInfo');
        if(is_numeric($value))
        {
            $this->validateOnly('form.sx_ordernumber');
            $this->form->getOrderInfo($value, $this->activeWarehouse->short);
            $this->dispatch('enable-date-update', enabledDates: $this->form->enabledDates);
        }
    }

    public function updatedFormSxOrdernumber($value)
    {
        $this->form->suffix = null;
        $this->form->reset([
            'orderInfo',
            'zipcodeInfo',
            'scheduleType',
            'schedule_date',
            'schedule_time',
            'line_item',
            'alertConfig',
            'ServiceStatus'
        ]);
        $this->reset('scheduledTruckInfo');
    }

    public function updatedFormServiceAddress($value)
    {
        $this->form->service_address = $value;
        $this->form->updatedAddress();
    }

    public function updateFormScheduleDate($date)
    {
        $this->form->schedule_date = Carbon::parse($date)->format('Y-m-d');
        $whse = $this->page ? $this->form->schedule->warehouse->id : $this->form->orderInfo->warehouse->id;

        $this->form->getTruckSchedules($whse);
        $this->form->schedule_time = null;
    }

    public function closeAddressValidation()
    {
        $this->useCurrentAddress();
    }

    public function fixAddress()
    {
        $this->form->showAddressBox = true;
    }

    public function useRecommended()
    {
        $this->form->service_address = $this->form->recommendedAddress;
        $this->useCurrentAddress();
    }

    public function useCurrentAddress()
    {
        $this->form->serviceZip = $this->form->extractZipCode($this->form->service_address);
        $this->form->showAddressModal = false;
        $this->form->reset([
            'recommendedAddress',
            'showAddressModal',
            'showAddressBox'
        ]);
        $this->form->checkZipcode();
    }

    public function scheduleTypeChange($field, $value)
    {
        $this->showTypeLoader = true;
        if(isset($this->form->Schedule)) {

        }
        $this->form->scheduleType = $value;
        $this->dispatch('scheduleTypeDispatch');

    }

    public function scheduleTypeDispatch()
    {
        $whse = $this->page ? $this->form->schedule->warehouse->id : $this->form->orderInfo->warehouse->id;
        $this->form->getEnabledDates($this->form->scheduleType == 'schedule_override', $whse);
        if($this->form->scheduleType == 'one_year') {
            $date = Carbon::now()->addYear()->format('Y-m-d');
        }

        if($this->form->scheduleType == 'next_avail' || $this->form->scheduleType == 'schedule_override') {
            $date = isset($this->form->enabledDates[0]) ? $this->form->enabledDates[0] : Carbon::now()->format('Y-m-d');
        }

        $this->form->reset(['schedule_time', 'truckSchedules', 'schedule_date']);
        $this->dispatch('enable-date-update', enabledDates: $this->form->enabledDates);
        $this->dispatch('set-current-date', activeDay: $date);
        $this->showTypeLoader = false;
    }


    public function save()
    {
        $this->authorize('update', $this->form->schedule);

        $response = $this->form->update();
        if(!$response['status']) {
            $this->alert($response['class'], $response['message']);
            return;
        }
        $this->EventUpdate($response);
        $this->reset('actionStatus');
    }



    public function EventUpdate($response)
    {
        $enumInstance = ScheduleEnum::tryFrom($response['schedule']->type);
        $icon = $enumInstance ? $enumInstance->icon() : null;
        $event = [
            'id' => $response['schedule']->id,
            'title' => 'Order #' . $response['schedule']->sx_ordernumber,
            'start' => $response['schedule']->schedule_date->format('Y-m-d'),
            'description' => 'schedule',
            'color' => $response['schedule']->status_color,
            'icon' => $icon,
        ];
        $this->alert($response['class'], $response['message']);
        $this->dispatch('remove-event-calendar', eventId: $response['schedule']->id);
        $this->dispatch('add-event-calendar', newEvent: $event);
    }

    public function showAddressModal()
    {
        $this->form->service_address_temp = $this->form->service_address;
        $this->serviceAddressModal = true;
        $this->dispatch('browser:show-edit-address');
    }

    public function updateAddress()
    {
        $this->form->service_address = $this->form->service_address_temp;
        $this->form->updatedAddress();
        $this->closeServiceAddressModal();
    }

    public function closeServiceAddressModal()
    {
        $this->serviceAddressModal = false;
    }

    public function revertAddress()
    {
        $this->form->service_address_temp = $this->form->addressFromOrder;
    }

    // todo need to check whether it is still required
    public function getEnabledDates()
    {
        $whse = $this->page ? $this->form->schedule->warehouse->id : $this->form->orderInfo->warehouse->id;
        if(empty($this->form->enabledDates)) {
            $this->form->getEnabledDates(false, $whse);
        }
        return $this->form->enabledDates;
    }

    public function selectSlot($scheduleId)
    {
        $schedule = TruckSchedule::find($scheduleId);
        $this->form->schedule_time = $schedule->id;
        $this->form->selectedTruckSchedule = $schedule;
        $this->scheduledTruckInfo = [
            'truck_name' => $schedule->truck->truck_name,
            'vin_number' => $schedule->truck->vin_number,
            'driver_name' => $schedule->driver?->name,
            'make_model' => $schedule->truck->model_and_make,
            'year' => $schedule->truck->year,
            'shiftType' => $schedule->truck->shift_type,
            'cubic_storage' => $schedule->truck->cubic_storage_space,
            'notes' => $schedule->truck->notes,
        ];
        $this->resetValidation(['form.schedule_time']);
    }

    public function getSROInfo($sro)
    {
        if(config('sx.mock'))
        {
            $faker = \Faker\Factory::create();
            return [
                'first_name' => $faker->name(),
                'last_name' => $faker->lastName(),
                'address' => $faker->streetAddress(),
                'state' => $faker->state(),
                'city' => $faker->city(),
                'zip' => $faker->postcode(),
                'brand' => 'Toro',
                'model' => 'ghd567df',
                'id' => '1',
                'status' => 'Complete',
                'note' => 'this is a note',
                'sx_repair_order_no' => '67678854'
            ];
        }else{
            $sro = RepairOrders::select('first_name','last_name', 'at_home_address as address','at_home_state as state', 'at_home_city as city', 'at_home_zip as zip', 'brand', 'model', 'id', 'status', 'at_home_note as note', 'sx_repair_order_no')->where('sro_no', $sro)->first();
            if(!empty($sro)) {
                return $sro->toArray();
            }
            return null;
        }

    }

    public function cancelSchedule()
    {
        $response = $this->form->cancelSchedule();
        $this->reset(['actionStatus']);
        $this->EventUpdate($response);
    }

    public function undoCancel()
    {
        $this->authorize('update', $this->form->schedule);
        $response = $this->form->undoCancel();
        $this->reset(['actionStatus', 'sro_number', 'sro_verified']);
        $this->EventUpdate($response);
    }

    public function updatedSroNumber($value)
    {
        $this->sro_response = [];
        $this->sro_verified = false;

        $this->sro_response = strlen($value) > 6 ? $this->getSROInfo($value) : [];
    }

    public function linkSRO()
    {
        $response = $this->form->linkSRONumber($this->sro_number);
        $this->alert($response['class'], $response['message']);
        $this->EventUpdate($response);
    }

    public function unlinkSro()
    {
        $response = $this->form->unlinkSro();
        $this->reset([
            'sro_number',
            'sro_verified',
            'sro_response',
            'actionStatus'
        ]);
        $this->alert($response['class'], $response['message']);
        $this->EventUpdate($response);
    }

    public function confirmSchedule()
    {
        $response = $this->form->confirmSchedule();
        $this->alert($response['class'], $response['message']);
        $this->reset('actionStatus');
        $this->EventUpdate($response);
    }

    public function cancelConfirm()
    {
        $response = $this->form->unConfirm();
        $this->reset([
            'sro_number',
            'sro_verified',
            'sro_response',
            'actionStatus'
        ]);
        $this->alert($response['class'], $response['message']);

        $this->EventUpdate($response);
    }

    public function startSchedule()
    {
        $this->authorize('startSchedule', $this->form->schedule);
        $response = $this->form->startSchedule();
        $this->reset('actionStatus');
        $this->EventUpdate($response);
    }

    public function completeSchedule()
    {
        $this->authorize('update', $this->form->schedule);
        $response = $this->form->completeSchedule();
        $this->reset('actionStatus');
        $this->EventUpdate($response);
    }

    public function openEvent($schedule)
    {
        $this->page = true;
        $this->form->init($schedule);
        $this->selectedType = $schedule->type;
        $this->scheduledLineItem = Product::whereRaw('account_id = ? and LOWER(`prod`) = ? LIMIT 1',[1,strtolower($schedule->line_items)])->get()->toArray();
        $this->dispatch('attchQueryParam', $schedule->id);
    }

    public function changeStatus($status)
    {
        if($this->actionStatus == $status) {
            $this->reset('actionStatus');
            return;
        }
        $this->actionStatus = $status;
    }
}
