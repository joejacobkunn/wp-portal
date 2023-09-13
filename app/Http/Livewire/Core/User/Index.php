<?php

namespace App\Http\Livewire\Core\User;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Core\User\Traits\FormRequest;
use App\Models\Core\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Index extends Component
{
    use AuthorizesRequests, FormRequest;

    public User $user;

    public $addRecord = false;

    public function breadcrumbs()
    {
        return [
            [
                'title' => 'Users',
                'href' => route('core.user.index'),
            ],
        ];
    }

    public function mount()
    {
        $this->authorize('viewAny', User::class);

        $this->formInit();
        $this->breadcrumbs = $this->breadcrumbs();
    }

    public function render()
    {
        return view('livewire.core.user.index')->extends('livewire-app');
    }

    public function create()
    {
        //user creation allowed only for master admins
        if (auth()->user()->isMasterAdmin()) {
            $this->addRecord = true;
        }
    }

    public function cancel()
    {
        $this->addRecord = false;
    }
}
