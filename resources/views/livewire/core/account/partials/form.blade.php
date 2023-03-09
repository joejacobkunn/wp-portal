<div class="row">
    <div class="col-12 col-md-12">
        <div class="card card-body shadow-sm mb-4">
            <form wire:submit.prevent="{{ (!empty($role->id) ? 'save()' : 'submit()')}}">

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input
                            label="Name"
                            model="account.name"
                            lazy
                        />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input
                            label="Subdomain"
                            model="account.subdomain"
                            prependText="http://"
                            appendText=".{{ config('app.domain') }}"
                            lazy
                        />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input
                            label="Admin Email"
                            model="adminEmail"
                            placeholder="Enter Admin Email"
                            prepend-icon="fas fa-envelope"
                            lazy
                            hint="User will be set as Super Admin for accountand will be sent an onboarding email to setup password"
                        />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-1">
                        <x-forms.checkbox
                            label="Activate Account"
                            model="account.is_active"
                            lazy
                        />
                    </div>
                </div>
                
                <hr>

                <div class="mt-2 float-end">
                    
                    <button type="submit" class="btn btn-primary">
                        <div wire:loading wire:target="{{ (!empty($role->id) ? 'save' : 'submit')}}">
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