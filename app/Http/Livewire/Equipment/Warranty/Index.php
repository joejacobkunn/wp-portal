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
            'title' => 'Warranty Registration',
        ],
    ];
    protected $listeners = ['upBreadcrumb'=>'upBreadcrumb'];
    public $tabs = [
        'warranty-tabs' => [
            'active' => 'warrantyImport',
            'links' => [
                'warrantyImport' => 'Warranty Import',
                'warrantyReport' => 'Warranty Report',
                'brand' => 'Brand Configurator',
            ]]];

    protected $queryString = [
        'tabs.warranty-tabs.active' => ['except' => '', 'as' => 'tab'],
    ];
    public function mount()
    {
        $this->authorize('view', BrandWarranty::class);
    }

    public function upBreadcrumb($breadcrumbs)
    {
        $this->breadcrumbs = $breadcrumbs;
    }

    public function render()
    {
        return $this->renderView('livewire.equipment.warranty.index');
    }
}
