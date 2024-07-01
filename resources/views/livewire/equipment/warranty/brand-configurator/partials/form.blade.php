
<div class="row">
    <div class="col-12 col-md-12">
        <div class="card card-body shadow-sm mb-4">
            <form wire:submit.prevent="submit()">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="form-group">
                            <x-forms.select
                                label="Brand"
                                model="brandId"
                                :options="$brands"
                                :selected="$brandId"
                                hasAssociativeIndex
                                default-option-label="- None -"
                                label-index="name"
                                value-index="id"  />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input
                            label="Brand Prefix"
                            model="prefix"
                            lazy />

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input
                            label="Alt Names"
                            model="altName"
                            lazy />
                    </div>
                </div>
                <hr>

                <div class="mt-2 float-start">

                    <button type="submit" class="btn btn-primary">
                        <div wire:loading wire:target="submit">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>
                        {{$button_text}}
                    </button>

                    <button type="button" wire:click="cancel" class="btn btn-light-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
