<?php

namespace App\Http\Livewire\Equipment\Warranty\BrandConfigurator;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Equipment\Warranty\BrandConfigurator\Traits\FormRequest;
use App\Models\Equipment\UnavailableReport;
use App\Models\Equipment\Warranty\BrandWarranty;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Index extends Component
{
    use AuthorizesRequests, FormRequest;
    public BrandWarranty $warranty;
    public $addRecord = false;
    public $showBtn = true;
    public $linesDisabled =true;
    public $breadcrumbs = [
        [
            'title' => 'Warranty Registration',
        ],
    ];

    public function mount()
    {
        $this->formInit();
    }
    protected $listeners = [
        'brandUpdated' => 'brandUpdated',
    ];

    public function render()
    {
        $this->authorize('view', BrandWarranty::class);
        return $this->renderView('livewire.equipment.warranty.index');
    }

    public function getConfiguredProperty()
    {
        if(!auth()->user()->can('equipment.warranty.view') ) return false;
        return true;
    }

    public function create(){
        $this->addRecord =true;
        $this->showBtn = false;
    }
    public function cancel(){
        $this->addRecord = false;
        $this->showBtn = true;
    }
}
