<?php

namespace App\Http\Livewire\Marketing\SMSMarketing;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Marketing\SMSMarketing\Traits\FormRequest;
use App\Models\Marketing\SMSMarketing;
use App\Services\Kenect;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads, FormRequest;
    public $addRecord = False;
    public $importIteration=0;
    public $smsImportTableId = 'smstab-id';
    public $importFile;
    public $name;
    public $page ='Send bulk sms campaigns via Kenect';
    public $showalert=[
        'status' => false,
        'class' => null,
        'message' => null
    ];

    public $breadcrumbs = [
        [
            'title' => 'Marketing',
        ],
        [
            'title' => 'SMS Marketing',
        ]
    ];
    protected $rules = [
        'name' => 'required',
        'importFile' => 'required|file|mimes:csv,txt|ends_with_csv'
    ];
    protected $validationAttributes = [
        'name' => 'Name',
        'importFile' => 'File'
    ];
    protected $listeners = [
        'cancel'=> 'cancel'
    ];

    public function updatedImportFile()
    {
        $this->validateOnly('importFile');
        $this->showalert['status'] = false;
        $this->dataImport();
    }

    public function downloadInvalidEntries()
    {
        return $this->downloadEntires();
    }

    public function importData()
    {
        $this->validate();
        if (empty($this->validatedRows)) {
            $this->showalert['status'] = true;
            $this->showalert['class'] = 'danger';
            $this->showalert['message'] = 'There is no valid data to Import!';
            return;
        }
        $this->saveData();
    }

    public function render()
    {
        return $this->renderView('livewire.marketing.sms-marketing.index');
    }

    public function create()
    {
        $this->addRecord = true;
        $this->page = 'import new file';
    }

    public function cancel()
    {
        $this->importFile = null;
        $this->name = null;
        $this->validatedRows = [];
        $this->importErrorRows = [];
        $this->showalert['status'] = false;
        $this->importIteration++;
        $this->resetValidation();
        $this->addRecord = false;
    }

    public function refreshStatus()
    {
        $processingCount = SMSMarketing::where('status', '!=', 'complete')->count();

        if ($processingCount > 0) {
            $this->smsImportTableId = uniqid();
            return;
        }
        $this->reset('smsImportTableId');
    }

    public function downloadDemo()
    {
        $filePath = public_path(config('marketing.sms.demo_file_path'));

        if (!file_exists($filePath)) {
            $this->showalert['status'] = true;
            $this->showalert['class'] = 'danger';
            $this->showalert['message'] = 'File not found!';
            return;
        }
        return response()->download($filePath);
    }
}
