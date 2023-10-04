<div>

    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:content>

            <div>
                <div class="card border-light shadow-sm mb-4" style="min-height: 600px">
                    <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
                        @if(!$addReport)
                        <button wire:click="create" class="btn btn-sm btn-outline-primary float-end"><i
                                class="fa-solid fa-plus"></i> New
                            Report</button>
                        @endif
                        <h3 class="h5 mb-0">{{ !$addReport ? 'Manage Reports here' : 'Create a New Report here' }}</h3>
                    </div>

                    <div class="card-body">
                        @if($addReport)
                        @include('livewire.reporting.partials.form', ['button_text' => 'Add Report'])
                        @else
                        @include('livewire.reporting.partials.listing')
                        @endif

                    </div>
                </div>
            </div>

            </x-slot>

    </x-page>

</div>