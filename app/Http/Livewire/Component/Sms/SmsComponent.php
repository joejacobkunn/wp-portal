<?php

namespace App\Http\Livewire\Component\Sms;

use App\Models\SMS\KenectCache;
use App\Services\KenectSms;
use Livewire\Component;

class SmsComponent extends Component
{
    public $phone;
    public $email;
    public $isLoading=true;
    public $apiUser;
    public $alert=false;
    public function render()
    {
         return view('livewire.component.sms.sms-component');
    }
    public function mount()
    {
        //dd($this->email);
        //$this->isLoading = false;
        $this->init();
    }

    public function init()
    {
        if(!$this->phone || !$this->email) {
            $this->alert=true;
            $this->isLoading=false;
        }
        $this->checkUserIncache();
    }

    public function checkUserIncache()
    {
        $this->apiUser = KenectCache::where('phone', $this->phone)
            ->where('email', $this->email)->first();
    }
}
