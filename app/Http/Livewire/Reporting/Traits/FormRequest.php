<?php

namespace App\Http\Livewire\Reporting\Traits;
use App\Models\Report\Report;
use PHPSQLParser\PHPSQLParser;


trait FormRequest
{
    protected $validationAttributes = [
        'report.name' => 'Report Name',
    ];


    protected function rules()
    {
        return [
            'report.name' => 'required',
            'report.description' => 'required',
            'report.query' => 'required',
            'report.group_by' => 'nullable',
        ];
    }

    /** Properties */

    /**
     * Initialize form attributes
     */
    public function formInit()
    {
        if (empty($this->report)) {
            $this->report = new Report();
            $this->report->name = null;
            $this->report->description = null;
            $this->report->query = null;
            $this->report->group_by = '';
        }else{
            $this->group_by_options = collect($this->get_columns($this->report->query));
        }

    }

    public function updatedReportQuery($value)
    {
        $columns = [];

        if(!empty($value))
        {
            $columns = $this->get_columns($value);
        }


        $this->group_by_options = collect($columns);

    }

    /**
     * Form submission action
     */
    public function submit()
    {
        $this->validate();

        if (! empty($this->report->id)) {
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

        $this->report->save();

        session()->flash('success', 'Report created!');

        return redirect()->route('reporting.index');
    }

    /**
     * Update existing user
     */
    // @TODO Remove after confirmation on WP-8 Remove User info Edit
    public function update()
    {
        $this->report->save();

        session()->flash('success', 'Report saved!');

        $this->editRecord = false;
    }

    public function closeModal()
    {
        $this->deactivate_modal = false;
    }

    private function get_columns($query)
    {
        $columns = [];
        $parser = new PHPSQLParser($this->cleanWithNoLocks($query), true);
        $parsed_columns = $parser->parsed;
        if($parsed_columns)
        {
            foreach($parsed_columns['SELECT'] as $column){
                if(is_array($column['alias'])) $column_name = $this->clean($column['alias']['name']);
                else $column_name = $this->clean(end($column['no_quotes']['parts']));
                $columns[] =  ['label' => $column_name, 'name' => $column_name];
            }

        }

        return $columns;

    }

    private function clean($string)
    {
        $string = str_replace('"','',$string);
        $string = str_replace("'",'',$string);
        return $string;
    }

    private function cleanWithNoLocks($string)
    {
        $string = str_replace('WITH (NOLOCK)','',$string);
        $string = str_replace("WITH(NOLOCK)",'',$string);
        $string = str_replace('with (nolock)','',$string);
        $string = str_replace("with(nolock)",'',$string);

        return $string;
    }




}
