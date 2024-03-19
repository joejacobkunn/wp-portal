<?php

namespace App\Http\Livewire\Order\EmailTemplate\Traits;

use App\Models\Order\EmailTemplate;

trait FormRequest
{
    public $name;
    public $emailContent;
    public $smsContent;
    public $is_active = 1;

    protected $validationAttributes = [
        'name' => 'Name',
        'emailContent' => 'Email Content',
        'smsContent' => 'SMS Content',
    ];

    protected function rules()
    {
        return [
            'name' => 'required|min:3',
            'emailContent' => 'required',
            'smsContent' => 'required|max:160',
            'is_active' => 'nullable',
        ];
    }

    /**
     * Initialize form attributes
     */
    public function formInit()
    {
        if (!empty($this->template->id)) {
            $this->name = $this->template->name;
            $this->emailContent = $this->template->email_content;
            $this->smsContent = $this->template->sms_content;
            $this->is_active = 1;
        } 
    }

    /**
     * Form submission action
     */
    public function submit()
    {
        $this->validate();

        if (! empty($this->template->id)) {
            $this->update();
        } else {
            $this->store();
        }
    }

    /**
     * Create new account
     */
    public function store()
    {
        //$this->authorize('store', EmailTemplate::class);

        $data = [
            'name' => $this->name,
            'email_content' => $this->emailContent,
            'sms_content' => $this->smsContent,
            'is_active' => $this->is_active,
        ];

        $this->template = new EmailTemplate;
        $this->template->account_id = account()->id;
        $this->template->fill($data);
        $this->template->save();

        session()->flash('success', 'Template created!');

        return $this->redirect(route('order.email-template.show', [
                'template' => $this->template->id,
            ]), navigate: true);
    }

    /**
     * Update existing account
     */
    public function update()
    {
        //$this->authorize('update', $this->template);

        $this->template->name = $this->name;
        $this->template->email_content = $this->emailContent;
        $this->template->sms_content = $this->smsContent;
        $this->template->is_active = $this->is_active;
        $this->template->save();

        $this->editRecord = false;
        session()->flash('success', 'Template updated!');
    }
}
