<?php
 namespace App\Http\Livewire\Scheduler\Schedule\Forms;

use App\Classes\SX;
use App\Contracts\DistanceInterface;
use App\Events\Scheduler\EventScheduled;
use App\Models\Core\CalendarHoliday;
use App\Models\Core\Warehouse;
use App\Models\Order\Order;
use App\Models\Scheduler\NotificationTemplate;
use App\Models\Scheduler\Schedule;
use App\Models\Scheduler\Shifts;
use App\Models\Scheduler\TruckSchedule;
use App\Models\Scheduler\Zipcode;
use App\Models\SX\Order as SXOrder;
use App\Rules\ValidateScheduleDate;
use App\Rules\ValidateScheduleTime;
use App\Rules\ValidateSlotsforSchedule;
use Carbon\Carbon;
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
    public $allItems = [];
    public $status;
    public $orderInfo;
    protected $SXOrderInfo;
    public $zipcodeInfo;
    public $created_by;
    public $shipping;
    public $saveRecommented = false;
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
                Rule::unique('schedules', 'sx_ordernumber')->whereNull('deleted_at'),
                Rule::exists('orders', 'order_number')
                ->where(function ($query) {
                    $query->where('order_number_suffix', $this->suffix);
                }),
            ],
            'suffix' => 'required',
            'schedule_date' => [
                'required',
                new ValidateScheduleDate($this->getActiveDays())
            ],
            'scheduleType' =>'required',
            'schedule_time' =>[
                'required',
                new ValidateSlotsforSchedule()
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
        $this->alertConfig['status'] = false;
        if(!$this->sx_ordernumber) {
            $this->addError('sx_ordernumber', 'Order Number is required');
            return;
        }

        if(!is_numeric($suffix)) {
            $this->addError('sx_ordernumber', 'Order Suffix is required');
            return;
        }


        $this->orderInfo = Order::where(['order_number' =>$this->sx_ordernumber, 'order_number_suffix' => $suffix])
            ->first();

        $this->SXOrderInfo = (config('sx.mock')) ? [] : SXOrder::where('cono', 10)->where('orderno', $this->sx_ordernumber)->where('ordersuf', $suffix)->first();

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
        if(is_null($this->orderInfo->shipping_info)) {
            $this->addError('sx_ordernumber', 'Shipping info missing');
            return;
        }


        $this->service_address =  ($this->orderInfo?->shipping_info['line'] ?? '') . ', ';

        if (!empty($this->orderInfo?->shipping_info['line2'])) {
            $this->service_address .= $this->orderInfo?->customer?->address2.', ';
        }

        $this->service_address .= $this->orderInfo?->shipping_info['city'] . ", " .
            $this->orderInfo?->shipping_info['state'] . " " .$this->orderInfo?->shipping_info['zip'];

        $this->service_address = trim($this->service_address);
        $this->serviceZip = $this->orderInfo?->shipping_info['zip'];

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
        $this->getEnabledDates();
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

        $validatedData['status'] = 'Scheduled';
        $validatedData['created_by'] = Auth::user()->id;
        $validatedData['order_number_suffix'] = $this->suffix;
        $validatedData['truck_schedule_id'] = $this->schedule_time;
        $validatedData['schedule_type'] = $this->scheduleType;
        if($this->not_purchased_via_weingartz) {
            $validatedData['line_item'] = null;
        } else {
            $itemDesc = collect($this->orderInfo->line_items['line_items'])->firstWhere('shipprod', $this->line_item)['descrip'] ?? null;
            $validatedData['line_item'] = [$this->line_item=>$itemDesc];
            $validatedData['not_purchased_via_weingartz'] = 0;
        }


        $schedule = Schedule::create($validatedData);
        if($this->notes) {
            $schedule->comments()->create([
                'comment' => $this->notes,
                'user_id' => Auth::user()->id
            ]);
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
        $this->recommendedAddress =  $this->schedule->recommended_address;
        $this->getTruckSchedules();
    }

    public function update()
    {
        $validatedData = $this->validate(collect($this->rules())->only([
            'scheduleType',
            'schedule_date',
            'schedule_time',
            'reschedule_reason'
        ])->toArray());

        $validatedData['truck_schedule_id'] = $this->schedule_time;
        $this->schedule->fill($validatedData);
        $this->schedule->save();
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

    public function setAddress($status = null)
    {
        $this->addressKey = uniqid();
        if(!$status) {
            $this->service_address = $this->recommendedAddress['formattedAddress'];
            return;
        }
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

    public function getRecomAddress()
    {
        $google = app(DistanceInterface::class);
        $this->serviceZip = $this->extractZipCode($this->service_address);
        $address=[
            'regionCode' => 'US',
            'addressLines' => $this->service_address,
            'zip' => $this->serviceZip
        ];

        $recom =  $google->addressValidation($address);
        if($recom->status() == 200) {
              $this->recommendedAddress = $recom['result']['address'];
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



    public function getTruckSchedules()
    {

        $this->truckSchedules = DB::table('truck_schedules')
        ->whereNull('truck_schedules.deleted_at')
        ->join('zipcode_zone', 'truck_schedules.zone_id', '=', 'zipcode_zone.zone_id')
        ->join('scheduler_zipcodes', 'zipcode_zone.scheduler_zipcode_id', '=', 'scheduler_zipcodes.id')
        ->whereNull('scheduler_zipcodes.deleted_at')
        ->join('zones', 'zipcode_zone.zone_id', '=', 'zones.id')
        ->whereNull('zones.deleted_at')
        ->join('trucks', 'truck_schedules.truck_id', '=', 'trucks.id')
        ->whereNull('trucks.deleted_at')
        ->where('scheduler_zipcodes.zip_code', $this->serviceZip)
        ->where('truck_schedules.schedule_date', '=', $this->schedule_date)
        ->select(
            'truck_schedules.*',
            'zones.name as zone_name',
            'trucks.id as truck_id',
            'trucks.truck_name',
            DB::raw('(SELECT COUNT(*) FROM schedules WHERE truck_schedule_id = truck_schedules.id and status <> "Cancelled") as schedule_count')
        )
        ->get();

    }

    public function calendarInit()
    {
        $holidays = CalendarHoliday::listAll();
        $this->disabledDates = array_column($holidays, 'date');
    }

    public function getEnabledDates()
    {
        $schedules = DB::table('truck_schedules')
        ->whereNull('truck_schedules.deleted_at')
        ->join('zipcode_zone', 'truck_schedules.zone_id', '=', 'zipcode_zone.zone_id')
        ->join('scheduler_zipcodes', 'zipcode_zone.scheduler_zipcode_id', '=', 'scheduler_zipcodes.id')
        ->whereNull('scheduler_zipcodes.deleted_at')
        ->select(
            'truck_schedules.*',
            DB::raw('(SELECT COUNT(*) FROM schedules WHERE truck_schedule_id = truck_schedules.id and status <> "Cancelled") as schedule_count')
        )
        ->where('scheduler_zipcodes.zip_code', $this->serviceZip)
        ->get();
        $this->enabledDates = $schedules->pluck('schedule_date')->toArray();
    }

    public function getSerialNumbers($orderno, $suffix)
    {
        if(config('sx.mock'))
        {
           $serials = [];
            foreach($this->orderInfo->line_items['line_items'] as $item)
            {
                if (mt_rand(0,1))
                {
                    $serials[] = ['prod' => $item['shipprod'], 'serialno' => rand ( 100000 , 999999 )];
                }
            }

            return $serials;
        }

        return DB::connection('sx')->select("select s.prod,s.serialno from pub.icets s where s.cono = ? and s.ordertype = 'o' and s.orderno = ? and s.ordersuf = ? with(nolock)",[10, $orderno, $suffix]);
    }

    public function linkSRONumber($sro)
    {
        $this->schedule->sro_number = $sro;
        $this->schedule->status = 'Confirmed';
        $this->schedule->confirmed_at = Carbon::now();
        $this->schedule->confirmed_by = Auth::user()->id;
        $this->schedule->save();
        return ['status' =>true, 'class'=> 'success', 'message' =>'SRO number successfully linked', 'schedule' => $this->schedule];
    }

    public function unlinkSRO()
    {
        $this->schedule->sro_number = null;
        $this->schedule->status = 'Scheduled';
        $this->schedule->save();
        return ['status' =>true, 'class'=> 'success', 'message' =>'Schedule Unconfirmed', 'schedule' => $this->schedule];
    }

    public function cancelSchedule()
    {
        $this->validateOnly('cancel_reason');
        $this->schedule->status = 'Cancelled';
        $this->schedule->cancel_reason = $this->cancel_reason;
        $this->schedule->cancelled_at = Carbon::now();
        $this->schedule->cancelled_by = Auth::user()->id;
        $this->schedule->save();
        return ['status' =>true, 'class'=> 'success', 'message' =>'schedule cancelled', 'schedule' => $this->schedule];
    }

    public function undoCancel()
    {
        $this->schedule->status = 'Scheduled';
        $this->schedule->cancel_reason = null;
        $this->schedule->save();
        $this->fill($this->schedule);
        return ['status' =>true, 'class'=> 'success', 'message' =>'schedule Uncancelled', 'schedule' => $this->schedule];
    }

    public function startSchedule()
    {
        $this->schedule->status = 'Out for Delivery';
        $this->schedule->save();
        $this->fill($this->schedule);
        return ['status' =>true, 'class'=> 'success', 'message' =>'Delivery initiated', 'schedule' => $this->schedule];
    }

    public function completeSchedule()
    {
        $this->schedule->status = 'Completed';
        $this->schedule->completed_at = Carbon::now();
        $this->schedule->completed_by = Auth::user()->id;
        $this->schedule->save();
        return ['status' =>true, 'class'=> 'success', 'message' =>'schedule completed', 'schedule' => $this->schedule];

    }

    public function updatedAddress()
    {
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
        $this->getEnabledDates();
    }

    public function extractZipCode($address)
    {
        preg_match('/\b[A-Z]{2}\s+(\d{5})\b/', $address, $matches);

        return $matches[1] ?? null;
    }

    public function validateAddress($address, $zip)
    {
        $addressString = $address;
        $google = app(DistanceInterface::class);
        $address=[
            'regionCode' => 'US',
            'addressLines' => $address,
            'zip' => $zip
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
        $this->service_address = $addressString;
        $this->checkZipcode();
    }
}
