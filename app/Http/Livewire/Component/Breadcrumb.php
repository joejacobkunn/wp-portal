<?php

namespace App\Http\Livewire\Component;

use Livewire\Component;

class Breadcrumb extends Component
{
    public $showRootBreadcrumb = true;

    public $rootBreadcrumb = [
        'icon' => 'fa-home',
        'href' => '#',
    ];

    public $breadcrumbs = [];

    public $routeParams;

    public function render()
    {
        return view('livewire.component.breadcrumb');
    }

    public function mount()
    {

    }
}
