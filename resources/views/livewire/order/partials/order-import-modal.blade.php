<div class="order-export-modal">
    <x-modal :toggle="$exportModal" size="lg" :closeEvent="'closeModel'">
        <x-slot name="title">Export Orders</x-slot>
        <form wire:submit.prevent="submit()">
            <div class="row w-100">
                <div class="col-md-12">
                    <div class="form-group">
                        <x-forms.select label="Order Type"
                            model="orderType"
                            :options="[
                                        'open' => 'Open Orders',
                                        'closed' => 'Closed Orders'
                                    ]"
                            :selected="$orderType"
                            hasAssociativeIndex
                            default-option-label="- None -"
                            :key="'export-' . now()" />
                    </div>
                </div>
            </div>
            <div class="float-start mt-2">
                <button type="submit" class="btn btn-primary">
                    <div wire:loading wire:target="submit">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </div>
                    Export
                </button>
            </div>
        </form>
    </x-modal>
</div>
