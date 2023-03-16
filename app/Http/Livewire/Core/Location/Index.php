<?php

namespace App\Http\Livewire\Core\Location;

use App\Models\Core\Account;
use App\Models\Core\Location;
use Livewire\Component;
use App\Http\Livewire\Core\Location\Traits\FormRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class Index extends Component
{
    use AuthorizesRequests, FormRequest;

    public Account $account;

    public $location;

    public $addLocation = false;

    public $viewLocation = false;

    public function render()
    {
        return view('livewire.core.location.index');
    }

    public function mount()
    {
        $this->location = new Location();
        $this->formInit();
    }


    public function create()
    {
        $this->authorize('store', Location::class);
        $this->addLocation = true;
    }

    public function cancel()
    {
        $this->addLocation = false;
        $this->reset();
        $this->resetValidation();
    }
}
