<?php

namespace App\Http\Livewire\Scheduler\Schedule;

use App\Enums\Scheduler\ScheduleEnum;
use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Scheduler\Schedule\Forms\ScheduleAHMForm;
use App\Http\Livewire\Scheduler\Schedule\Forms\SchedulePDForm;
use App\Models\Order\Order;
use App\Models\Product\Category;
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
    public ScheduleAHMForm $ahmForm;
    public SchedulePDForm $pdForm;
    public $form;

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
    public $startedSchedules;
    public $showConfirmMessage = false;
    public $orderErrorStatus = false;
    public $contactModal = false;
    public $tempPhone;
    public $tempEmail;
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
        if(in_array($this->selectedType, ['pickup', 'delivery'])) {
            $this->form = $this->pdForm;
        }
        if($this->selectedType == 'at_home_maintenance') {
            $this->form = $this->ahmForm;
        }
        $this->form->type = $this->selectedType;
        $this->scheduleOptions = collect(ScheduleEnum::cases())
        ->filter(fn($case) =>
            $case->name === 'at_home_maintenance' ||
            (auth()->user()->can('scheduler.override') && in_array($case->name, ['delivery', 'pickup']))
        )
        ->mapWithKeys(fn($case) => [$case->name => $case->label()])
        ->toArray();

        if($this->page) {
            $this->form->init($this->selectedSchedule);
            $this->sro_number = $this->selectedSchedule->sro_number;
            if($this->sro_number) {
                $this->sro_verified = true;
                $this->sro_response = $this->getSROInfo($this->sro_number);
            }
            $this->startedSchedules = 0;
            if (!empty($this->form->schedule->truckSchedule->driver_id)) {
                $this->startedSchedules = Schedule::where('status', 'out_for_delivery')
                    ->whereHas('truckSchedule', function ($query) {
                        $query->where('driver_id', $this->form->schedule->truckSchedule->driver_id);
                    })
                    ->count();
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
        if(in_array($value, ['pickup', 'delivery'])) {
            $this->form = $this->pdForm;
        }
        if($value == 'at_home_maintenance') {
            $this->form = $this->ahmForm;
        }
        $this->form->reset();
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
            'ServiceStatus',
            'phone',
            'email'
        ]);
        $this->reset('scheduledTruckInfo');
        if(is_numeric($value))
        {
            $this->validateOnly('form.sx_ordernumber');
            $response = $this->form->getOrderInfo($value, $this->activeWarehouse->short);
            if(is_array($response) && !$response['status']) {
                $this->alert('error', $response['message']);
                return;
            }
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

        if($this->form->type != ScheduleEnum::at_home_maintenance->value) {
            $this->checkCargoSpace();
        }
        $this->reset('scheduledTruckInfo');
    }

    public function closeAddressValidation()
    {
        $this->useCurrentAddress();
    }

    public function fixAddress()
    {

        $zip = $this->form->extractZipCode($this->form->recommendedAddress);
        $response = $this->form->validateAddress($this->form->recommendedAddress, $zip);
        if(!$response['status']) {
            $this->alert('error', $response['message']);
            return;
        }
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
        ]);
        $this->form->checkZipcode();
    }

    public function scheduleTypeChange($field, $value)
    {
        $this->showTypeLoader = true;

        $this->form->scheduleType = $value;
        $this->dispatch('scheduleTypeDispatch');

    }

    public function scheduleTypeDispatch()
    {
        $whse = $this->page ? $this->form->schedule->warehouse->id : $this->form->orderInfo->warehouse->id;
        $this->form->getEnabledDates($this->form->scheduleType == 'schedule_override', $whse);
        $date = Carbon::now();
        if($this->form->scheduleType == 'one_year') {
            $date = $date->addYear()->format('Y-m-d');
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
        $this->form->schedule_time = $schedule->id; // to get scheduled truck time slot
        $this->form->selectedTruckSchedule = $schedule;
        $cargoInfo = $this->form->truckSchedules->where('id', $schedule->id)->first();
        $this->reset('scheduledTruckInfo');
        $this->scheduledTruckInfo = [
            'truck_name' => $schedule->truck->truck_name,
            'vin_number' => $schedule->truck->vin_number,
            'driver_name' => $schedule->driver?->name,
            'make_model' => $schedule->truck->model_and_make,
            'year' => $schedule->truck->year,
            'shiftType' => $schedule->truck->shift_type,
            'notes' => $schedule->truck->notes,
            'height' => $schedule->truck->height,
            'width' => $schedule->truck->width,
            'length' => $schedule->truck->length,
            'cargoInfo' => $cargoInfo->cargoItems ?? null,
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
        $this->reset(['actionStatus', 'sro_number', 'sro_verified', 'sro_response']);
        $this->EventUpdate($response);
    }

    public function updatedSroNumber($value)
    {
        $this->sro_response = [];
        $this->sro_verified = false;
        $this->reset([
            'orderErrorStatus',
            'showConfirmMessage'
        ]);
        $this->sro_response = strlen($value) > 6 ? $this->getSROInfo($value) : [];
        if(empty($this->sro_response)) {
            return;
        }
        $order = Order::where('order_number', $this->sro_response['sx_repair_order_no'])->select('id','whse', 'order_number')->first();
        if(!$order) {
            $this->orderErrorStatus = true;
            return;
        }

        if($order->whse != $this->form->schedule->truckSchedule->truck->warehouse_short) {
            $this->showConfirmMessage = true;
            return;
        }
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
        if(config('sx.mock') ) {
            $this->confirmedSchedule();
            return;
        }
        $order = Order::where('order_number', $this->sro_response['sx_repair_order_no'])->select('id','whse', 'order_number')->first();
        if(!$order) {
            $this->orderErrorStatus = true;
            return;
        }

        if($order->whse != $this->form->schedule->truckSchedule->truck->warehouse_short) {
            $this->showConfirmMessage = true;
            return;
        }
        $this->confirmedSchedule();
    }

    public function confirmedSchedule()
    {
        $response = $this->form->confirmSchedule();
        $this->alert($response['class'], $response['message']);
        $this->reset(['actionStatus', 'showConfirmMessage', 'orderErrorStatus']);
        $this->EventUpdate($response);
    }

    public function cancelConfirm()
    {
        $response = $this->form->unConfirm();
        $this->reset([
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

    public function showEditContactModal()
    {
        $this->tempPhone = $this->form->phone;
        $this->tempEmail = $this->form->email;
        $this->contactModal = true;
    }

    public function updateContact()
    {
        $this->validate([
            'tempEmail' => 'required|email:rfc,dns|max:255',
            'tempPhone' => 'required|regex:/^\d{10}$/'
        ], [
            'tempEmail.required' => 'Email is required.',
            'tempEmail.email' => 'Enter a valid email address.',
            'tempPhone.required' => 'Phone number is required.',
            'tempPhone.regex' => 'Phone number must be exactly 10 digits.'
        ]);

        $this->form->phone = $this->tempPhone;
        $this->form->email = $this->tempEmail;
        $this->form->reset('contactError');
        $this->closeContactModal();
    }

    public function closeContactModal()
    {
        $this->contactModal = false;
        $this->resetValidation();
        $this->reset([
            'tempPhone',
            'tempEmail'
        ]);
    }
    public function updateContactSchedule()
    {
        $this->validate([
            'tempEmail' => 'nullable|email:rfc,dns|max:255',
            'tempPhone' => 'nullable|regex:/^\d{10}$/'
        ], [
            'tempEmail.email' => 'Enter a valid email address.',
            'tempPhone.regex' => 'Phone number must be exactly 10 digits.'
        ]);

        $this->form->phone = $this->tempPhone;
        $this->form->email = $this->tempEmail;
        $this->form->updateContact();
        $this->alert('success', 'Contact updated');
        $this->form->reset('contactError');
        $this->closeContactModal();
    }

    public function checkCargoSpace()
    {

        foreach($this->form->truckSchedules as $truckSchedule) {
            $orderArray  = [];
            $productArray  = [];
            $schedules = Schedule::where('truck_schedule_id', $truckSchedule->id)->whereIn('status', ['scheduled', 'out_for_delivery'])->get();
            if($schedules) {
                foreach($schedules as $schedule ) {
                    $orderArray[] = $schedule->sx_ordernumber;
                    if($schedule->line_item) {
                        foreach ($schedule->line_item as $key => $item) {
                            $productArray[] = ['prod' => $key,'scheduled' =>true];
                        }
                    }
                }
            }
            $orderArray[] = $this->form->sx_ordernumber;
            if($this->form->line_item) {
                foreach($this->form->line_item as $key => $item) {
                    if($this->page) {
                        $productArray[] = ['prod' => $key];
                    } else {
                        $productArray[] = ['prod' => $item];
                    }
                }
            }
            $orders = Order::whereIn('order_number', $orderArray)->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'line_items' => $order->line_items ?? null,
                ];
            })
            ->toArray();
            foreach($productArray as $key => $product) {
                foreach ($orders as $item) {
                    foreach ($item['line_items']['line_items'] as $lineItem) {
                        if ($lineItem['shipprod'] === $product['prod']) {
                            $productArray[$key]['cat'] = strtoupper($lineItem['prodcat']);
                            $productArray[$key]['desc'] = strtoupper($lineItem['descrip']);
                            break 2;
                        }
                    }
                }
            }
            $categoryArray = array_column($productArray, 'cat');
            $categories = Category::with('cargoConfigurator')->whereIn('name', $categoryArray)->get();
            foreach($productArray as $key =>  $item) {
                $category = $categories->where('name', $item['cat'])->first();
                $productArray[$key]['height'] = $category->cargoconfigurator?->height;
                $productArray[$key]['length'] = $category->cargoconfigurator?->length;
                $productArray[$key]['width'] = $category->cargoconfigurator?->width;
            }


            //truck
            $truckHeight = $truckSchedule->truck_height;
            $truckWidth = $truckSchedule->truck_width;
            $truckLength = $truckSchedule->truck_length;
            $status = $this->checkSpaceArrangement($productArray, $truckHeight, $truckWidth, $truckLength);
            $truckSchedule->storageStatus = false;
            if($status) {
                $truckSchedule->storageStatus = true;
                $spaceAvailableAfter = $this->calculateOccupiedSpace($productArray, $truckLength, $truckWidth);
                $truckSchedule->cargoItems = $productArray;
                $truckSchedule->availableSpace = $spaceAvailableAfter;
            }
        }
    }
    private function calculateOccupiedSpace($productArray, $truckLength, $truckWidth)
    {
        $totalArea = array_reduce($productArray, function ($carry, $item) {
            if ( isset($item['scheduled']) && !empty($item['scheduled'])) {
                return $carry + ($item['length'] * $item['width']);
            }
            return $carry;
        }, 0);

        $truckArea = $truckLength * $truckWidth;
        $usedPercentage = ($totalArea / $truckArea) * 100;
        $availablePercentage = 100 - $usedPercentage;
        return round($availablePercentage, 2);
    }

    public function checkSpaceArrangement($scheduledItems, $truckHeight, $truckWidth, $truckLength)
    {
        $totalItems = 0;
        if(empty($scheduledItems)) {
            return true;
        }

        usort($scheduledItems, function ($a, $b) {
            return min($a['length'], $a['width']) <=> min($b['length'], $b['width']);
        });
        $occupiedSpace = array_fill(0, $truckWidth, array_fill(0, $truckLength, false));
        foreach ($scheduledItems as $item) {
            $itemFits = false;
            if ($item['height'] > $truckHeight) {
                return false;
            }
            // Try placing item normally (L x W) or rotated (W x L)
            $possibleOrientations = [
                ['length' => $item['length'], 'width' => $item['width']],  // Normal
                ['length' => $item['width'], 'width' => $item['length']]   // Rotated
            ];

            foreach ($possibleOrientations as $orientation) {
                $itemLength = $orientation['length'];
                $itemWidth = $orientation['width'];
                for ($y = 0; $y <= $truckWidth - $itemWidth; $y++) {
                    for ($x = 0; $x <= $truckLength - $itemLength; $x++) {
                        // Check if the item fits in the current space
                        if ($this->canPlaceItem($occupiedSpace, $x, $y, $itemLength, $itemWidth)) {
                            // Mark space as occupied
                            $this->markOccupied($occupiedSpace, $x, $y, $itemLength, $itemWidth);
                            $itemFits = true;
                            break 3;
                        }
                    }
                }
            }
            if (!$itemFits) {
                return false;
            }
        }
        return true;
    }


    private function canPlaceItem($occupiedSpace, $x, $y, $length, $width) {
        for ($i = $y; $i < $y + $width; $i++) {
            for ($j = $x; $j < $x + $length; $j++) {
                if ($occupiedSpace[$i][$j]) {
                    return false; // Space is already occupied
                }
            }
        }
        return true;
    }

    // Helper private function: Mark space as occupied
    private function markOccupied(&$occupiedSpace, $x, $y, $length, $width) {
        for ($i = $y; $i < $y + $width; $i++) {
            for ($j = $x; $j < $x + $length; $j++) {
                $occupiedSpace[$i][$j] = true;
            }
        }
    }
}
