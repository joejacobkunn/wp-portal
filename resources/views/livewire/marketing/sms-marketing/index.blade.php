<div>
    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:title>SMS Marketing</x-slot>
        <x-slot:description>sms marketing imported records</x-slot>
        <x-slot:content>
            <div class="card border-light shadow-sm">
                <div class="card-header border-gray-300 mt-4 p-3 mb-md-0">
                    @if (!$addRecord)
                            @can('marketing.sms-manage')
                                <button wire:click="create" class="btn btn-primary btn-lg btn-fab"><i class="fas fa-plus"></i></button>
                            @endcan
                    @endif
                </div>
                <div class="card-body">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <div class="col-md-12 mb-3 text-center" wire:loading wire:target="importData">
                                <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
                                <span>Please wait, processing records...</span>
                                <span class="sr-only">Loading...</span>
                            </div>
                            <div wire:loading.remove wire:target="importData">
                                @if($addRecord)
                                    @include('livewire.marketing.sms-marketing.partials.form', ['button_text' => 'Import'])
                                @else
                                    <div wire:poll.5s="refreshStatus">
                                        <livewire:marketing.s-m-s-marketing.table wire:key="imt-{{ $smsImportTableId }}" lazy>
                                    </div>
                                 @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-slot>
    </x-page>
</div>
