<?php

namespace App\Http\Livewire\Core\Location;

use App\Http\Livewire\Core\Location\Traits\FormRequest;
use App\Models\Core\Account;
use App\Models\Core\Location;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Index extends Component
{
    use AuthorizesRequests, FormRequest;

    public Account $account;

    public $location;

    public $addLocation = false;

    public $viewLocation = false;

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

    public function mount()
    {
        $this->authorize('viewAny', Location::class);

        $this->location = new Location();
        $this->formInit();
    }

    public function render()
    {
        return view('livewire.core.location.index');
    }

    public function create()
    {
        $this->authorize('store', Location::class);
        $this->addLocation = true;
    }

    public function cancel()
    {
        $this->addLocation = false;
        $this->resetExcept('account', 'location');
        $this->formInit();
        $this->resetValidation();
    }
}
