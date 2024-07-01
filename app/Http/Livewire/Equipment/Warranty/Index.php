<?php
namespace App\Http\Livewire\Equipment\Warranty;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Equipment\Warranty\BrandConfigurator\Traits\FormRequest;
use App\Models\Equipment\UnavailableReport;
use App\Models\Equipment\Warranty\BrandConfigurator\BrandWarranty;
use App\Traits\HasTabs;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Index extends Component
{
    use AuthorizesRequests, HasTabs;
    public $breadcrumbs = [
        [
            'title' => 'Brand Configurator',
        ],
    ];
    public $tabs = [
        'warranty-tabs' => [
            'active' => 'brand',
            'links' => [
                'brand' => 'Brand Configurator',
                'warrantyImport' => 'Warranty Import',
            ],
        ]
    ];
    public function mount(){
        $this->authorize('view', BrandWarranty::class);
    }
    public function render()
    {
        return $this->renderView('livewire.equipment.warranty.index');
    }
}
