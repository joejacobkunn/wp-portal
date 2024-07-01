<?php

namespace App\Http\Livewire\Equipment\Warranty\WarrantyImport;

use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;
    public $csvFile;
    public $importIteration=0;
    public $rows = [];
    protected $rules = [
        'csvFile' => 'required|file|mimes:csv,excel'
    ];
    protected $validationAttributes = [
        'csvFile' => 'File'
    ];

    public function render()
    {
        return view('livewire.equipment.warranty.warranty-import.index');
    }

    public function updatedCsvFile()
    {
        $this->validateOnly('csvFile');

        if (($handle = fopen($this->csvFile->getRealPath(), 'r')) !== false) {
            $this->rows = [];
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $this->rows[] = $data;
            }
            fclose($handle);
        }
    }

    public function cancel()
    {
        $this->csvFile = null;
        $this->rows = [];
        $this->importIteration++;
    }
}
