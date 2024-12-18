<?php
 namespace App\Http\Livewire\Scheduler\Schedule\Forms;

use App\Models\Order\Order;
use App\Models\Scheduler\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
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
    public $created_by;

    protected $validationAttributes = [
        'type' => 'Schedule',
        'sx_ordernumber' => 'Order Number',
        'schedule_date' => 'Schedule Date',
        'schedule_time' => 'Schedule Time',
        'line_items' => 'Line Items',
        'suffix' => 'Order Suffix',
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
            'schedule_date' => 'required|after_or_equal:today',
            'schedule_time' => 'required|date_format:H:i',
        ];

    }

    public function getOrderInfo($suffix)
    {
        $this->resetValidation(['sx_ordernumber', 'order_number_suffix']);
        if(!$this->sx_ordernumber) {
            $this->addError('order_number_suffix', 'order number is required');
            return;
        }
        $this->orderInfo = Order::where(['order_number' =>$this->sx_ordernumber, 'order_number_suffix' => $suffix])
            ->whereIn('stage_code', [1,2])->first();
        if(!$this->orderInfo) {
            $this->addError('sx_ordernumber', 'order not found');
        }

        if(empty($this->orderInfo->line_items)) {
            $this->addError('sx_ordernumber', 'Line items not found in this order');
        }
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
}
