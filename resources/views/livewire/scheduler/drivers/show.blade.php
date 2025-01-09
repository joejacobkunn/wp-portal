<div>
    <x-page :breadcrumbs="$breadcrumbs">
        <x-slot:title>Drivers</x-slot>
        <x-slot:description>{{ $editRecord ? ' Edit Staff details' : 'View Staff Details' }}</x-slot>
        <x-slot:content>
            @if ($editRecord)
            <div class="card border-light shadow-sm zones-tab">
                <div class="card-body">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                @include('livewire.scheduler.drivers.partials.form', [
                                    'button_text' => 'Update',
                                ])
                        </div>
                    </div>

                </div>
            </div>
            @else
                @include('livewire.scheduler.drivers.partials.view')
            @endif
        </x-slot>
    </x-page>
</div>
