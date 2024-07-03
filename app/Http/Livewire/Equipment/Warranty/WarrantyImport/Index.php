<?php
namespace App\Http\Livewire\Equipment\Warranty\WarrantyImport;

use App\Models\Equipment\Warranty\BrandConfigurator\BrandWarranty;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;

    public $csvFile;
    public $brands;
    public $csvErrorCount;
    public $importIteration=0;
    public $rows = [];
    protected $rules = [
        'csvFile' => 'required|file|mimes:csv'
    ];
    protected $validationAttributes = [
        'csvFile' => 'File'
    ];

    public function mount()
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

    public function render()
    {
        return view('livewire.equipment.warranty.warranty-import.index');
    }

    public function updatedCsvFile()
    {
        $this->validateOnly('csvFile');

        if (($handle = fopen($this->csvFile->getRealPath(), 'r')) !== false) {
            $this->rows = [];
            $matchFound =0;
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $status =null;
                if (in_array(strtolower($data[0]), $this->brands)) {
                    $status=true;
                    $matchFound++ ;
                }
                $this->rows[] = ['data'=>$data,'status'=>$status];
            }
            fclose($handle);
            $this->csvErrorCount = Count($this->rows) - $matchFound -1 ; //-1 is used to remove the header row
        }
    }

    public function cancel()
    {
        $this->csvFile = null;
        $this->rows = [];
        $this->importIteration++;
    }
}
