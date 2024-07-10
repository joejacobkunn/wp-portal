<?php
namespace App\Http\Livewire\Equipment\Warranty\WarrantyImport\Traits;

use App\Exports\WarrantyExport;
use App\Imports\WarrantyImport;
use App\Models\Equipment\Warranty\BrandConfigurator\BrandWarranty;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Maatwebsite\Excel\Facades\Excel;

trait ImportExportRequest
{
    public $brands;
    public $importIteration=0;
    public $importAction = false;
    public $validatedRows = [];
    public $importErrorRows = [];
    public $failures = [];

    public function init()
    {
        $brandWarranties = BrandWarranty::with('brand')
        ->where('account_id', Auth::user()->account_id)->get();

        $brands = $brandWarranties->pluck('brand.name')->unique()->values()->all();

        $Altnames = $brandWarranties
            ->flatMap(function ($item) {
                return explode(',', $item->alt_name);
            })
            ->unique()
            ->values()
            ->all();
        $this->brands = array_map('strtolower', array_unique(array_merge($brands, $Altnames)));
    }

    public function dataImport()
    {
        try {

            $import = new WarrantyImport($this->brands);
            Excel::import($import, $this->csvFile);
        } catch (\Exception $e) {
            $this->dispatch('cancel');
            $this->alert('error', $e->getMessage());
        }

        $this->validatedRows = $import->getData();
        $failures = $import->failures();

        if (count($failures) > 0) {
            foreach ($failures as $failure) {
                $this->importErrorRows[] = $failure->values();
            }
        }
    }

    public function downloadEntires()
    {
        return Excel::download(new WarrantyExport($this->importErrorRows), 'invalid-rows'.now().'.xlsx');
    }
}
