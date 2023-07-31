<?php

namespace App\Http\Livewire\Core\Module;

use App\Models\Core\Account;
use App\Models\Core\Module;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Index extends Component
{
    use AuthorizesRequests;

    public Account $account;

    public $modules = [];

    protected $listeners = [
        'refreshPage' => '$refresh',
        'closeModal',
    ];

    public function mount()
    {
        $this->authorize('viewAny', Module::class);

        $this->modules = Module::get();
    }

    public function render()
    {
        return view('livewire.core.module.index');
    }

    public function toggleModule($module_id, $value)
    {
        if (! auth()->user()->isMasterAdmin()) {
            abort(403);
        }

        if ($value) {
            $this->account->modules()->attach([$module_id]);
        } else {
            $this->account->modules()->detach([$module_id]);
        }

        $this->emit('refreshPage');
    }
}
