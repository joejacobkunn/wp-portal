<?php
namespace App\Http\Livewire\Equipment\Warranty\BrandConfigurator;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Equipment\Warranty\BrandConfigurator\Traits\FormRequest;
use App\Models\Equipment\Warranty\BrandConfigurator\BrandWarranty;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Index extends Component
{
    use AuthorizesRequests, FormRequest;
    public BrandWarranty $warranty;
    public $addRecord = false;

    public function mount()
    {
        $this->authorize('view', BrandWarranty::class);
        $this->formInit();
    }

    public function render()
    {
        return view('livewire.equipment.warranty.brand-configurator.index');
    }

    public function create()
    {
        $this->addRecord =true;
    }

    public function cancel()
    {
        $this->addRecord = false;
    }

}
