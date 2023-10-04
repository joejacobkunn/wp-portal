<div class="row">
    <div class="col-12 col-md-12">
        <div class="card card-body shadow-sm mb-4">
            <form wire:submit.prevent="submit">

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input label="Report Name" model="report.name" lazy />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="col-md-12 mb-3">
                            <x-forms.textarea label="Description" model="report.description" rows="3" lazy />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="col-md-12 mb-3">
                            <x-forms.textarea label="Query" model="report.query" rows="10" lazy />
                        </div>
                    </div>
                </div>

                @if(!empty($group_by_options))

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="form-group">
                            <x-forms.select label="Group By" model="report.group_by" :options="$group_by_options"
                                :selected="$report->group_by ?? ''" default-selectable default-option-label="- None -"
                                label-index="label" value-index="name" />
                        </div>
                    </div>
                </div>

                @endif



                <div class="mt-2">
                    <button type="submit" class="btn btn-success">
                        <div wire:loading wire:target="submit">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>

                        {{$button_text}}

                    </button>
                    <button type="button" wire:click="cancel" class="btn btn-gray-200">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>