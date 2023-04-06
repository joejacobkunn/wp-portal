<?php

namespace App\Http\Livewire\Core\Module;

use App\Models\Core\Account;
use App\Models\Core\Module;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class Index extends Component
{
    use AuthorizesRequests;

    public Account $account;

    public $modules = [];

    protected $listeners = [
        'refreshPage' => '$refresh',
        'closeModal'
    ];

    public function render()
    {
        $this->authorize('view', Module::class);

        return view('livewire.core.module.index');
    }

    public function mount()
    {
        $this->modules = Module::get();
    }

    public function toggleModule($module_id,$value)
    {
        if(!auth()->user()->isMasterAdmin()) abort(403);

        if($value)
            $this->account->modules()->attach([$module_id]);
        else
            $this->account->modules()->detach([$module_id]);

        $this->emit('refreshPage');
    }
}
