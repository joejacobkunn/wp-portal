<?php
namespace App\Http\Livewire\Equipment\Warranty\WarrantyImport;

use App\Http\Livewire\Component\Component;
use App\Models\Equipment\Warranty\BrandConfigurator\BrandWarranty;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;

class Report extends Component
{
    use AuthorizesRequests;

    public $last_refresh_timestamp;
    public $non_registered_count;

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

}
