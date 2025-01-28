<div>
    <div class="card border-light shadow-sm zip-tab">
        @if (!$addRecord)
            <div class="card-header border-gray-300 p-3 mb-4">
                <button wire:click="create" class="btn btn-primary btn-lg btn-fab"><i class="fas fa-plus"></i></button>
            </div>
        @endif
        <div class="card-body">
            @if (!$addRecord)
                <div class="alert alert-light-primary color-primary"> View and manage ZIP Codes for
                    <strong>{{ $warehouse->title }}</strong>
                    here
                </div>
            @else
                <div class="alert alert-light-primary color-primary"> Add ZIP Code for
                    <strong>{{ $warehouse->title }}</strong>
                    here
                </div>
            @endif

            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    @if ($addRecord)
                        @include('livewire.scheduler.service-area.zip-code.partials.form', [
                            'button_text' => 'Add ZIP Code',
                        ])
                    @else
                        <livewire:scheduler.service-area.zip-code.table wire:key="'zipcode'" whseId="{{ $warehouseId }}" lazy>
                    @endif

                </div>
            </div>

        </div>
    </div>
</div>
