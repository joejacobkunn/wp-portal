<?php

namespace App\Http\Livewire\Core\User;

use App\Http\Livewire\Component\Component;
use App\Models\Core\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AuthenticationLog extends Component
{
    use AuthorizesRequests;

    public function render()
    {
        return view('livewire.core.user.authentication-log')->extends('livewire-app');
    }

    public function breadcrumbs()
    {
        return [
            [
                'title' => 'Users',
                'href' => route('core.user.index'),
            ],
            [
                'title' => 'Auth Logs'
            ]
        ];
    }

    public function mount()
    {
        $this->authorize('viewAny', User::class);

        $this->breadcrumbs = $this->breadcrumbs();
    }


}
