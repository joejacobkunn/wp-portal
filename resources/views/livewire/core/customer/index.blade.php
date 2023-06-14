<div>

    <x-page :breadcrumbs="$breadcrumbs">

        <script src="https://cdnjs.cloudflare.com/ajax/libs/mark.js/8.11.1/mark.min.js"
            integrity="sha512-5CYOlHXGh6QpOFA/TeTylKLWfB3ftPsde7AnmhuitiTX4K5SqCLBeKro6sPS8ilsz1Q4NRx3v8Ko2IBiszzdww=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>


        <x-slot:content>

            @if($addRecord)
            @include('livewire.vehicle.vehicle.partials.form', ['button_text' => 'Add Vehicle'])
            @else
            <div>
                <div class="card border-light shadow-sm mb-4" style="min-height: 600px">
                    <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">

                        @can('vehicle.manage')
                        <button wire:click="create()" class="btn btn-success btn-lg btn-fab"><i
                                class="fas fa-plus"></i></button>
                        @endcan

                        <h3 class="h5 mb-0">Customer List for {{$account->name}}</h3>
                    </div>

                    <div class="card-body">
                        <livewire:core.customer.table :account="$account" />
                    </div>
                </div>
            </div>

            @endif
            @include('livewire.core.customer.partials.open-order-modal')

            </x-slot>

    </x-page>

    @push('scripts')
    <script>
        var instance = new Mark(document.querySelector(".table"));
instance.mark("fairway", {
"element": "span",
"className": "highlight"
});
    </script>
    @endpush


</div>