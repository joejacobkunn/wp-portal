<?php
namespace App\Http\Livewire\Equipment\Warranty\WarrantyImport;

use App\Http\Livewire\Component\Component;
use App\Jobs\ExportWarrantyReport;
use App\Models\Equipment\Warranty\BrandConfigurator\BrandWarranty;
use App\Models\Equipment\Warranty\Report as WarrantyReport;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Report extends Component
{
    use AuthorizesRequests, LivewireAlert;

    public $last_refresh_timestamp;
    public $non_registered_count;

    protected $listeners = [
        'exportWarrantyRecords' => 'exportWarrantyRecords'
    ];
    public function mount()
    {
        $this->updateBreadcrumb();
        $this->last_refresh_timestamp = Cache::get('warranty_registration_report_sync_timestamp');
        $this->non_registered_count = Cache::get('warranty_registration_non_registered_count');
    }

    public function render()
    {
        return view('livewire.equipment.warranty.warranty-import.report');
    }

    public  function updateBreadcrumb() {
        $newBreadcrumbs = [
                [
                    'title' => 'Warranty Report',
                ]
        ];
        $this->dispatch('upBreadcrumb', $newBreadcrumbs);
    }

    public function register($serial_no, $cust_no)
    {
        DB::connection('sx')->statement("UPDATE pub.icses SET user9 = '".date("m/d/y")."' , user4 = '".auth()->user()->sx_operator_id."' where cono = 10 and currstatus = 's' and LTRIM(RTRIM(UPPER(icses.serialno))) = '".$serial_no."' and custno = '".$cust_no."'");
        $record = WarrantyReport::where('serial',$serial_no)->where('cust_no',$cust_no)->first();
        $record->update(['registration_date' => date("m/d/y"), 'registered_by' => auth()->user()->sx_operator_id]);
        return $record->registration_date;
    }

    public function unregister($serial_no, $cust_no)
    {
        DB::connection('sx')->statement("UPDATE pub.icses SET user9 = NULL , user4 = '' where cono = 10 and currstatus = 's' and LTRIM(RTRIM(UPPER(icses.serialno))) = '".$serial_no."' and custno = '".$cust_no."'");
        $record = WarrantyReport::where('serial',$serial_no)->where('cust_no',$cust_no)->first();
        $record->update(['registration_date' => '', 'registered_by' => '']);
    }

    public function ignore($serial_no, $cust_no)
    {
        DB::connection('sx')->statement("UPDATE pub.icses SET user9 = '2001-01-01' , user4 = '".auth()->user()->sx_operator_id."' where cono = 10 and currstatus = 's' and LTRIM(RTRIM(UPPER(icses.serialno))) = '".$serial_no."' and custno = '".$cust_no."'");
        $record = WarrantyReport::where('serial',$serial_no)->where('cust_no',$cust_no)->first();
        $record->update(['registration_date' => '2001-01-01', 'registered_by' => auth()->user()->sx_operator_id]);
    }

    public function exportWarrantyRecords()
    {
        ExportWarrantyReport::dispatch(Auth::user());
        $this->alert('success', 'records will be mailed after export is completed!');
    }
}
