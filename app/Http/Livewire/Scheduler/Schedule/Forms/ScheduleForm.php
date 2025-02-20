<?php
 namespace App\Http\Livewire\Scheduler\Schedule\Forms;

use App\Classes\SX;
use App\Contracts\DistanceInterface;
use App\Events\Scheduler\EventCancelled;
use App\Events\Scheduler\EventComplete;
use App\Events\Scheduler\EventDispatched;
use App\Events\Scheduler\EventRescheduled;
use App\Events\Scheduler\EventScheduled;
use App\Models\Core\CalendarHoliday;
use App\Models\Core\Warehouse;
use App\Models\Order\Order;
use App\Models\Scheduler\NotificationTemplate;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Livewire\Form;

class ScheduleForm extends Form
{
    public ?Schedule $schedule;

    public $type;
    public $sx_ordernumber;
    public $suffix;
    public $schedule_date;
    public $schedule_time;
    public $status;
    public $orderInfo;
    protected $SXOrderInfo;
    public $zipcodeInfo;
    public $created_by;
    public $shipping;
    public $orderTotal;
    public $disabledDates;
    public $truckSchedules = [];
    public $enabledDates = [];
    public $scheduleType;
    public $ServiceStatus = false;
    public $serialNumbers;
    public $line_item;
    public $serviceZip;
    public $notes;
    public $addressKey = '1232234';
    public $service_address;
    public $service_address_temp; //used for updating address
    public $cancel_reason;
    public $reschedule_reason;
    public $unconfirmedAddressTypes;
    public $showAddressModal;
    public $showAddressBox;
    public $not_purchased_via_weingartz;
    public $addressVerified = false;
    public $addressFromOrder;
    public $selectedTruckSchedule;
    public $recommendedAddress;

