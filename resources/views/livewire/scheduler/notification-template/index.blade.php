<div>

    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:title>Scheduler Notification Templates</x-slot>

        <x-slot:description>
            {{ 'Manage notofication templates here' }}
        </x-slot>

        <x-slot:content>
            <div class="card border-light shadow-sm warranty-tab">
                <div class="card-body">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                <livewire:scheduler.notification-template.table lazy
                                    wire:key="{{ 'template-table'}}">
                        </div>
                    </div>

                </div>
            </div>
        </x-slot>
    </x-page>
</div>
