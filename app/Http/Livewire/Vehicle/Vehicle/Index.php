<?php

namespace App\Http\Livewire\Vehicle\Vehicle;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Core\Account\Traits\FormRequest;
use App\Models\Core\Account;
use App\Models\Vehicle\Vehicle;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Services\Environment\Domain;

class Index extends Component
{
    use AuthorizesRequests;
    use FormRequest;

    public $vehicle;

    public $account;

    public $addRecord = false;

    public $breadcrumbs = [
        [
            'title' => 'Vehicles',
        ],
    ];

    public function render()
    {
        //$this->authorize('viewAny', Account::class);

        return $this->renderView('livewire.vehicle.vehicle.index');
    }

    public function mount()
    {
        $this->account = Account::find(Domain::getClientId());
        $this->vehicle = new Vehicle();
        $this->formInit();
    }

    public function create()
    {
        //$this->authorize('store', Account::class);
        $this->addRecord = true;
    }

    /**
     * Form cancel action
     */
    public function cancel()
    {
        $this->reset();
        $this->resetValidation();
        $this->addRecord = false;
    }
}
