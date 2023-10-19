<div class="row">
    <div class="col-12 col-md-12">
        <div class="card card-body shadow-sm mb-4">
            <form wire:submit.prevent="submit">

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input label="Dashboard Name" model="dashboard.name" lazy />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="form-group">
                            <x-forms.select multiple label="Reports" model="dashboard.reports" :options="$reports"
                                :selected="$dashboard->reports ?? ''" default-selectable default-option-label="- None -" label-index="name"
                                value-index="id" />
                        </div>
                    </div>
                </div>



                <div class="mt-2">
                    <button type="submit" class="btn btn-success">
                        <div wire:loading wire:target="submit">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>

                        {{ $button_text }}

                    </button>
                    <button type="button" wire:click="cancel" class="btn btn-gray-200">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
