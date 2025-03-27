<?php
 namespace App\Http\Livewire\Scheduler\Schedule\Forms;

use App\Classes\SX;
use App\Contracts\DistanceInterface;
use App\Enums\Scheduler\ScheduleEnum;
use App\Models\Core\CalendarHoliday;
use App\Models\Core\Warehouse;
use App\Models\Order\Order;
use App\Models\Product\Category;
use App\Models\Product\Product;
use App\Models\Scheduler\CargoConfigurator;
use App\Models\Scheduler\Schedule;
use App\Models\Scheduler\Shifts;
use App\Models\Scheduler\TruckSchedule;
use App\Models\Scheduler\Zipcode;
use App\Models\Scheduler\Zones;
use App\Models\SX\Order as SXOrder;
use App\Models\SX\OrderLineItem;
use App\Rules\ValidateScheduleDate;
use App\Rules\ValidateScheduleTime;
use App\Rules\ValidateSlotsforSchedule;
use Carbon\Carbon;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Livewire\Form;

class SchedulePDForm extends ScheduleForm
{

    public $line_item = [];
    public $prodDimension = [];
    public $contactError;
    public $tag_number;

    protected $validationAttributes = [
        'type' => 'Schedule Type',
        'sx_ordernumber' => 'Order Number',
        'scheduleType' => 'Schedule Type',
        'schedule_date' => 'Schedule Date',
        'schedule_time' => 'Time Slot',
        'suffix' => 'Order Suffix',
        'notes' => 'Notes',
        'line_item' => 'Line item',
        'service_address' => 'Service Address',
        'cancel_reason' => 'Reason',
        'tag_number' => 'Tag Number',
    ];
    protected function rules()
    {
        return [
            'type' => 'required',
            'sx_ordernumber' => [
                'required',
                'numeric',
                'max_digits:9',
            ],
            'suffix' => 'required|numeric|max_digits:1',
            'schedule_date' => [
                'required',
                new ValidateScheduleDate($this->getActiveDays())
            ],
            'scheduleType' =>'required',
            'schedule_time' =>[
                'required',
                new ValidateSlotsforSchedule($this->scheduleType)
            ],
            'line_item' => 'required',
            'notes' =>'nullable',
            'service_address' =>'required',
            'reschedule_reason' =>'nullable|string|max:225',
            'cancel_reason' => 'required|string|max:220',
            'not_purchased_via_weingartz' => 'nullable',
            'tag_number' => [
                'nullable',
                'digits:4',
                'integer',
                'required_if:type,pickup',
            ],
        ];

    }

    public function messages()
    {
        return [
            'schedule_time.required' => 'Time slot not selected',
        ];
    }

