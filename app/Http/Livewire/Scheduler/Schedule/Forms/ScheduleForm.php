<?php
 namespace App\Http\Livewire\Scheduler\Schedule\Forms;

use App\Classes\SX;
use App\Contracts\DistanceInterface;
use App\Models\Core\CalendarHoliday;
use App\Models\Core\Warehouse;
use App\Models\Order\Order;
use App\Models\Scheduler\Schedule;
use App\Models\Scheduler\Shifts;
use App\Models\Scheduler\TruckSchedule;
use App\Models\Scheduler\Zipcode;
use App\Models\SX\Order as SXOrder;
use App\Rules\ValidateScheduleDate;
use App\Rules\ValidateScheduleTime;
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
    public $SXOrderInfo;
    public $zipcodeInfo;
    public $scheduleDateDisable = false;
    public $created_by;
    public $shipping;
    public $saveRecommented = false;
    public $orderTotal;
    public $disabledDates;
    public $truckSchedules = [];
    public $enabledDates = [];
    public $scheduleType;
    public $shiftMsg;

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
    ];
    public $serviceArray = [
        'at_home_maintenance' => 'At Home Maintenance',
        'delivery_pickup' => 'Delivery/Pickup',
    ];
    protected function rules()
    {
        return [
            'type' => 'required',
            'sx_ordernumber' => [
                'required',
                Rule::unique('schedules', 'sx_ordernumber')->whereNull('deleted_at')->ignore($this->getScheduledId()),
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
            'schedule_time' =>'required',
        ];

    }

    public function messages()
    {
        return [
            'schedule_time.required' => 'Time slot not selected',
        ];
    }

    public function getOrderInfo($suffix)
    {
        $google = app(DistanceInterface::class);
        $this->saveRecommented = false;
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
            $this->addError('sx_ordernumber', 'order not found');
            $this->reset([
                'zipcodeInfo',
                'scheduleDateDisable',
                'schedule_date',
                'schedule_time',
                'enabledDates' ,
                'truckSchedules',
                'shiftMsg',
                'scheduleType'

            ]);
            return;
        }

        if(empty($this->orderInfo->line_items)) {
            $this->addError('sx_ordernumber', 'Line items not found in this order');
            return;
        }

        if(is_null($this->orderInfo->shipping_info)) {
            $this->addError('sx_ordernumber', 'Shipping info missing');
            return;
        }

        $this->orderTotal = (config('sx.mock')) ? '234.25' : number_format($this->SXOrderInfo->totordamt,2);
        $this->getDistance();
        $this->zipcodeInfo = Zipcode::with('zones')->where('zip_code', $this->orderInfo?->shipping_info['zip'])->first();
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
            if($zone->service == 'ahm' && $value == 'at_home_maintenance' ) {
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
        $validatedData = $this->validate();
        $validatedData['status'] = 'Scheduled';
        $validatedData['created_by'] = Auth::user()->id;
        $validatedData['order_number_suffix'] = $this->suffix;
        $validatedData['truck_schedule_id'] = $this->schedule_time;
        $validatedData['schedule_type'] = $this->scheduleType;
        if($this->saveRecommented) {
            $validatedData['recommended_address'] = array_intersect_key(
                $this->recommendedAddress,
                array_flip(['postalAddress', 'formattedAddress'])
            );
        }
        $schedule = Schedule::create($validatedData);
        return $schedule;
    }

    public function init(Schedule $schedule)
    {
        $this->schedule = $schedule;
        $this->schedule_time = $schedule->truck_schedule_id;
        $this->fill($schedule->toArray());
        $this->schedule_date = Carbon::parse($schedule->schedule_date)->format('Y-m-d');
        $this->suffix = $schedule->order_number_suffix;
        $this->scheduleType = $schedule->schedule_type;
        $this->recommendedAddress =  $this->schedule->recommended_address;
        $this->shiftMsg = 'service is scheduled for '.Carbon::parse($this->schedule_date)->toFormattedDayDateString()
        .' between '
        .$this->schedule->truckSchedule->start_time. ' - '.$this->schedule->truckSchedule->end_time;
        $this->getTruckSchedules();
    }

    public function update()
    {
        $validatedData = $this->validate();
        $validatedData['order_suffix_number'] = $this->suffix;
        $validatedData['schedule_type'] = $this->scheduleType;
        if($this->saveRecommented) {
            $validatedData['recommended_address'] =
            array_intersect_key(
                $this->recommendedAddress,
                array_flip(['postalAddress', 'formattedAddress'])
            );
        }

        $validatedData['truck_schedule_id'] = $this->schedule_time;
        $this->schedule->fill($validatedData);

        $this->schedule->save();
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

    public function setAddress()
    {
        $this->saveRecommented = true;
    }

    public function getDistance()
    {
        $google = app(DistanceInterface::class);
        $warehouse = Warehouse::where('short' , $this->orderInfo->whse)->first();
        $shipto = $this->orderInfo->shipping_info['line'].', ' .$this->orderInfo->shipping_info['line2'].', '
        .$this->orderInfo->shipping_info['city'].', '.$this->orderInfo->shipping_info['state'].', '.$this->orderInfo->shipping_info['zip'];

        $distance = $google->findDistance($warehouse->address, $shipto);

        if ($distance['status'] === 'OK') {
            $elements = $distance['rows'][0]['elements'][0] ?? null;
            $this->shipping['distance'] = $elements['distance']['text'] ?? null;
            $this->shipping['duration'] =   $elements['duration']['text'] ?? null;
        }
    }

    public function getRecomAddress()
    {
        $google = app(DistanceInterface::class);

        $shipto = $this->orderInfo->shipping_info['line'].', ' .$this->orderInfo->shipping_info['line2'].', '
        .$this->orderInfo->shipping_info['city'].', '.$this->orderInfo->shipping_info['state'].', '.$this->orderInfo->shipping_info['zip'];
        $address=[
            'regionCode' => 'US',
            'addressLines' => $shipto,
            'zip' => $this->orderInfo->shipping_info['zip']
        ];

        $recom =  $google->addressValidation($address);
        if($recom->status() == 200) {
              $this->recommendedAddress = $recom['result']['address'];
        }
    }

    public function checkServiceValidity()
    {
        $ServiceStatus = $this->checkServiceAVailability($this->type);
        if(!$ServiceStatus) {
            $this->alertConfig['message'] = 'This ZIP Code is not eligible for <strong>'.Str::of($this->type)->replace('_', ' ')->title().'</strong>';
            $this->alertConfig['icon'] = 'fa-times-circle';
            $this->alertConfig['class'] = 'danger';
            $this->reset(['scheduleDateDisable', 'schedule_date', 'schedule_time']);
            return;
        }
        $this->alertConfig['message'] = 'This ZIP Code is eligible for <strong>'.Str::of($this->type)->replace('_', ' ')->title().'</strong>';
        $this->alertConfig['icon'] = 'fa-check-circle';
        $this->alertConfig['class'] = 'success';

        $this->scheduleDateDisable = false;
    }



    public function getTruckSchedules()
    {

        $this->truckSchedules = DB::table('truck_schedules')
        ->join('zipcode_zone', 'truck_schedules.zone_id', '=', 'zipcode_zone.zone_id')
        ->join('scheduler_zipcodes', 'zipcode_zone.scheduler_zipcode_id', '=', 'scheduler_zipcodes.id')
        ->join('zones', 'zipcode_zone.zone_id', '=', 'zones.id')
        ->join('trucks', 'truck_schedules.truck_id', '=', 'trucks.id')
        ->join('orders', DB::raw("JSON_UNQUOTE(JSON_EXTRACT(orders.shipping_info, '$.zip'))"), '=', 'scheduler_zipcodes.zip_code')
        ->where('orders.order_number', '=', $this->sx_ordernumber)
        ->where('truck_schedules.schedule_date', '=', $this->schedule_date)
        ->select(
            'truck_schedules.*',
            'orders.id as order_id',
            'zones.name as zone_name',
            'trucks.id as truck_id',
            'trucks.truck_name',
            DB::raw('(SELECT COUNT(*) FROM schedules WHERE truck_schedule_id = truck_schedules.id) as schedule_count')
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
        ->join('zipcode_zone', 'truck_schedules.zone_id', '=', 'zipcode_zone.zone_id')
        ->join('scheduler_zipcodes', 'zipcode_zone.scheduler_zipcode_id', '=', 'scheduler_zipcodes.id')
        ->join('orders', DB::raw("JSON_UNQUOTE(JSON_EXTRACT(orders.shipping_info, '$.zip'))"), '=', 'scheduler_zipcodes.zip_code')
        ->where('orders.order_number', '=', $this->sx_ordernumber)
        ->select(
            'truck_schedules.*',
            'orders.id as order_id',
            DB::raw('(SELECT COUNT(*) FROM schedules WHERE truck_schedule_id = truck_schedules.id) as schedule_count')
            )
        ->get();
        $this->enabledDates = $schedules->pluck('schedule_date')->toArray();
    }
}
