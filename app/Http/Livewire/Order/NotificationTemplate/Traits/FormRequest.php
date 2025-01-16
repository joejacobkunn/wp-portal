<?php

namespace App\Http\Livewire\Order\NotificationTemplate\Traits;

use App\Models\Order\NotificationTemplate;

trait FormRequest
{
    public $name;
    public $emailContent;
    public $emailSubject;
    public $smsContent;
    public $templateType;
    public $is_active = 1;

    public $templateTypes = [
        'Follow Up' => 'Customer Follow Up',
        'Shipment Follow Up' => 'Shipment Follow Up',
        'Cancelled' => 'Cancelled',
        'Receiving Follow Up' => 'Receiving Follow Up'

    ];

    protected $validationAttributes = [
        'name' => 'Name',
        'emailContent' => 'Email Content',
        'smsContent' => 'SMS Content',
        'templateType' => 'Template Type'
    ];

    protected function rules()
    {
        return [
            'name' => 'required|min:3',
            'emailSubject' => 'required',
            'emailContent' => 'required',
            'smsContent' => 'nullable',
            'templateType' => 'required',
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
            $this->emailSubject = $this->template->email_subject;
            $this->emailContent = $this->template->email_content;
            $this->smsContent = $this->template->sms_content;
            $this->templateType = $this->template->type;
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
        //$this->authorize('store', NotificationTemplate::class);

        $data = [
            'name' => $this->name,
            'email_subject' => $this->emailSubject,
            'email_content' => $this->emailContent,
            'type' => $this->templateType,
            'sms_content' => $this->smsContent,
            'is_active' => $this->is_active,
            'created_by' => auth()->user()->id
        ];

        $this->template = new NotificationTemplate();
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
        $this->template->email_subject = $this->emailSubject;
        $this->template->email_content = $this->emailContent;
        $this->template->sms_content = $this->smsContent;
        $this->template->is_active = $this->is_active;
        $this->template->save();

        $this->editRecord = false;
        session()->flash('success', 'Template updated!');
    }
}
