<?php
 namespace App\Http\Livewire\Scheduler\Schedule\Forms;

use App\Classes\SX;
use App\Contracts\DistanceInterface;
use App\Models\Core\Warehouse;
use App\Models\Order\Order;
use App\Models\Scheduler\Schedule;
use App\Models\Scheduler\Zipcode;
use App\Rules\ValidateScheduleDate;
use App\Rules\ValidateScheduleTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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
    public $line_items = [];
    public $status;
    public $orderInfo;
    public $zipcodeInfo;
    public $scheduleDateDisable = true;
    public $created_by;
    public $shipping;
    public $orderTotal;
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
        'schedule_date' => 'Schedule Date',
        'schedule_time' => 'Schedule Time',
        'line_items' => 'Line Items',
        'suffix' => 'Order Suffix',
    ];
    public $serviceArray = [
        'at_home_maintenance' => 'At Home Maintenance',
        'delivery_pickup' => 'Delivery/Pickup',
        'morning' => '8:00AM - 12:00PM',
        'noon' => '12:00PM - 4:00PM',
        'afternoon' => '4:00PM - 7:00PM',
    ];
    protected function rules()
    {
        return [
            'type' => 'required',
            'sx_ordernumber' => [
                'required',
                Rule::unique('schedules', 'sx_ordernumber')->ignore($this->getScheduledId()),
                Rule::exists('orders', 'order_number')
                ->where(function ($query) {
                    $query->where('order_number_suffix', $this->suffix);
                }),
            ],
            'suffix' => 'required',
            'line_items' => 'required|array',
            'schedule_date' => [
                'required',
                'after_or_equal:today',
                new ValidateScheduleDate($this->getActiveDays())
            ],
            'schedule_time' => [
                'required',
                'date_format:H:i',
                new ValidateScheduleTime($this->getActiveDays(), $this->type, $this->schedule_date)
            ],
        ];

    }

    public function getOrderInfo($suffix)
    {
        $google = app(DistanceInterface::class);

        $this->resetValidation(['sx_ordernumber', 'suffix']);
        $this->alertConfig['status'] = false;
        if(!$this->sx_ordernumber) {
            $this->addError('sx_ordernumber', 'order number is required');
            return;
        }

        $this->orderInfo = Order::where(['order_number' =>$this->sx_ordernumber, 'order_number_suffix' => $suffix])
            ->first();
        if(is_null($this->orderInfo)) {
            $this->addError('sx_ordernumber', 'order not found');
            $this->reset(['zipcodeInfo', 'scheduleDateDisable', 'schedule_date', 'schedule_time']);
            return;
        }

        $warehouse = Warehouse::where('short' , $this->orderInfo->whse)->first();
        $shipto = $this->orderInfo->shipping_info['line'].', ' .$this->orderInfo->shipping_info['line2'].', '
        .$this->orderInfo->shipping_info['city'].', '.$this->orderInfo->shipping_info['state'].', '.$this->orderInfo->shipping_info['zip'];
        $distance = $google->findDistance($warehouse->address, $shipto);

        if ($distance['status'] === 'OK') {
            $elements = $distance['rows'][0]['elements'][0] ?? null;
            $this->shipping['distance'] = $elements['distance']['text'] ?? null;
            $this->shipping['duration'] =   $elements['duration']['text'] ?? null;
        }

        if(empty($this->orderInfo->line_items)) {
            $this->addError('sx_ordernumber', 'Line items not found in this order');
            return;
        }

        if(is_null($this->orderInfo->shipping_info)) {
            $this->addError('sx_ordernumber', 'Shipping info missing');
            return;
        }

        $this->orderTotal = $this->getTotalInvoiceData($this->orderInfo->line_items, $this->orderInfo->sx_customer_number, $this->orderInfo->whse);

        $this->zipcodeInfo = Zipcode::where('zip_code', $this->orderInfo?->shipping_info['zip'])->first();
        $this->reset('alertConfig');
        $this->alertConfig['status'] = true;
        if(!$this->zipcodeInfo) {
            $this->alertConfig['message'] = 'Zipcode not configured';
            $this->alertConfig['icon'] = 'fa-times-circle';
            $this->alertConfig['class'] = 'danger';
            $this->alertConfig['show_url'] = true;
            $this->alertConfig['urlText'] = 'create new';
            $this->alertConfig['url'] = 'service-area.index';
            $this->alertConfig['params'] = 'tab=zip_code';
            return;
        }

        if(!$this->checkServiceAVailability($this->type)) {
            return;
        }

    }

    public function checkServiceAVailability($value)
    {
        if(!$this->orderInfo ||  !$this->zipcodeInfo) {
            return false;
        }
        $this->alertConfig['status'] = true;
        if(!in_array($value, $this->zipcodeInfo?->service)) {
            $this->alertConfig['message'] = 'This ZIP Code is not eligible for <strong>'.Str::of($this->type)->replace('_', ' ')->title().'</strong>';
            $this->alertConfig['icon'] = 'fa-times-circle';
            $this->alertConfig['class'] = 'danger';
            $this->reset(['scheduleDateDisable', 'schedule_date', 'schedule_time']);
            return false;
        }

        $this->alertConfig['message'] = 'This ZIP Code is eligible for <strong>'.Str::of($this->type)->replace('_', ' ')->title().'</strong>';
        $this->alertConfig['icon'] = 'fa-check-circle';
        $this->alertConfig['class'] = 'success';

        $this->scheduleDateDisable = false;
        return true;
    }

    public function store()
    {
        $validatedData = $this->validate();
        $validatedData['status'] = 'Scheduled';
        $validatedData['created_by'] = Auth::user()->id;
        $validatedData['order_number_suffix'] = $this->suffix;
        $schedule = Schedule::create($validatedData);
        return $schedule;
    }

    public function init(Schedule $schedule)
    {
        $this->schedule = $schedule;
        $this->fill($schedule->toArray());
        $this->schedule_time = Carbon::parse($schedule->schedule_time)->format('H:i');
        $this->suffix = $schedule->order_number_suffix;
    }

    public function update()
    {
        $validatedData = $this->validate();
        $validatedData['order_suffix_number'] = $this->suffix;
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
        $days = $this->zipcodeInfo?->getZone?->schedule_days;
        return  collect($days)
        ->filter(fn($day) => $day['enabled'])
        ->toArray();
    }
}