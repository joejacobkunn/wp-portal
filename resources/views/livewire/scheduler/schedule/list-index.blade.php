<x-page :breadcrumbs="$breadcrumbs">
    <x-slot:title>Schedule</x-slot>
    <x-slot:content>
        <ul class="nav nav-pills mb-2">
            <li class="nav-item">
                <a class="nav-link" aria-current="page" href="{{ route('schedule.calendar.index', ['whse' => $this->activeWarehouse->id]) }}" wire:navigate><i
                        class="far fa-calendar-alt"></i>
                    Calendar View</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="javascript:;"><i class="fas fa-list"></i> List View</a>
            </li>
        </ul>
        <div class="row" wire:poll.60s>
            <div class="col-12">
                <div class="card border-light shadow-sm schedule-tab">
                    <div class="card-body">
                        <div class="float-end">
                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                {{ $this->activeWarehouse?->title }}
                            </button>
                            <ul class="dropdown-menu" style="">
                                @foreach ($this->warehouses as $whse)
                                    <li><a class="dropdown-item" href="javascript:;"
                                            wire:click="changeWarehouse('{{ $whse->id }}')">{{ $whse->title }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <x-tabs tabId="schedule-list-index-tabs" class="mb-5" :key="'wh' . $activeWarehouseId.uniqid()">

                            <x-slot:tab_header_today>Today <span
                                    class="badge badge-lg {{ $tabs['schedule-list-index-tabs']['active'] == 'today' ? 'text-primary bg-white ml-2' : 'bg-primary' }} ms-2 ml-2">{{ $tabCounts['today'] ?? '0' }}</span></x-slot>
                            <x-slot:tab_header_tomorrow>Tomorrow <span
                                    class="badge badge-lg {{ $tabs['schedule-list-index-tabs']['active'] == 'tomorrow' ? 'text-primary bg-white ml-2' : 'bg-primary' }} ms-2 ml-2">{{ $tabCounts['tomorrow'] ?? '0' }}</span></x-slot>
                            <x-slot:tab_header_unconfirmed>Unconfirmed <span
                                    class="badge badge-lg {{ $tabs['schedule-list-index-tabs']['active'] == 'unconfirmed' ? 'text-primary bg-white ml-2' : 'bg-primary' }} ms-2 ml-2">{{ $tabCounts['unconfirmed'] ?? '0' }}</span></x-slot>
                            <x-slot:tab_header_all>All <span
                                    class="badge badge-lg {{ $tabs['schedule-list-index-tabs']['active'] == 'all' ? 'text-primary bg-white ml-2' : 'bg-primary' }} ms-2 ml-2">{{ $tabCounts['all'] ?? '0' }}</span></x-slot>

                            <x-slot:content component="scheduler.schedule.table" :whse="$this->activeWarehouse->short"
                                :activeTab="$tabs['schedule-list-index-tabs']['active']">
                            </x-slot>

                        </x-tabs>
                    </div>
                </div>
            </div>
        </div>
        @if ($showEventModal)
            <x-modal toggle="showEventModal" size="xl" :closeEvent="'closeEventModal'">
                <x-slot name="title">Schedule
                    {{ App\Enums\Scheduler\ScheduleEnum::tryFrom($selectedSchedule->type)->label() }}</x-slot>

                <livewire:scheduler.schedule.schedule-order lazy wire:key="schedule-order"
                :page="true"
                :selectedType="$selectedSchedule->type"
                :selectedSchedule="$selectedSchedule"
                :activeWarehouse="$this->activeWarehouse"
                >
            </x-modal>
        @endif
    </x-slot>
</x-page>