    public function getOrderInfo($suffix, $aciveWarehouse)
    {
        $this->resetValidation(['sx_ordernumber', 'suffix']);
        $this->reset('not_purchased_via_weingartz');
        $this->alertConfig['status'] = false;
        if(!$this->sx_ordernumber) {
            $this->addError('sx_ordernumber', 'Order Number is required');
            return;
        }


        if(!is_numeric($suffix)) {
            $this->addError('sx_ordernumber', 'Order Suffix is required');
            return;
        }

        $this->SXOrderInfo = [];

        if(!config('sx.mock'))
        {

            $this->SXOrderInfo = SXOrder::where('cono', 10)->where('orderno', $this->sx_ordernumber)->where('ordersuf', $suffix)->first();

            if(is_null($this->SXOrderInfo)) {
                $this->addError('sx_ordernumber', 'Order not Found');
                $this->reset([
                    'zipcodeInfo',
                    'scheduleDateDisable',
                    'schedule_date',
                    'schedule_time',
                    'enabledDates' ,
                    'truckSchedules',
                    'scheduleType',
                    'line_item',
                    'orderInfo',
                    'phone',
                    'email'

                ]);
                return;
            }

            $this->orderInfo = $this->syncSXOrder($this->SXOrderInfo);

        }else{
            $this->orderInfo = Order::where(['order_number' =>$this->sx_ordernumber, 'order_number_suffix' => $suffix])
            ->first();
        }

        if(is_null($this->orderInfo)) {
            $this->addError('sx_ordernumber', 'Order not Found');
            $this->reset([
                'zipcodeInfo',
                'scheduleDateDisable',
                'schedule_date',
                'schedule_time',
                'enabledDates' ,
                'truckSchedules',
                'scheduleType',
                'line_item',
                'orderInfo',
                'phone',
                'email'

            ]);
            return;
        }

        $this->orderTotal = (config('sx.mock')) ? '234.25' : number_format($this->SXOrderInfo->totordamt,2);
        if(is_null($this->orderInfo->shipping_info) || empty($this->orderInfo?->shipping_info['line']) || strlen($this->orderInfo?->shipping_info['line']) < 2) {
            $this->addError('sx_ordernumber', 'Shipping info missing');
            return;
        }
        $categoryArray = [];
        if(!empty($this->orderInfo?->line_items['line_items'])) {
            foreach ($this->orderInfo->line_items['line_items'] as $item) {
                $categoryArray[] = $item['prodcat'];
            }
        }

        if(!empty($categoryArray)) {

            $categories = Category::with('cargoConfigurator')
            ->whereIn('name', $categoryArray)
            ->get();

            $this->prodDimension = $categories->map(function ($category) {
                return [
                    'category_id' => $category->id,
                    'category' => $category->name,
                    'cargo_id' => $category->cargoConfigurator?->id,
                    'height' => $category->cargoConfigurator?->height,
                    'width' => $category->cargoConfigurator?->width,
                    'length' => $category->cargoConfigurator?->length,
                ];
            });
        }

        $this->serialNumbers = $this->getSerialNumbers($this->sx_ordernumber, $suffix);
        $this->phone = $this->orderInfo->customer?->phone;
        $this->email = $this->orderInfo->customer?->email;
        $this->contactError = match (true) {
            !$this->phone && !$this->email => "Contact info is missing.",
            !$this->phone => "Contact number is missing.",
            !$this->email => "Contact email is missing.",
            default => null,
        };
        $this->service_address =  ($this->orderInfo?->shipping_info['line'] ?? '') . ', ';

        if (!empty($this->orderInfo?->shipping_info['line2'])) {
            $this->service_address .= $this->orderInfo?->customer?->address2.', ';
        }

        $this->service_address .= $this->orderInfo?->shipping_info['city'] . ", " .
            $this->orderInfo?->shipping_info['state'] . " " .$this->orderInfo?->shipping_info['zip'];

        $this->service_address = trim($this->service_address);
        $this->serviceZip = $this->orderInfo?->shipping_info['zip'];
        $this->addressFromOrder = $this->service_address;

        $response = $this->validateAddress($this->service_address, $this->serviceZip);
        return $response;
    }

    public function getTruckSchedules($whse)
    {
        $truckScheduleQuery = DB::table('truck_schedules')
        ->whereNull('truck_schedules.deleted_at')
        ->join('zipcode_zone', 'truck_schedules.zone_id', '=', 'zipcode_zone.zone_id')
        ->join('scheduler_zipcodes', 'zipcode_zone.scheduler_zipcode_id', '=', 'scheduler_zipcodes.id')
        ->whereNull('scheduler_zipcodes.deleted_at')
        ->join('zones', 'zipcode_zone.zone_id', '=', 'zones.id')
        ->whereNull('zones.deleted_at')
        ->join('trucks', 'truck_schedules.truck_id', '=', 'trucks.id')
        ->whereNull('trucks.deleted_at')
        ->when($this->type == ScheduleEnum::delivery->value, function($query) {
            return $query->where('is_delivery', 1);
        })
        ->when($this->type == ScheduleEnum::pickup->value, function($query) {
            return $query->where('is_pickup', 1);
        });
        if($this->scheduleType == 'schedule_override' && Auth::user()->can('scheduler.can-schedule-override')) {
            $zones = Zones::where('service', 'pickup_delivery')->where('is_active',1)->pluck('id');
        } else {
            $zonesQuery = Zones::where('is_active', 1)
                ->where('service', 'pickup_delivery');
            $zip = $this->serviceZip;
            $zonesQuery->where('whse_id', $whse)->whereHas('zipcodes', function ($query) use($zip) {
                $query->where('zip_code', $zip);
            });
            $zones = $zonesQuery->pluck('id');
        }

        $this->truckSchedules = $truckScheduleQuery->whereIn('truck_schedules.zone_id', $zones)
        ->where('truck_schedules.schedule_date', '=', $this->schedule_date)
        ->select(
            'truck_schedules.*',
            'zones.name as zone_name',
            'trucks.id as truck_id',
            'trucks.truck_name',
            'trucks.height as truck_height',
            'trucks.width as truck_width',
            'trucks.length as truck_length',
            DB::raw('(SELECT COUNT(*) FROM schedules WHERE truck_schedule_id = truck_schedules.id and status <> "cancelled") as schedule_count')
        )
        ->distinct()
        ->get();
    }

