<div class="order-import-modal">
    <x-modal :toggle="$importModal" size="lg" :closeEvent="'closeModel'">
        <x-slot name="title">Import Orders</x-slot>
        <form wire:submit.prevent="submit()">
            <div class="row w-100">
                <div class="col-md-12">
                    <div class="form-group">
                        <x-forms.select label="Order Type"
                            model="orderType"
                            :options="['all' => 'ALL',
                                        'open' => 'Open',
                                        'closed' => 'Closed',
                                        'cancelled' => 'Cancelled',
                                        'quotes' => 'Quotes'
                                    ]"
                            :selected="$orderType"
                            hasAssociativeIndex
                            default-option-label="- None -"
                            :key="'import-' . now()" />
                    </div>
                </div>
                <div class="col-md-12 mb-3" x-data="{ uploading: false, progress: 0 }" x-on:livewire-upload-start="uploading = true"
                    x-on:livewire-upload-finish="uploading = false" x-on:livewire-upload-cancel="uploading = false"
                    x-on:livewire-upload-error="uploading = false">
                    <label>Import File</label>
                    <input type="file" id="csv-{{ $importIteration }}" class="form-control" wire:model="importFile">
                    @error('importFile')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <div x-show="uploading" class="mt-2">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="float-start mt-2">
                <button type="submit" class="btn btn-primary">
                    <div wire:loading wire:target="submit">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </div>
                    Import
                </button>
            </div>
        </form>
    </x-modal>
</div>
