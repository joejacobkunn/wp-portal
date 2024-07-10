<?php
namespace App\Http\Livewire\Equipment\Warranty\WarrantyImport;

use App\Http\Livewire\Equipment\Warranty\WarrantyImport\Traits\ImportExportRequest;
use App\Models\Equipment\Warranty\BrandConfigurator\BrandWarranty;
use App\Models\Product\Brand;
use App\Models\SX\SerializedProduct;
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
        //$this->validateOnly('csvFile');

        if (config('sx.mock')) {
            sleep(2);
            foreach($this->validatedRows as $row)
            {
            }

        } else {
            foreach($this->validatedRows as $row)
            {
                $brand = Brand::whereRaw('LOWER(name) = ?', [strtolower($row['brand'])])->first();
                
                $brand_config = BrandWarranty::where('brand_id', $brand->id)->first();
                
                SerializedProduct::where('cono', 10)
                ->where('whseto', '')->where('currstatus', 's')
                ->whereIn('prod',[$brand_config->prefix.$row['model'],strtolower($brand_config->prefix.$row['model']), strtoupper($brand_config->prefix.$row['model'])])
                ->whereIn('serialno', [$row['serial'],strtolower($row['serial']), strtoupper($row['serial'])])
                ->update(['user9' => date("m/d/y", strtotime($row['reg_date'])), 'user4', auth()->user()->sx_operator_id]);

            }
        }
        $this->alert('success','Import completed successfully!');
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