    public function getEnabledDates($shouldOverride, $whse)
    {
        $zonesQuery = Zones::where('is_active', 1)
            ->where('service', 'pickup_delivery');
        $zip = $this->serviceZip;
        if (!$shouldOverride) {
            $zonesQuery->where('whse_id', $whse)->whereHas('zipcodes', function ($query) use($zip) {
                $query->where('zip_code', $zip);
            });
        }
        $zones = $zonesQuery->pluck('id');

        // Optimize the schedule counts subquery
        $scheduleCounts = DB::table('schedules')
            ->whereNull('deleted_at')
            ->where('status', '!=', 'cancelled')
            ->select('truck_schedule_id', DB::raw('COUNT(*) as scheduled_count'))
            ->groupBy('truck_schedule_id');

        // Main query with necessary joins preserved
        $this->enabledDates = DB::table('truck_schedules')
            ->whereNull('truck_schedules.deleted_at')
            ->join('zipcode_zone', 'truck_schedules.zone_id', '=', 'zipcode_zone.zone_id')
            ->join('scheduler_zipcodes', 'zipcode_zone.scheduler_zipcode_id', '=', 'scheduler_zipcodes.id')
            ->whereNull('scheduler_zipcodes.deleted_at')
            ->join('zones', 'zipcode_zone.zone_id', '=', 'zones.id')
            ->whereNull('zones.deleted_at')
            ->join('trucks', 'truck_schedules.truck_id', '=', 'trucks.id')
            ->whereNull('trucks.deleted_at')
            ->when(!$shouldOverride, function($query) use ($scheduleCounts) {
                return $query->leftJoinSub($scheduleCounts, 'sc', function ($join) {
                    $join->on('truck_schedules.id', '=', 'sc.truck_schedule_id');
                });
            })
            ->when($this->type == ScheduleEnum::delivery->value, function($query) {
                return $query->where('is_delivery', 1);
            })
            ->when($this->type == ScheduleEnum::pickup->value, function($query) {
                return $query->where('is_pickup', 1);
            })
            ->whereIn('truck_schedules.zone_id', $zones)

            ->whereDate('truck_schedules.schedule_date', '>=', now()->toDateString())
            ->when(!$shouldOverride, function($query) {
                return $query->whereRaw('COALESCE(sc.scheduled_count, 0) < truck_schedules.slots');
            })
            ->select('truck_schedules.schedule_date')
            ->distinct()
            ->pluck('schedule_date')
            ->toArray();
    }

    public function updatedAddress()
    {
        $this->addressKey = uniqid();
        $whse = $this->orderInfo->warehouse->id;
        $this->serviceZip = $this->extractZipCode($this->service_address);
        $zipcodeInfo = Zipcode::with([
            'zones' => function ($query) use ($whse) {
                $query->where('whse_id', $whse)->where('service', 'pickup_delivery');
            },
            'warehouse:id,title'
        ])
        ->where('zip_code', $this->serviceZip)
        ->whereHas('zones', function ($query) use ($whse) {
            $query->where('whse_id', $whse)->where('service', 'pickup_delivery');
        })
        ->get();

        if ($zipcodeInfo->isNotEmpty()) {
            $this->zipcodeInfo = $zipcodeInfo->toArray();
        }
        $this->reset('alertConfig');
        $this->alertConfig['status'] = true;
        if(!$zipcodeInfo) {
            $this->zipcodeInfo = null;
            $this->alertConfig['message'] = 'ZIP Code not configured';
            $this->alertConfig['icon'] = 'fa-times-circle';
            $this->alertConfig['class'] = 'danger';
            $this->alertConfig['show_url'] = true;
            $this->alertConfig['urlText'] = 'Configure ZIP';
            $this->alertConfig['url'] = 'service-area.index';
            $this->alertConfig['params'] = 'tab=zip_code';
            $this->reset(['schedule_date', 'schedule_time', 'scheduleType', 'ServiceStatus']);
            return;
        }
        $this->getDistance();
        $this->checkServiceValidity();
        $this->getEnabledDates(false, $this->orderInfo?->warehouse->id);
    }


