<?php

namespace App\Http\Livewire\Order;


use App\Http\Livewire\Component\Component;
use App\Imports\OrdersImport;

use App\Jobs\ProcessOrders;
use App\Models\Order\Order;
use App\Models\SX\Company;
use App\Traits\HasTabs;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use HasTabs, AuthorizesRequests, WithFileUploads;

    public $account;

    public $orders = [];

    public $statusCount = [];

    public $pendingReviewCount = 0;

    public $dnr_count = 0;

    public $pending_review_count = 0;

    public $follow_up_count = 0;

    public $ignored_count = 0;

    public $exportModal = false;

    public $order_data_sync_timestamp;

    public $metricFilter = [];

    public $orderType;
    public $showExportNotification = false;
    public $breadcrumbs = [
        [
            'title' => 'Orders',
        ],
    ];
    protected $rules = [
        'orderType' => 'required',
    ];

    protected $listeners = [
        'closeModel'=> 'closeModel'
    ];

    public function mount()
    {
        $this->authorize('viewAny', Order::class);

        $this->order_data_sync_timestamp = Cache::get('order_data_sync_timestamp', '');

        $this->account = account();

    }

    public function render()
    {
        return $this->renderView('livewire.order.index');
    }

    public function setFilter($filter, $value)
    {
        $this->metricFilter = [
            'filter' => $filter,
            'value' => $value
        ];
    }

    public function getStatusCounts()
    {
        $this->dnr_count = Order::where('cono', auth()->user()->account->sx_company_number)->where('is_dnr', 1)->where('status', 'Pending Review')->count();
        $this->pending_review_count = Order::where('cono', auth()->user()->account->sx_company_number)->where('status', 'Pending Review')->count();;
        $this->follow_up_count = Order::where('cono', auth()->user()->account->sx_company_number)->whereIn('status', ['Follow Up','Shipment Follow Up'])->count();
        $this->ignored_count = Order::where('cono', auth()->user()->account->sx_company_number)->where('status', 'Ignored')->count();
    }

    public function updateOpenOrders()
    {
        Artisan::call('sx:update-open-orders');
        $this->order_data_sync_timestamp = Cache::get('order_data_sync_timestamp');
        $this->dispatch('refreshDatatable');
    }

    public function export()
    {
        $this->exportModal = true;
    }

    public function submit()
    {
        $this->validate();
        ProcessOrders::dispatch($this->orderType);
        $this->showExportNotification = true;
        $this->closeModel();
    }

    public function closeModel()
    {
        $this->exportModal =false;
        $this->reset(['orderType']);
        $this->resetValidation();
    }
}
