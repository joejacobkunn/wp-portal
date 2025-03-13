<?php

namespace App\Http\Livewire\Equipment\FloorModelInventory\Notes;

use App\Http\Livewire\Component\Component;
use App\Models\Core\Warehouse;
use App\Models\Equipment\FloorModelInventory\FloorModelInventoryNote;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\WithPagination;

class Index extends Component
{
    use LivewireAlert, WithPagination;

    public $addRecord = false;
    public $showUpdateModel = false;
    public $showDeleteModel = false;
    public $paginationLimit = 10;

    public ?FloorModelInventoryNote $editInventoryNote;
    public $warehouses;
    public $userWarehouseShort = '';


    #[Validate('required|min:3', 'note')]
    public $note;

    #[Validate('required', 'warehouse')]
    public $warehouse_short;

    #[Url('warehouse')]
    public $filter_warehouse = '';

    #[Url('search')]
    public $searchText = '';

    #[Url('order-by')]
    public $orderBy = 'latest';

    public function mount()
    {
        $this->dispatch('floorModelInventory:updateSubHeading', 'Note List');
        $this->warehouses = Warehouse::select('id','title','short')
            ->where('cono',10)
            ->orderBy('title', 'asc')
            ->get();

        $this->userWarehouseShort = $this->warehouses->firstWhere('title', auth()->user()->office_location)?->short;
    }

    public function render()
    {
        return $this->renderView('livewire.equipment.floor-model-inventory.notes.index', [
            'notes' => $this->notes
        ]);
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
            ->when(true, function ($q) {
                return $this->orderBy === 'oldest'
                    ? $q->oldest()
                    : $q->latest();
            })
            ->paginate($this->paginationLimit);
    }

    public function updatedSearchText()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->dispatch('floorModelInventory:updateSubHeading', 'Add New Note');
        $this->authorize('store', FloorModelInventoryNote::class);
        $this->warehouse_short = $this->userWarehouseShort;
        $this->addRecord = true;
        $this->resetValidation();
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
        $this->reset(['note', 'addRecord', 'warehouse_short']);
        $this->resetValidation();
        $this->dispatch('floorModelInventory:updateSubHeading', 'Note List');
    }

    public function edit(FloorModelInventoryNote $note)
    {
        $this->editInventoryNote = $note;
        $this->note = $note->note;
        $this->warehouse_short = $note->warehouse_short;
        $this->showUpdateModel = true;
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
        $this->reset(['editInventoryNote', 'note', 'warehouse_short']);
        $this->showUpdateModel = false;
    }

    public function delete(FloorModelInventoryNote $note)
    {
        $this->authorize('delete', $note);
        $this->editInventoryNote = $note;
        $this->showDeleteModel = true;
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
        $this->reset(['editInventoryNote']);
        $this->showDeleteModel = false;
    }
}