    public function validateAddress($address, $zip)
    {
        $this->addressVerified = false;

        $addressString = $address;
        $google = app(DistanceInterface::class);
        $address=[
            'regionCode' => 'US',
            'addressLines' => $address,
            //'zip' => $zip
        ];

        $recom =  $google->addressValidation($address);
        if($recom->status() != 200) {

              return [
                'status' => false,
                'message' => 'Failed to Validate Address'
              ];
        }

        $zipParts = explode('-', $recom['result']['address']['postalAddress']['postalCode']);
        $zipcode =  $zipParts[0] ?? null;

        if (isset($recom['result']['address']['unconfirmedComponentTypes'])) {
            // Filter out "country" from the array
            $tempArray = array_filter($recom['result']['address']['unconfirmedComponentTypes'], function ($value) {
                return $value !== 'country';
            });
            $tempArray = array_values($tempArray);
        }

        if($zipcode != $zip) {

            $tempArray[] = 'postal-code';
        }

        if (!empty($tempArray)) {
            $this->unconfirmedAddressTypes = $tempArray;
            $this->showAddressModal = true;
            $this->recommendedAddress = $recom['result']['address']['formattedAddress'];
            $this->checkZipcode();
            return [
                'status' => false,
                'message' => 'Service Address is not complete'
            ];
        }

        $this->reset([
            'unconfirmedAddressTypes'
        ]);
        $this->showAddressModal = false;

        $this->service_address = $recom['result']['address']['formattedAddress'];
        $this->addressVerified = true;
        $this->checkZipcode();
        return [
            'status' => true,
            'message' => ''
        ];
    }

    public function checkZipcode()
    {
        $this->getDistance();
        $whse = $this->orderInfo->warehouse->id;
        $zipcodeInfo = Zipcode::with([
            'zones' => function ($query) use ($whse) {
                $query->where('whse_id', $whse)->where('service', 'pickup_delivery');
            },
            'warehouse:id,title'
        ])
        ->where('zip_code', $this->serviceZip)
        ->whereHas('zones', function ($query) use ($whse) {
            $query->where('whse_id', $whse)->where('service', 'pickup_delivery');
        })
        ->get();

        if ($zipcodeInfo->isNotEmpty()) {
            $this->zipcodeInfo = $zipcodeInfo->toArray();
        }

        $this->reset('alertConfig');
        $this->alertConfig['status'] = true;
        if(!$this->zipcodeInfo) {
            $this->alertConfig['message'] = 'ZIP Code not configured';
            $this->alertConfig['icon'] = 'fa-times-circle';
            $this->alertConfig['class'] = 'danger';
            $this->alertConfig['show_url'] = true;
            $this->alertConfig['urlText'] = 'Configure ZIP';
            $this->alertConfig['url'] = 'service-area.index';
            $this->alertConfig['params'] = 'tab=zip_code';
            $this->reset(['schedule_date', 'schedule_time', 'scheduleType', 'ServiceStatus']);
            return;
        }
        $this->checkServiceValidity();
        $this->getEnabledDates(false, $this->orderInfo?->warehouse?->id);
    }

