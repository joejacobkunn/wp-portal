<div>
    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:title>Drivers</x-slot>
        <x-slot:description>Manage Drivers</x-slot>
        <x-slot:content>
            <div class="card border-light shadow-sm warranty-tab">
                <div class="card-body">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                <livewire:scheduler.drivers.table lazy
                                    wire:key="{{ 'drivers-table'}}">
                        </div>
                    </div>

                </div>
            </div>
        </x-slot>
    </x-page>
</div>
