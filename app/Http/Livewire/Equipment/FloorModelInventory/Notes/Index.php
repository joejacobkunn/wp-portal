<?php

namespace App\Http\Livewire\Equipment\FloorModelInventory\Notes;

use App\Http\Livewire\Component\Component;
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

    #[Validate('required|min:3')]
    public $note;

    #[Url()]
    public $searchText = '';

    public function mount()
    {
        $this->dispatch('floorModelInventory:updateSubHeading', 'Note List');
    }

    public function render()
    {
        return $this->renderView('livewire.equipment.floor-model-inventory.notes.index', [
            'notes' => $this->notes
        ]);
    }

    public function getNotesProperty()
    {
        return FloorModelInventoryNote::with('user:id,name,abbreviation,email')
            ->latest()
            ->when($this->searchText, fn ($query) =>
                $query->where('note', 'like', '%' . $this->searchText . '%')
            )
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
        $this->addRecord = true;
        $this->resetValidation();
    }

    public function store()
    {
        $validated = $this->validate();

        FloorModelInventoryNote::create([
            'note' => $validated['note'],
            'user_id' => auth()->id()
        ]);

        $this->alert('success','Inventory Note Created');
        $this->cancel();
    }

    public function cancel()
    {
        $this->reset(['note', 'addRecord']);
        $this->resetValidation();
        $this->dispatch('floorModelInventory:updateSubHeading', 'Note List');
    }

    public function edit(FloorModelInventoryNote $note)
    {
        $this->editInventoryNote = $note;
        $this->note = $note->note;
        $this->showUpdateModel = true;
    }

    public function update()
    {
        $this->authorize('update', $this->editInventoryNote);

        $validated = $this->validateOnly('note');
        $this->editInventoryNote->note = $validated['note'];
        $this->editInventoryNote->save();

        $this->alert('success','Inventory Note Updated');
        $this->cancelUpdate();
    }

    public function cancelUpdate()
    {
        $this->reset(['editInventoryNote']);
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
