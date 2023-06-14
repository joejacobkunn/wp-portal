<?php

namespace App\Http\Livewire\Dashboard;

use App\Http\Livewire\Component\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Index extends Component
{
    use AuthorizesRequests;

    public $breadcrumbs = [];

    public function breadcrumbs()
    {
        return [
            [
                'title' => 'Dashboard',
            ],
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.index')->extends('livewire-app');
    }

    public function mount()
    {
        $this->breadcrumbs = $this->breadcrumbs();
    }
}