    public $alertConfig = [
        'status' => false,
        'message' => '',
        'icon' => '',
        'class' => '',
        'show_url' => false,
        'url' => '',
        'params' => '',
        'urlText' => '',
    ];

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
            'line_item' => $this->not_purchased_via_weingartz ? 'nullable' : 'required',
            'notes' =>'nullable',
            'service_address' =>'required',
            'reschedule_reason' =>'nullable|string|max:225',
            'cancel_reason' => 'required|string|max:220',
            'not_purchased_via_weingartz' => 'nullable'
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
                    'orderInfo'

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
                'orderInfo'

            ]);
            return;
        }

        $this->orderTotal = (config('sx.mock')) ? '234.25' : number_format($this->SXOrderInfo->totordamt,2);
        if(is_null($this->orderInfo->shipping_info) || empty($this->orderInfo?->shipping_info['line'])) {
            $this->addError('sx_ordernumber', 'Shipping info missing');
            return;
        }
        if(empty($this->orderInfo->line_items['line_items'])) {
            $this->not_purchased_via_weingartz = true;
        }

        $this->serialNumbers = $this->getSerialNumbers($this->sx_ordernumber, $suffix);

        $this->service_address =  ($this->orderInfo?->shipping_info['line'] ?? '') . ', ';

        if (!empty($this->orderInfo?->shipping_info['line2'])) {
            $this->service_address .= $this->orderInfo?->customer?->address2.', ';
        }

        $this->service_address .= $this->orderInfo?->shipping_info['city'] . ", " .
            $this->orderInfo?->shipping_info['state'] . " " .$this->orderInfo?->shipping_info['zip'];

        $this->service_address = trim($this->service_address);
        $this->serviceZip = $this->orderInfo?->shipping_info['zip'];
        $this->addressFromOrder = $this->service_address;

        $this->validateAddress($this->service_address, $this->serviceZip);
    }

    public function checkZipcode()
    {
        $this->getDistance();
        $this->zipcodeInfo = Zipcode::with('zones')->where('zip_code', $this->serviceZip)->first();
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

    public function checkServiceAVailability($value)
    {
        if(!$this->orderInfo ||  !$this->zipcodeInfo) {
            return false;
        }
        foreach($this->zipcodeInfo?->zones as $zone)
        {
            if(strtolower($zone->service) == 'ahm' && $value == 'at_home_maintenance' ) {
                return true;
            }
            if($zone->service == 'Pickup/Delivery' ) {
                if($value == 'pickup' || $value == 'delivery') {
                    return true;
                }
            }
        }

        return false;
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

        if($this->not_purchased_via_weingartz) {
            $validatedData['line_item'] = null;
        } else {
            $itemDesc = collect($this->orderInfo->line_items['line_items'])->firstWhere('shipprod', $this->line_item)['descrip'] ?? null;
            $validatedData['line_item'] = [$this->line_item=>$itemDesc];
            $validatedData['not_purchased_via_weingartz'] = 0;
            $validatedData['serial_no'] = collect($this->serialNumbers)->firstWhere('prod',$this->line_item)?->serialno;
        }


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

        EventScheduled::dispatch($schedule);

        return ['status' =>true, 'class'=> 'success', 'message' =>'New schedule Created', 'schedule' => $schedule];
    }

    public function init(Schedule $schedule)
    {
        $this->schedule = $schedule;
        $this->schedule_time = $schedule->truck_schedule_id;
        $this->fill($schedule->toArray());
        $this->line_item = $schedule->line_item ? key($schedule->line_item): null;
        $this->schedule_date = Carbon::parse($schedule->schedule_date)->format('Y-m-d');
        $this->suffix = $schedule->order_number_suffix;
        $this->scheduleType = $schedule->schedule_type;
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
        $validatedData['truck_schedule_id'] = $this->schedule_time;

        $this->schedule->fill($validatedData);
        $this->schedule->save();
        $this->selectedTruckSchedule = $this->selectedTruckSchedule->fresh();
        if($this->scheduleType == 'schedule_override' && $this->selectedTruckSchedule->schedule_count > $this->selectedTruckSchedule->slots) {
            $this->selectedTruckSchedule->slots = $this->selectedTruckSchedule->slots + 1;
            $this->selectedTruckSchedule->save();

        }
        $this->scheduleType = null;
        $this->schedule_date = null;
        $this->schedule_time = null;

        EventRescheduled::dispatch($this->schedule);

        return ['status' =>true, 'class'=> 'success', 'message' =>'schedule updated', 'schedule' => $this->schedule];
    }

    public function delete()
    {
        $this->schedule->delete();
    }

    public function getScheduledId()
    {
        return $this->schedule?->id ?? null;
    }

    public function getTotalInvoiceData($items, $sx_customer_number, $whse )
    {
        $invoice_request = [
            'sx_operator_id' => auth()->user()->sx_operator_id,
            'sx_customer_number' => $sx_customer_number ?? 1,
            'warehouse' => $whse,
            'cart' => $items,
        ];

        $sx = new SX();
        return $sx->get_total_invoice_data($invoice_request);
    }

    public function getActiveDays()
    {
        $holidays = CalendarHoliday::listAll();
        return ['holidays' => $holidays];
    }

    public function getDistance()
    {
        $google = app(DistanceInterface::class);
        $warehouse = Warehouse::where('short' , $this->orderInfo->whse)->first();

        $distance = $google->findDistance($warehouse->address, $this->service_address);

        if ($distance['status'] === 'OK') {
            $elements = $distance['rows'][0]['elements'][0] ?? null;
            $this->shipping['distance'] = $elements['distance']['text'] ?? null;
            $this->shipping['duration'] =   $elements['duration']['text'] ?? null;
        }
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
        ->whereNull('trucks.deleted_at');

        $zones = Zones::whereHas('zipcodes', function ($query) {
            $query->where('zip_code', $this->serviceZip);
        })->pluck('id');

        if($this->scheduleType == 'schedule_override' && Auth::user()->can('scheduler.can-schedule-override')) {
            $zones = Zones::where('is_active',1)->pluck('id');
        }

        $this->truckSchedules = $truckScheduleQuery->whereIn('truck_schedules.zone_id', $zones)
        ->where('truck_schedules.schedule_date', '=', $this->schedule_date)
        ->select(
            'truck_schedules.*',
            'zones.name as zone_name',
            'trucks.id as truck_id',
            'trucks.truck_name',
            DB::raw('(SELECT COUNT(*) FROM schedules WHERE truck_schedule_id = truck_schedules.id and status <> "cancelled") as schedule_count')
        )
        ->distinct()
        ->get();
    }

    public function getEnabledDates($shouldOverride, $whse)
    {
        $zones = Zones::whereHas('zipcodes', function ($query) {
                    $query->where('zip_code', $this->serviceZip);
                })->pluck('id');
        if($shouldOverride) {
            if( Auth::user()->can('scheduler.can-schedule-override')) {
                $zones = Zones::where('is_active',1)->pluck('id');
            }
        }
        $this->enabledDates = DB::table('truck_schedules')
        ->whereNull('truck_schedules.deleted_at')

        ->select(
            'truck_schedules.schedule_date',
        )
        ->whereIn('truck_schedules.zone_id', $zones)
        ->get()
        ->pluck('schedule_date')
        ->unique()
        ->values()
        ->toArray();
    }

    public function getSerialNumbers($orderno, $suffix)
    {
        if (config('sx.mock')) {
            $serials = [];
            foreach ($this->orderInfo->line_items['line_items'] as $item) {
                if (mt_rand(0, 1)) {
                    $obj = new \stdClass();
                    $obj->prod = $item['shipprod'];
                    $obj->serialno = rand(100000, 999999);
                    $serials[] = $obj;
                }
            }
            return $serials;
        }

        return DB::connection('sx')->select("select s.prod,s.serialno from pub.icets s where s.cono = ? and s.ordertype = 'o' and s.orderno = ? and s.ordersuf = ? with(nolock)",[10, $orderno, $suffix]);
    }

    public function linkSRONumber($sro)
    {
        $this->schedule->status = 'scheduled_linked';
        $this->schedule->sro_number = $sro;
        $this->schedule->save();
        return ['status' =>true, 'class'=> 'success', 'message' =>'SRO number successfully linked', 'schedule' => $this->schedule];
    }

    public function confirmSchedule()
    {
        $this->schedule->status = 'confirmed';
        $this->schedule->save();
        return ['status' =>true, 'class'=> 'success', 'message' =>'Schedule confirmed successfully', 'schedule' => $this->schedule];
    }

    public function unConfirm()
    {
        $this->schedule->status = 'scheduled_linked';
        $this->schedule->save();
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
        EventCancelled::dispatch($this->schedule);
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
        $this->schedule->save();
        $this->fill($this->schedule);
        EventDispatched::dispatch($this->schedule);
        return ['status' =>true, 'class'=> 'success', 'message' =>'Delivery initiated', 'schedule' => $this->schedule];
    }

    public function completeSchedule()
    {
        $this->schedule->status = 'completed';
        $this->schedule->completed_at = Carbon::now();
        $this->schedule->completed_by = Auth::user()->id;
        $this->schedule->save();
        EventComplete::dispatch($this->schedule);
        return ['status' =>true, 'class'=> 'success', 'message' =>'schedule completed', 'schedule' => $this->schedule];

    }

    public function updatedAddress()
    {
        $this->addressKey = uniqid();
        $this->serviceZip = $this->extractZipCode($this->service_address);
        $this->zipcodeInfo = Zipcode::with('zones')->where('zip_code', $this->serviceZip)->first();
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
        $this->getDistance();
        $this->checkServiceValidity();
        $this->getEnabledDates(false, $this->orderInfo?->warehouse->id);
    }

    public function extractZipCode($address)
    {
        preg_match('/\b[A-Z]{2}\s+(\d{5})\b/', $address, $matches);

        return $matches[1] ?? null;
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

            return [
                'status' => false,
                'message' => 'Service Address is not complete'
            ];
        }

        $this->reset([
            'unconfirmedAddressTypes'
        ]);
        $this->showAddressModal = false;
        $this->showAddressBox = false;
        $this->service_address = $recom['result']['address']['formattedAddress'];
        $this->addressVerified =true;
        $this->checkZipcode();
    }

    private function syncSXOrder($sx_order)
    {
        $line_items = $this->getSxOrderLineItemsProperty($sx_order['orderno'],$sx_order['ordersuf']);
        $wt_status = $this->checkForWarehouseTransfer($sx_order,$line_items);

        $portal_order = Order::updateOrCreate(
            [
                'order_number' => $sx_order['orderno'],
                'order_number_suffix' => $sx_order['ordersuf'],
                'cono' => $sx_order['cono']
            ],
            [
                'whse' => strtolower($sx_order['whse']),
                'taken_by' => $sx_order['takenby'],
                'order_date' => Carbon::parse($sx_order['enterdt'])->format('Y-m-d'),
                'stage_code' => $sx_order['stagecd'],
                'sx_customer_number' => $sx_order['custno'],
                'is_sro' => $this->isSro($sx_order,$line_items->toArray()),
                'ship_via' => strtolower($sx_order['shipviaty']),
                'qty_ship' => $sx_order['totqtyshp'],
                'qty_ord' => $sx_order['totqtyord'],
                'promise_date' => $sx_order['promisedt'],
                'line_items' => ['line_items' => $line_items->toArray() ?: []],
                'is_sales_order' => $this->isSales($line_items->toArray()),
                'is_web_order' => $sx_order['user6'] == '6' ? 1 : 0,
                'warehouse_transfer_available' => ($wt_status == 'wt') ? true : false,
                'partial_warehouse_transfer_available' => ($wt_status == 'p-wt') ? true : false,
                'golf_parts' => $sx_order['user6'] == '6' ? $sx_order->hasGolfParts($line_items) : null,
                'non_stock_line_items' => $sx_order->hasNonStockItems($line_items),
                'last_line_added_at' => Carbon::parse($sx_order['enterdt'])->format('Y-m-d'),
                'status' => 'Pending Review',
                'shipping_info' => $sx_order->constructAddress($sx_order)
            ]
        );

        return $portal_order;

    }

    private function getSxOrderLineItemsProperty($order_number, $order_suffix, $cono = 10)
    {
        $required_line_item_columns = [
            'oeel.orderno',
            'oeel.ordersuf',
            'oeel.shipto',
            'oeel.lineno',
            'oeel.qtyord',
            'oeel.proddesc',
            'oeel.price',
            'oeel.shipprod',
            'oeel.statustype',
            'oeel.prodcat',
            'oeel.prodline',
            'oeel.specnstype',
            'oeel.qtyship',
            'oeel.ordertype',
            'oeel.netamt',
            'oeel.orderaltno',
            'oeel.user8',
            'oeel.vendno',
            'oeel.whse',
            'oeel.stkqtyord',
            'oeel.stkqtyship',
            'oeel.returnfl',
            'icsp.descrip',
            'icsl.user3',
            'icsl.whse',
            'icsl.prodline',
            'oeel.cono',
            'oeel.enterdt',
        ];

        return OrderLineItem::select($required_line_item_columns)
        ->leftJoin('icsp', function (JoinClause $join) {
            $join->on('oeel.cono','=','icsp.cono')
            ->on('oeel.shipprod', '=', 'icsp.prod');
                //->where('icsp.cono', $this->customer->account->sx_company_number);
        })
        ->leftJoin('icsl', function (JoinClause $join) {
            $join->on('oeel.cono','=','icsl.cono')
                ->on('oeel.whse', '=', 'icsl.whse')
                ->on('oeel.vendno', '=', 'icsl.vendno')
                ->on('oeel.prodline', '=', 'icsl.prodline');
        })
        ->where('oeel.orderno', $order_number)->where('oeel.ordersuf', $order_suffix)
        ->where('oeel.cono', $cono)
        ->orderBy('oeel.lineno', 'asc')
        ->get();
    }

    private function isSales($line_items)
    {
        foreach($line_items as $line_item)
        {
            if(!str_contains($line_item['prodline'], 'LR-E') && str_contains($line_item['prodline'], '-E'))
            {
                return true;
            }
        }

        return false;
    }

    private function isSro($order,$line_items)
    {
        $is_sro = $order['user1'] == 'SRO' ? 1 : 0;

        if($is_sro) return true;

        foreach($line_items as $line_item)
        {
            if(str_contains($line_item['prodline'], 'LR-E'))
            {
                return true;
            }
        }

        return false;

    }

    private function checkForWarehouseTransfer($sx_order, $line_items)
    {
        $line_item_level_statuses = [];

        if($sx_order->isBackOrder())
        {
            foreach($line_items as $line_item)
            {
                $backorder_count = intval($line_item->stkqtyord) - intval($line_item->stkqtyship);

                if($backorder_count > 0 && strtolower($line_item->ordertype) != 't')
                {
                    $inventory_levels = $line_item->checkInventoryLevelsInWarehouses(array_diff(['ann','ceda','farm','livo','utic','wate', 'zwhs', 'ecom'], [strtolower($line_item->whse)]));

                    foreach($inventory_levels as $inventory_level)
                    {
                        $available_stock = $inventory_level->qtyonhand - ($inventory_level->qtycommit + $inventory_level->qtyreservd);

                        if($available_stock >= $backorder_count) $line_item_level_statuses[] = 'wt'; //full wt available
                        if(!($available_stock >= $backorder_count) && $available_stock > 0) $line_item_level_statuses[] = 'p-wt'; //partial wt transfer

                    }
                }
            }
        }

        if(in_array('wt',$line_item_level_statuses)) return 'wt';
        if(in_array('p-wt',$line_item_level_statuses)) return 'p-wt';

        return 'n/a';

    }


}
