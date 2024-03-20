<div>
    <div class="card border-light shadow-sm mb-4" style="min-height: 600px">
        <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
            <button wire:click="create()" class="btn btn-primary btn-lg btn-fab"><i class="fas fa-plus"></i></button>
            <h3 class="h5 mb-0">Notification Templates</h3>
        </div>

        <div class="card-body">
            <livewire:order.notification-template.table lazy>
        </div>
    </div>
</div>
