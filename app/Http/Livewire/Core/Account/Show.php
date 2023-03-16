<?php

namespace App\Http\Livewire\Core\Account;

use App\Traits\HasTabs;
use App\Models\Core\User;
use App\Models\Core\Account;
use App\Models\Core\AccountApiKey;
use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Core\Account\Traits\FormRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Show extends Component
{
    use AuthorizesRequests, FormRequest, HasTabs;

    public Account $account;

    public $breadcrumbs = [
        [
            'title' => 'Accounts',
            'route_name' => 'core.account.index'
        ],
    ];

    public $actionButtons = [
        [
            'icon' => 'fa-edit',
            'color' => 'primary',
            'listener' => 'edit'
        ],
        [
            'icon' => 'fa-trash',
            'color' => 'danger',
            'confirm' => true,
            'confirm_header' => "Confirm Delete",
            'listener' => 'deleteRecord'
        ],
    ];

    protected $listeners = [
        'deleteRecord' => 'delete',
        'edit' => 'edit',
    ];

    public $editRecord = false;

    public $tabs = [
        'general' => 'General',
        'locations' => 'Locations',
        'modules' => 'Modules',
        'credentials' => 'API Credentials',
    ];
    public $tab;

    public $queryString = ['tab'];

    public function mount()
    {
        $this->formInit();

        array_push($this->breadcrumbs, ['title' => $this->account->name]);
    }

    public function render()
    {
        return $this->renderView('livewire.core.account.show');
    }

    public function edit()
    {
        $this->editRecord = true;
    }

    public function delete()
    {
        $this->authorize('delete', $this->account);

        $this->account->subdomain = $this->account->subdomain. "+deleted+" . time();
        $this->account->save();

        //delete pivot records
        $this->account->metadata()->delete();
        User::where('account_id', $this->account->id)->delete();

        $this->account->delete();
        session()->flash('success', 'Account Deleted !');

        return redirect()->route('core.account.index');
    }

    public function cancel()
    {
        //reset dirty attributes to original
        $this->account->setRawAttributes($this->account->getOriginal());
        $this->resetValidation();
        $this->editRecord = false;
    }
}
