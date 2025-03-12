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

    public function cancel()
    {
        $this->addRecord = false;
        $this->resetValidation();
        $this->reset(['note']);
        $this->dispatch('floorModelInventory:updateSubHeading', 'Note List');
    }

    public function submit()
    {
        $validated = $this->validate();

        FloorModelInventoryNote::create([
            'note' => $validated['note'],
            'user_id' => auth()->id()
        ]);

        $this->alert('success','Inventory Note Created');
        $this->cancel();
    }

    public function editNote(FloorModelInventoryNote $note)
    {
        $this->editInventoryNote = $note;
        $this->note = $note->note;
        $this->showUpdateModel = true;
    }

    public function update()
    {
        $this->authorize('update', $this->editInventoryNote);

        $this->editInventoryNote->note = $this->note;
        $this->editInventoryNote->save();

        $this->alert('success','Inventory Note Updated');
        $this->closeUpdate();
    }

    public function closeUpdate()
    {
        $this->reset(['editInventoryNote']);
        $this->showUpdateModel = false;
    }

    public function deleteNote(FloorModelInventoryNote $note)
    {
        $this->editInventoryNote = $note;
        $this->showDeleteModel = true;
    }

    public function confirmDelete()
    {
        $this->authorize('delete', $this->editInventoryNote);

        $this->editInventoryNote->delete();
        $this->alert('success','Inventory Note Deleted');
        $this->closeDelete();
    }

    public function closeDelete()
    {
        $this->reset(['editInventoryNote']);
        $this->showDeleteModel = false;
    }
}
