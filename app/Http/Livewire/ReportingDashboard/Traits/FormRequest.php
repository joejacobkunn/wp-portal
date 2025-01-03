<?php

namespace App\Http\Livewire\ReportingDashboard\Traits;

use App\Models\Report\Dashboard;


trait FormRequest
{
    protected $validationAttributes = [
        'dashboard.name' => 'Dashboard Name',
    ];


    protected function rules()
    {
        return [
            'dashboard.name' => 'required',
            'dashboard.reports' => 'required|array|min:1|max:2',
            'dashboard.is_active' => 'nullable',
        ];
    }

    /** Properties */

    /**
     * Initialize form attributes
     */
    public function formInit()
    {
        if (empty($this->dashboard)) {
            $this->dashboard = new Dashboard();
            $this->dashboard->name = null;
            $this->dashboard->account_id = null;
            $this->dashboard->reports = [];
            $this->dashboard->is_active = 1;
        }else{
        }

    }

    /**
     * Form submission action
     */
    public function submit()
    {
        $this->validate();

        $this->dashboard->account_id = auth()->user()->account->id;

        if (! empty($this->dashboard->id)) {
            $this->update();
        } else {
            $this->store();
        }
    }

    /**
     * Create new report
     */
    public function store()
    {
        $this->authorize('store', $this->dashboard);

        $this->dashboard->save();

        session()->flash('success', 'Report dashboard created!');

        return redirect()->route('reporting-dashboard.index');
    }

    /**
     * Update existing user
     */
    // @TODO Remove after confirmation on WP-8 Remove User info Edit
    public function update()
    {
        $this->authorize('update', $this->dashboard);

        $this->dashboard->save();

        session()->flash('success', 'Dashboard saved!');

        $this->editRecord = false;
    }

    public function closeModal()
    {
        $this->deactivate_modal = false;
    }




}
