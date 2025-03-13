<?php

namespace App\Http\Livewire\Equipment\FloorModelInventory\Notes;

use App\Http\Livewire\Component\Component;
use App\Models\Core\Warehouse;
use App\Models\Equipment\FloorModelInventory\FloorModelInventoryNote;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class Index extends Component
{
    use LivewireAlert, WithPagination;

    public ?FloorModelInventoryNote $editInventoryNote;
    public $note;
    public $warehouse_short;

    public $addRecord = false;
    public $showUpdateModal = false;
    public $showDeleteModal = false;
    public $paginationLimit = 10;
    public $warehouses;
    public $userWarehouseShort = '';

    #[Url('warehouse')]
    public $filter_warehouse = '';

    #[Url('search')]
    public $searchText = '';

    #[Url('order-by')]
    public $orderBy = 'latest';

    protected $rules = [
        'note' => 'required|min:3',
        'warehouse_short' => 'required'
    ];

    protected $validationAttributes = [
            'warehouse_short' => 'warehouse',
    ];

    public function mount()
    {
        $this->dispatch('floorModelInventory:updateSubHeading', 'Note List');
        $this->warehouses = Warehouse::select('id','title','short')
            ->where('cono',10)
            ->orderBy('title', 'asc')
            ->get();

        $userLocation = auth()->user()?->office_location;
        $this->userWarehouseShort = $userLocation
            ? $this->warehouses->firstWhere('title', $userLocation)?->short
            : null;
    }

    public function render()
    {
        return $this->renderView('livewire.equipment.floor-model-inventory.notes.index', [
            'notes' => $this->notes
        ]);
    }

    #[On('filter_warehouse:changed')]
    public function filterWarehouseChanged($name, $value, $recheckValidation = true)
    {
        $this->fieldUpdated($name, $value, $recheckValidation);
        $this->resetPage();
    }

    public function getNotesProperty()
    {
        return FloorModelInventoryNote::with(['user:id,name,abbreviation,email', 'warehouse:id,short,title'])
            ->when($this->filter_warehouse, fn ($query) =>
                $query->where('warehouse_short', $this->filter_warehouse)
            )
            ->when($this->searchText, fn ($query) =>
                $query->where('note', 'like', '%' . $this->searchText . '%')
            )
            ->when($this->orderBy === 'oldest', fn ($q) => $q->oldest())
            ->when($this->orderBy !== 'oldest', fn ($q) => $q->latest())
            ->paginate($this->paginationLimit);
    }

    public function updatedSearchText()
    {
        $this->resetPage();
    }

    private function resetForm()
    {
        $this->reset(['editInventoryNote', 'note', 'warehouse_short']);
        $this->resetValidation();
    }

    public function create()
    {
        $this->authorize('store', FloorModelInventoryNote::class);
        $this->resetForm();
        $this->dispatch('floorModelInventory:updateSubHeading', 'Add New Note');
        $this->warehouse_short = $this->userWarehouseShort;
        $this->addRecord = true;
    }

    public function store()
    {
        $validated = $this->validate();

        FloorModelInventoryNote::create([
            'note' => $validated['note'],
            'warehouse_short' => $validated['warehouse_short'],
            'user_id' => auth()->id()
        ]);

        $this->alert('success','Inventory Note Created');
        $this->cancel();
    }

    public function cancel()
    {
        $this->reset(['addRecord']);
        $this->dispatch('floorModelInventory:updateSubHeading', 'Note List');
    }

    public function edit(FloorModelInventoryNote $note)
    {
        $this->resetForm();
        $this->editInventoryNote = $note;
        $this->note = $note->note;
        $this->warehouse_short = $note->warehouse_short;
        $this->showUpdateModal = true;
    }

    public function update()
    {
        $this->authorize('update', $this->editInventoryNote);

        $validated = $this->validate();
        $this->editInventoryNote->note = $validated['note'];
        $this->editInventoryNote->warehouse_short = $validated['warehouse_short'];
        $this->editInventoryNote->save();

        $this->alert('success','Inventory Note Updated');
        $this->cancelUpdate();
    }

    public function cancelUpdate()
    {
        $this->reset(['showUpdateModal']);
    }

    public function delete(FloorModelInventoryNote $note)
    {
        $this->authorize('delete', $note);
        $this->editInventoryNote = $note;
        $this->showDeleteModal = true;
    }

    public function destroy()
    {
        $this->authorize('delete', $this->editInventoryNote);

        $this->editInventoryNote->delete();
        $this->alert('success','Inventory Note Deleted');
        $this->cancelDelete();
    }

    public function cancelDelete()
    {
        $this->reset(['editInventoryNote', 'showDeleteModal']);
    }
}
