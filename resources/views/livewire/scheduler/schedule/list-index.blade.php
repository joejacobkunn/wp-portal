<x-page :breadcrumbs="$breadcrumbs">
    <x-slot:title>Schedule</x-slot>
    <x-slot:content>
        <ul class="nav nav-pills mb-2">
            <li class="nav-item">
                <a class="nav-link" aria-current="page" href="{{ route('schedule.calendar.index') }}" wire:navigate><i class="far fa-calendar-alt"></i>
                    Calendar View</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="javascript:;"><i class="fas fa-list"></i> List View</a>
            </li>
        </ul>
        <div class="row">
            <div class="col-12">
                <div class="card border-light shadow-sm schedule-tab">
                    <div class="card-body">
                        <div class="float-end">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">{{ $this->activeWarehouse?->title }}</button>
                            <ul class="dropdown-menu" style="">
                                @foreach ($this->warehouses as $whse)
                                <li><a class="dropdown-item" href="javascript:;" wire:click="changeWarehouse('{{ $whse->id }}')">{{ $whse->title }}</a></li>
                                @endforeach
                            </ul>
                        </div>

                        <x-tabs
                            tabId="schedule-list-index-tabs"
                            class="mb-5"
                            :key="'wh'. $activeWarehouseId">
                            <x-slot:content
                                component="scheduler.schedule.table"
                                :whse="$activeWarehouseId"
                                :activeTab="$tabs['schedule-list-index-tabs']['active']">
                            </x-slot>

                        </x-tabs>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>
</x-page>