    public function checkServiceValidity()
    {
        $this->ServiceStatus = $this->checkServiceAVailability($this->type);
        if(!$this->ServiceStatus) {
            $this->alertConfig['message'] = 'This ZIP Code is not eligible for <strong>'.Str::of($this->type)->replace('_', ' ')->title().'</strong>';
            $this->alertConfig['icon'] = 'fa-times-circle';
            $this->alertConfig['class'] = 'danger';
            $this->reset(['schedule_date', 'schedule_time', 'scheduleType']);
            return;
        }
        $this->alertConfig['message'] = 'This ZIP Code is eligible for <strong>'.Str::of($this->type)->replace('_', ' ')->title().'</strong>';
        $this->alertConfig['icon'] = 'fa-check-circle';
        $this->alertConfig['class'] = 'success';

    }
    public function store()
    {
        $validatedData = $this->validate(collect($this->rules())->only([
            'type',
            'sx_ordernumber',
            'suffix',
            'notes',
            'line_item',
            'service_address',
            'scheduleType',
            'schedule_date',
            'schedule_time',
            'not_purchased_via_weingartz',
        ])->toArray());
        if(!$this->ServiceStatus) {
            return ['status' =>false, 'class'=> 'error', 'message' =>'Failed to save'];
        }

        $existingSchedules = Schedule::where('sx_ordernumber', $this->sx_ordernumber)
        ->whereNotIn('status', ['cancelled', 'completed'])
        ->whereBetween('schedule_date', [
            Carbon::parse($this->schedule_date)->subMonths(6),
            Carbon::parse($this->schedule_date)->addMonths(6)
        ])
        ->get();
        if($existingSchedules->count() >=1 ) {
            $this->addError('sx_ordernumber', 'Order number already scheduled within six months');
            return ['status' =>false, 'class'=> 'error', 'message' =>'Failed to save'];
        }

        $validatedData['status'] = 'scheduled';
        $validatedData['created_by'] = Auth::user()->id;
        $validatedData['order_number_suffix'] = $this->suffix;
        $validatedData['truck_schedule_id'] = $this->schedule_time;
        $validatedData['schedule_type'] = $this->scheduleType;
        $validatedData['whse'] = $this->selectedTruckSchedule->truck->warehouse_short;
        $validatedData['phone'] = $this->phone;
        $validatedData['email'] = $this->email;

        $itemDesc = collect($this->orderInfo->line_items['line_items'])
        ->whereIn('shipprod', (array) $this->line_item) // Convert to array if not already
        ->pluck('descrip', 'shipprod') // Get descriptions with their corresponding shipprod values
        ->toArray();
        $validatedData['not_purchased_via_weingartz'] = 0;
        $validatedData['line_item'] = $itemDesc;

        $validatedData['serial_no'] = collect($this->serialNumbers)->firstWhere('prod',$this->line_item)?->serialno;

        $schedule = Schedule::create($validatedData);
        if($this->notes) {
            $schedule->comments()->create([
                'comment' => $this->notes,
                'user_id' => Auth::user()->id
            ]);
        }
        if( $this->scheduleType == 'schedule_override' && $schedule->truckSchedule->schedule_count > $schedule->truckSchedule->slots) {
            $schedule->truckSchedule->slots = $schedule->truckSchedule->slots + 1;
            $schedule->truckSchedule->save();
        }
        if($this->notifyUser) {
            //EventScheduled::dispatch($schedule);
        }

        return ['status' =>true, 'class'=> 'success', 'message' =>'New schedule Created', 'schedule' => $schedule];
    }


    public function linkSRONumber($sro)
    {
        $this->schedule->status = 'scheduled_linked';
        $this->schedule->sro_linked_by = Auth::user()->id;
        $this->schedule->sro_linked_at = Carbon::now();
        $this->schedule->sro_number = $sro;
        $this->schedule->save();
        return ['status' =>true, 'class'=> 'success', 'message' =>'SRO number successfully linked', 'schedule' => $this->schedule];
    }

    public function confirmSchedule()
    {
        $this->schedule->status = 'confirmed';
        $this->schedule->confirmed_by = Auth::user()->id;
        $this->schedule->confirmed_at = Carbon::now();
        $this->schedule->save();
        if($this->schedule->schedule_date->format('Y-m-d') == Carbon::now()->format('Y-m-d')) {
            Artisan::call('app:route-finder');
        }
        return ['status' =>true, 'class'=> 'success', 'message' =>'Schedule confirmed successfully', 'schedule' => $this->schedule];
    }

    public function unConfirm()
    {
        $this->schedule->status = 'scheduled_linked';
        $this->schedule->expected_arrival_time = null;
        $this->schedule->travel_prio_number = null;
        $this->schedule->save();
        if($this->schedule->schedule_date->format('Y-m-d') == Carbon::now()->format('Y-m-d')) {
            Artisan::call('app:route-finder');
        }
        return ['status' =>true, 'class'=> 'success', 'message' =>'Schedule Unconfirmed', 'schedule' => $this->schedule];
    }

    public function unlinkSro()
    {
        $this->schedule->status = 'scheduled';
        $this->schedule->sro_number = null;
        $this->schedule->save();
        return ['status' =>true, 'class'=> 'success', 'message' =>'SRO Number Unlinked', 'schedule' => $this->schedule];
    }

    public function cancelSchedule()
    {
        $this->validateOnly('cancel_reason');
        $this->schedule->status = 'cancelled';
        $this->schedule->cancel_reason = $this->cancel_reason;
        $this->schedule->cancelled_at = Carbon::now();
        $this->schedule->cancelled_by = Auth::user()->id;
        $this->schedule->save();
        if($this->notifyUser) {
            //EventCancelled::dispatch($this->schedule);
        }

        return ['status' =>true, 'class'=> 'success', 'message' =>'schedule cancelled', 'schedule' => $this->schedule];
    }

    public function undoCancel()
    {
        $this->schedule->status = 'scheduled';
        $this->schedule->sro_number = null;
        $this->schedule->cancel_reason = null;
        $this->schedule->save();
        $this->fill($this->schedule);
        return ['status' =>true, 'class'=> 'success', 'message' =>'schedule Uncancelled', 'schedule' => $this->schedule];
    }

