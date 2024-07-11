<?php
namespace App\Http\Livewire\Equipment\Warranty\WarrantyImport\Traits;

use App\Exports\WarrantyExport;
use App\Imports\WarrantyImport;
use App\Jobs\ProcessWarrantyRecords;
use App\Models\Equipment\Warranty\BrandConfigurator\BrandWarranty;
use App\Models\Equipment\Warranty\WarrantyImport\WarrantyImports;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
    public $showalert =[
        'status' =>false,
        'class' =>null,
        'message' =>null,
    ];
    public $page = 'viewData';
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

    public function saveData()
    {
        $extension = $this->csvFile->getClientOriginalExtension();
        $uploadedFileName = uniqid() . '.' . $extension;
        $uploadDirectory = 'public/warranty-imports/uploaded-files';
        $uploadDirectory = 'warranty-imports/uploaded-files';
        $validPath = 'warranty-imports/valid-records/' . uniqid() . '.xlsx';
        $failedPath = !empty($this->importErrorRows) ? 'warranty-imports/failed-records/' . uniqid() . '.xlsx' : null;

        try {
            $filePath = $this->csvFile->storeAs($uploadDirectory, $uploadedFileName, 'public');
            $export = new WarrantyExport($this->validatedRows);
            Excel::store($export, $validPath, 'public');

            if ($failedPath) {
                $exportFaild = new WarrantyExport($this->importErrorRows);
                Excel::store($exportFaild, $failedPath, 'public');
            }
        } catch (\Exception $e) {
            $this->showalert['staus'] = true;
            $this->showalert['class'] = 'error';
            $this->showalert['message'] = 'Error saving data';
            return;
        }

        $warrantyImport =  WarrantyImports::create([
            'name' => $this->name,
            'file_path' => $filePath,
            'uploaded_by' => Auth::user()->id,
            'failed_records' =>  $failedPath,
            'valid_records' =>  $validPath,
            'processed_count' =>  0,
            'total_records' => count($this->validatedRows),
            'status'        =>'queued'
        ]);

        //dispatch job to queue
        ProcessWarrantyRecords::dispatch($this->validatedRows, $warrantyImport);

        return redirect()->route('equipment.warranty.index');
    }
}
