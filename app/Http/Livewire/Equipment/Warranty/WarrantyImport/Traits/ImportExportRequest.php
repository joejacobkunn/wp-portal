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
           $this->addError('csvFile', $e->getMessage());
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
        return Excel::download(new WarrantyExport($this->importErrorRows), 'invalid-rows'.now().'.csv');
    }

    public function saveData()
    {
        $extension = $this->csvFile->getClientOriginalExtension();
        $uploadedFileName = uniqid() . '.' . $extension;
        $uploadDirectory =  config('warranty.upload_location');
        $validPath = config('warranty.valid_file_location') . uniqid() . '.csv';

        try {
            $filePath = $this->csvFile->storeAs($uploadDirectory, $uploadedFileName, 'public');
            $export = new WarrantyExport($this->validatedRows);
            Excel::store($export, $validPath, 'public');
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
            'failed_records' =>  null,
            'valid_records' =>  $validPath,
            'processed_count' =>  0,
            'total_records' => count($this->validatedRows) + count($this->importErrorRows),
            'status'        =>'queued'
        ]);

        //dispatch job to queue
        ProcessWarrantyRecords::dispatch($this->validatedRows, $warrantyImport, $this->importErrorRows);

        return redirect()->route('equipment.warranty.index');
    }
}
