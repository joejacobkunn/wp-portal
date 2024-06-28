<?php

namespace App\Http\Livewire\Component\Sms;

use Livewire\Component;

class SmsComponent extends Component
{
    public $phone;
    public $email;
    public $isLoading=true;
    public $alert=false;
    public function render()
    {
         return view('livewire.component..sms.sms-component');
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
    }
}
