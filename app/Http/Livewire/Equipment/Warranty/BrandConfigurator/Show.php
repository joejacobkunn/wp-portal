<?php
namespace App\Http\Livewire\Equipment\Warranty\BrandConfigurator;

use App\Models\Equipment\Warranty\BrandConfigurator\BrandWarranty;
use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Equipment\Warranty\BrandConfigurator\Traits\FormRequest;
use App\Models\Product\Line;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Show extends Component
{
    use AuthorizesRequests, FormRequest;
    public BrandWarranty $warranty;
    public $editRecord =false;
    public $productLines;
    public $breadcrumbs = [
        [
            'title' => 'Warranty Registration',
        ],
        [
            'title' => 'Brand Configurator',
            'route_name' => 'equipment.warranty.index',
        ],
    ];
    protected $listeners = [
        'deleteRecord' => 'delete',
        'edit' => 'edit',
        'brandUpdated' => 'brandUpdated',
    ];
    public $actionButtons = [
        [
            'icon' => 'fa-edit',
            'color' => 'primary',
            'listener' => 'edit',
        ],
        [
            'icon' => 'fa-trash',
            'color' => 'danger',
            'confirm' => true,
            'confirm_header' => 'Confirm Delete',
            'listener' => 'deleteRecord',
        ],
    ];

    public function render()
    {
       return $this->renderView('livewire.equipment.warranty.brand-configurator.show');
    }

    public function mount()
    {
        array_push($this->breadcrumbs, ['title' => $this->warranty->brand->name]);
        $this->formInit();
    }

    public function edit()
    {
        $this->editRecord = true;
    }

    public function resetForm()
    {
        $this->resetValidation();
        $this->reset(['brandId', 'altName', 'prefix']);
        $this->formInit();
    }
}
