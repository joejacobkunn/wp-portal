<div>
    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:title>Warranty Registration</x-slot>

        <x-slot:content>
            @include('livewire.equipment.warranty.brand-configurator.partials.tabs')

        <div class="card border-light shadow-sm mb-4" style="min-height: 600px;margin-top:23px">
            @if ($this->configured)
            <div class="card-header border-gray-300 mt-4 p-3 mb-4 mb-md-0">
                @if ( $this->showBtn)
                @can('equipment.warranty.manage')
                <button wire:click="create()" class="btn btn-primary btn-lg btn-fab"><i class="fas fa-plus"></i></button>
                @endcan
                @endif
            </div>

            <div class="card-body">
                    <div class="tab-content mt-4" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel"
                            aria-labelledby="home-tab">
                            @if($addRecord)
                                @include('livewire.equipment.warranty.brand-configurator.partials.form', ['button_text' => 'Submit'])
                            @else
                                    <livewire:equipment.warranty.brand-configurator.warranty-table lazy>
                            @endif

                        </div>
                    </div>

            </div>
            @else
            <div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Your account
                has
                not been configured in the Portal to use
                this app. Contact Mark Meister to resolve this</div>
            @endif

        </div>
        </x-slot>
    </x-page>
</div>
