<div>
    <div class="card border-light shadow-sm mb-4" style="min-height: 600px">
        <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
            @can('scheduler.truck.manage')
            <button wire:click="create()" class="btn btn-primary btn-lg btn-fab"><i class="fas fa-plus"></i></button>
            @endcan

            <h3 class="h5 mb-0">Truck List #{{$activeWarehouse->title}} </h3>
        </div>

        <div class="card-body">
            <livewire:scheduler.truck.table :whseId="$activeWarehouse->id" :key="'trucks-'.$activeWarehouse->id"/>
        </div>
    </div>
</div>
