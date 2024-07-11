<div class="card border-light shadow-sm warranty-tab">
    <div class="card-header border-gray-300 mt-4 p-3 mb-md-0">
        @if (!$addRecord)
                <button wire:click="create()" class="btn btn-primary btn-lg btn-fab"><i class="fas fa-plus"></i></button>
        @endif
    </div>
    <div class="card-body">
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                <div class="col-md-12 mb-3 text-center" wire:loading wire:target="importData">
                    <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
                    <span>Please wait, processing records...</span>
                    <span class="sr-only">Loading...</span>
                </div>
                @if($showalert['status'])
                @include('partials.alert',[
                    'class' =>$showalert['class'],
                    'message' =>$showalert['message'],
                    ])
                @endif
                <div wire:loading.remove>
                    @switch($page)
                        @case('viewData')
                            <livewire:equipment.warranty.warranty-import.table lazy>
                            @break
                        @case('form')
                            @include('livewire.equipment.warranty.warranty-import.partials.form', [
                                'button_text' => 'Import',
                            ])
                        @break
                        @case('success')
                            @include('livewire.equipment.warranty.warranty-import.partials.success', ['records' => $validatedRows])
                        @break

                    @endswitch
                </div>
            </div>
        </div>
    </div>
</div>
