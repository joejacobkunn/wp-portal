<?php
namespace App\Http\Livewire\Marketing\SMSMarketing\Traits;

use App\Exports\SMSMarketingExport;
use App\Imports\SMSMarketingImport;
use App\Jobs\ProcessSMSMarketing;
use App\Models\Marketing\SMSMarketing;
use App\Services\Kenect;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

trait FormRequest
{
    public $importErrorRows = [];
    public $validatedRows = [];
    public $teamLocationId = '18771';

    public function dataImport()
    {
        try {
            $import = new SMSMarketingImport();
            Excel::import($import, $this->importFile);
        } catch (\Exception $e) {
            $this->addError('importFile', $e->getMessage());
        }

        $this->validatedRows = $import->getData();
        $failures = $import->failures();
        if (count($failures) > 0) {
            foreach ($failures as $failure) {
                $this->importErrorRows[$failure->row()] = $failure->values();
            }
        }
    }

    public function downloadEntires()
    {
        return Excel::download(new SMSMarketingExport($this->importErrorRows), 'invalid-rows'.now().'.csv');
    }

    public function saveData()
    {
        $extension = $this->importFile->getClientOriginalExtension();
        $uploadedFileName = uniqid() . '.' . $extension;
        $uploadDirectory =  config('marketing.sms.upload_location');
        $validPath = config('marketing.sms.valid_file_location') . uniqid() . '.csv';
        $kenect = new Kenect();

        try {
            $filePath = $this->importFile->storeAs($uploadDirectory, $uploadedFileName, 'public');

        } catch (\Exception $e) {
            $this->dispatch('showError', 'Error saving uploaded file');
            return;
        }

        $locations = json_decode($kenect->locations());
        if(empty($locations)) {
            $this->dispatch('showError', 'Failed to fetch Locations');
            return;
        }

        $teams = $kenect->teams($this->teamLocationId);

        $data =  SMSMarketing::create([
            'name' => $this->name,
            'file' => $filePath,
            'failed_file' =>  null,
            'processed' =>  $validPath,
            'processed_count' =>  0,
            'total_count' => count($this->validatedRows) + count($this->importErrorRows),
            'created_by' => Auth::user()->id,
            'status' => 'queued'
        ]);

        ProcessSMSMarketing::dispatch($this->validatedRows, $data, $this->importErrorRows, $locations, json_decode($teams));
        return redirect()->route('marketing.sms-marketing.index');
    }
}
