<?php

namespace App\Http\Livewire\Core\Module;

use App\Models\Core\Account;
use Illuminate\Support\Facades\Http;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class HeroHubConfig extends Component
{
    use LivewireAlert;
    public Account $account;

    public $show = false;
    public $is_configured = false;

    public $client_id;
    public $client_key;
    public $organization_guid;

    protected $rules = [
        'client_id' => 'required',
        'client_key' => 'required',
        'organization_guid' => 'required'
    ];

    protected $listeners = [
        'closeModal'
    ];

    public function mount()
    {
        $this->is_configured = $this->account->herohubConfig()->exists();
    }

    public function render()
    {
        return view('livewire.core.module.hero-hub-config');
    }

    public function configure()
    {
        $this->validate();


        if($this->validateCredentials())
        {
            $this->account->herohubConfig()->create([
                'client_id' => $this->client_id,
                'client_key' => $this->client_key,
                'organization_guid' => $this->organization_guid
            ]);
    
            $this->show = false;
            $this->is_configured = true;
            $this->alert('success', 'HeroHub has been configured!');
        }else{
            $this->alert('error', 'Credentials are incorrect, try again!');
        }

    }

    public function validateCredentials()
    {
        $response = Http::post(config('herohub.token_endpoint'), [
            'clientId' => $this->client_id,
            'clientKey' => $this->client_key,
            'organizationGuid' => $this->organization_guid
        ]);
        return $response->successful();
    }

    public function closeModal()
    {
        $this->show = false;
    }
}