    public function startSchedule()
    {
        $this->schedule->status = 'out_for_delivery';
        $this->schedule->started_by = Auth::user()->id;
        $this->schedule->started_at = Carbon::now();
        $this->schedule->save();
        $this->fill($this->schedule);
        if($this->notifyUser) {
            //EventDispatched::dispatch($this->schedule);
        }
        return ['status' =>true, 'class'=> 'success', 'message' =>'Delivery initiated', 'schedule' => $this->schedule];
    }

    public function completeSchedule()
    {
        $this->validateOnly('tag_number');
        $this->schedule->tag_number = $this->tag_number;
        $this->schedule->status = 'completed';

        $this->schedule->completed_at = Carbon::now();
        $this->schedule->completed_by = Auth::user()->id;
        $this->schedule->save();
        if($this->notifyUser) {
            //EventComplete::dispatch($this->schedule);
        }
        return ['status' =>true, 'class'=> 'success', 'message' =>'schedule completed', 'schedule' => $this->schedule];

    }



    public function init(Schedule $schedule)
    {
        $this->schedule = $schedule;
        $this->fill($schedule->toArray());
        $this->phone = $this->phone ? $this->phone : $this->schedule->order->customer?->phone;
        $this->email = $this->email ? $this->email : $this->schedule->order->customer?->email;
        $this->line_item = $schedule->line_item;
        $this->reset(['schedule_date']);
        $this->suffix = $schedule->order_number_suffix;
        $this->serviceZip = $this->extractZipCode($this->service_address);

        $this->getTruckSchedules($this->schedule->warehouse->id);
        $holidays = CalendarHoliday::listAll();
        $this->disabledDates = array_column($holidays, 'date');
    }

    public function update()
    {
        $validatedData = $this->validate(collect($this->rules())->only([
            'scheduleType',
            'schedule_date',
            'schedule_time',
            'reschedule_reason'
        ])->toArray());
        $existingSchedules = Schedule::where('sx_ordernumber', $this->sx_ordernumber)
        ->whereNotIn('status', ['cancelled', 'completed'])
        ->where('id', '!=', $this->schedule->id)
        ->whereBetween('schedule_date', [
            Carbon::parse($this->schedule_date)->subMonths(6),
            Carbon::parse($this->schedule_date)->addMonths(6)
        ])
        ->get();
        if($existingSchedules->count() >=1 ) {
            $this->addError('schedule_date', 'Order number already scheduled within six months');
            return ['status' =>false, 'class'=> 'error', 'message' =>'Failed to save'];
        }
        $oldDate = $this->schedule->schedule_date;
        $validatedData['truck_schedule_id'] = $this->schedule_time;
        $validatedData['whse'] = $this->selectedTruckSchedule->truck->warehouse_short;

        $this->schedule->fill($validatedData);
        $this->schedule->save();
        activity()
        ->performedOn($this->schedule)
        ->event('updated')
        ->causedBy(Auth::user())
        ->withProperties([
            'old' => [
                'schedule_date' => $oldDate,
            ],
            'attributes' => [
                'schedule_date' => $this->schedule->schedule_date,
            ]
        ])
        ->log("Rescheduled from {$oldDate} to {$this->schedule->schedule_date} by " . Auth::user()->name);
        $this->selectedTruckSchedule = $this->selectedTruckSchedule->fresh();
        if($this->scheduleType == 'schedule_override' && $this->selectedTruckSchedule->schedule_count > $this->selectedTruckSchedule->slots) {
            $this->selectedTruckSchedule->slots = $this->selectedTruckSchedule->slots + 1;
            $this->selectedTruckSchedule->save();

        }
        $this->scheduleType = null;
        $this->schedule_date = null;
        $this->schedule_time = null;
        if($this->notifyUser) {
            //EventRescheduled::dispatch($this->schedule);
        }

        return ['status' =>true, 'class'=> 'success', 'message' =>'schedule updated', 'schedule' => $this->schedule];
    }
    public function checkServiceAVailability($value)
    {
        if(!$this->orderInfo ||  !$this->zipcodeInfo) {
            return false;
        }
        foreach($this->zipcodeInfo as $zipcode) {
            foreach($zipcode['zones'] as $zone)
            {
                if($zone['service'] == 'pickup_delivery' ) {
                    if($value == 'pickup' || $value == 'delivery') {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
