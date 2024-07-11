<?php
namespace App\Http\Livewire\Equipment\Warranty\WarrantyImport;

use App\Http\Livewire\Equipment\Warranty\WarrantyImport\Traits\ImportExportRequest;
use App\Imports\WarrantyImport;
use App\Models\Equipment\Warranty\BrandConfigurator\BrandWarranty;
use App\Models\Equipment\Warranty\WarrantyImport\WarrantyImports;
use App\Models\Product\Brand;
use App\Models\SX\SerializedProduct;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads, LivewireAlert, ImportExportRequest;

    public $csvFile;
    public $addRecord =false;
    protected $rules = [
        'csvFile' => 'required|file|mimes:csv,xlsx'
    ];
    protected $validationAttributes = [
        'csvFile' => 'File'
    ];
    protected $listeners = [
        'cancel'=> 'cancel'
        ];

    public function mount()
    {
        $this->updateBreadcrumb();
        $this->init();
    }

    public function render()
    {
        return view('livewire.equipment.warranty.warranty-import.index');
    }

    public function updatedCsvFile()
    {
        $this->validateOnly('csvFile');
        $this->showalert['status'] = false;
        $this->dataImport();
    }

    public function downloadInvalidEntries()
    {
        return $this->downloadEntires();
    }

    public function downloadDemo()
    {
        $filePath = public_path(config('warranty.demo_file_path'));

        if (!file_exists($filePath)) {
            $this->alert('error', 'File not found.');
            return;
        }
        return response()->download($filePath);
    }

    public function cancel()
    {
        $this->csvFile = null;
        $this->validatedRows = [];
        $this->importErrorRows = [];
        $this->showalert['status'] = false;
        $this->importIteration++;
    }

    public function importData()
    {
        if (empty($this->validatedRows)) {
            $this->showalert['status'] = true;
            $this->showalert['class'] = 'error';
            $this->showalert['message'] = 'There is no valid data to Import!';
            return;
        }
        sleep(3);
        $this->saveData();
        $this->page = 'success';
        return $this->validatedRows;
    }

    public  function updateBreadcrumb() {
        $newBreadcrumbs = [
                [
                    'title' => 'Warranty Registration',
                ],
                [
                    'title' => 'Warranty Import',
                ]

        ];
        $this->dispatch('upBreadcrumb', $newBreadcrumbs);
    }

    public function create()
    {
        $this->addRecord=true;
        $this->page='form';
    }
}
