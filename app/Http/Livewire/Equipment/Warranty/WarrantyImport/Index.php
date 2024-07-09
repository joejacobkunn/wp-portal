<?php
namespace App\Http\Livewire\Equipment\Warranty\WarrantyImport;

use App\Http\Livewire\Equipment\Warranty\WarrantyImport\Traits\ImportExportRequest;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads, LivewireAlert, ImportExportRequest;

    public $csvFile;
    protected $rules = [
        'csvFile' => 'required|file|mimes:csv,xlsx'
    ];
    protected $validationAttributes = [
        'csvFile' => 'File'
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
        $this->importIteration++;
    }

    public function importData()
    {
        $this->importAction = true;
        $this->validateOnly('csvFile');

        if (config('sx.mock')) {
            sleep(5);
        } else {
            foreach($this->validatedRows as $row)
            {

            }
        }
        $this->alert('success','import completed successfully!');
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
}
