<div class="row">
    <div class="col-12 col-md-12">
        <div class="card card-body shadow-sm mb-4">
            <form wire:submit.prevent="submit()">

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input label="Name" model="account.name" lazy />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="form-group">
                            <x-forms.select label="SX Account" model="account.sx_company_number" :options="$sx_accounts"
                                :selected="$account->sx_company_number ?? null" default-selectable
                                default-option-label="- None -" label-index="name" value-index="cono" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input label="Subdomain" model="account.subdomain" prependText="http://"
                            appendText=".{{ config('app.domain') }}" lazy />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input label="Admin Email" model="adminEmail" placeholder="Enter Admin Email"
                            prepend-icon="fas fa-envelope" lazy
                            hint="User will be set as Super Admin for account and will be sent an onboarding email to setup password" />
                    </div>
                </div>

                <x-forms.media
                    model="documents"
                    :entity="$account"
                    :collection="$account::DOCUMENT_COLLECTION"
                    editable
                    rules="mimes:jpeg,png,svg"
                />

                <div class="row">
                    <div class="col-md-12 mb-1">
                        <x-forms.checkbox label="Activate Account" model="account.is_active" lazy />
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
