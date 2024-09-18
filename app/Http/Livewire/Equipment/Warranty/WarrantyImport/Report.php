<?php
namespace App\Http\Livewire\Equipment\Warranty\WarrantyImport;

use App\Http\Livewire\Component\Component;
use App\Models\Equipment\Warranty\BrandConfigurator\BrandWarranty;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Report extends Component
{
    use AuthorizesRequests;

    public function mount()
    {
        $this->updateBreadcrumb();
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
