<?php

namespace App\Http\Livewire\Scheduler\Truck;

use App\Models\Scheduler\Truck;
use App\Http\Livewire\Component\Component;
use App\Models\Scheduler\Zones;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Rotation extends Component
{
    use AuthorizesRequests, LivewireAlert;
    
    public Truck $truck;

    public $rotations = [];
    public $rotationData = [];

    public $serviceTypes = [
        'ahm' => 'AHM',
        'delivery-pickup' => 'Delivery / Pickup'
    ];

    public $serviceType;

    public $baselineDate;

    public $zones = [];

    public $selectedZone;

    public $editRotation = false;

    public function mount()
    {
        $this->authorize('view', $this->truck);

        $this->zones = Zones::select('id', 'name')
            ->where('whse_id', $this->truck->whse)
            ->pluck('name', 'id')
            ->toArray();

        $this->initRotationData();
    }

    public function initRotationData()
    {
        $rotations = $this->truck
            ->rotations()
            ->select('truck_id', 'zone_id', 'sort_order')
            ->with('zone:id,name')
            ->orderBy('sort_order')
            ->get();

        $this->rotationData = [];
        foreach ($rotations as $rotation) {
            $this->rotationData[uniqid()] = $rotation->zone_id;
        }
        
        $this->rotations = $rotations->toArray();
    }

    public function render()
    {
        return $this->renderView('livewire.scheduler.truck.rotation');
    }

    public function editRotationAction()
    {
        $this->editRotation = true;
        $this->baselineDate = !empty($this->truck->baseline_date) ? Carbon::parse($this->truck->baseline_date)->toDateString(): '';
        $this->serviceType = $this->truck->service_type;

        if (empty($this->truck->baseline_date) || empty($this->rotations)) {
            $this->addRotationItem();
        }
    }

    public function addRotationItem()
    {
        $this->rotationData[uniqid()] = '';
        $this->dispatch($this->id() . ':zones-updated');
    }
    
    public function removeRotationItem($index)
    {
        unset($this->rotationData[$index]);
    }

    public function sortRotationItems($order)
    {
        $this->rotationData = collect($order)
            ->mapWithKeys(function ($key) {
                return array_key_exists($key, $this->rotationData) ? [$key => $this->rotationData[$key]] : [];
            })
            ->toArray();
    }

    public function saveRotationData()
    {
        $this->validate([
            'baselineDate' => 'required',
            'serviceType' => 'required',
            'rotationData.*' => 'required',
        ], [
            'rotationData.*.required' => 'The Zone field is required.',
        ]);

        $this->truck->fill([
            'service_type' => $this->serviceType,
            'baseline_date' => $this->baselineDate,
        ]);

        $baselineUpdated = $this->truck->isDirty('baseline_date');

        $this->truck->save();

        $this->truck->rotations()->delete();
        $sortOrder = 1;
        foreach ($this->rotationData as $zoneId) {
            $this->truck->rotations()->create([
                'zone_id' => $zoneId,
                'sort_order' => $sortOrder,
            ]);

            $sortOrder++;
        }

        if ($baselineUpdated) {
            //@TODO regenerate rotations
        }

        $this->rotations = $this->truck
            ->rotations()
            ->select('truck_id', 'zone_id', 'sort_order')
            ->with('zone:id,name')
            ->orderBy('sort_order')
            ->get()
            ->toArray();
        $this->editRotation = false;

        $this->alert('success', 'Updated rotation info.');
    }

    public function cancelRotationData()
    {
        $this->initRotationData();
        $this->editRotation = false;
    }
}
