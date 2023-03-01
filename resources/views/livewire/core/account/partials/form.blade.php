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
                    <div class="col-md-6 mb-3">
                        <x-forms.input
                            label="Subdomain"
                            model="account.subdomain"
                            appendText=".{{ config('app.domain') }}"
                            lazy
                        />
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-forms.input
                            label="Admin Email"
                            model="adminEmail"
                            placeholder="Enter Admin Email"
                            prepend-icon="fas fa-envelope"
                            lazy
                        />
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.textarea
                            label="Address"
                            model="account.address"
                            lazy
                        />
                    </div>
                    
                </div>
                
                <div role="separator" class="dropdown-divider my-3"></div>

                <div class="mt-2">
                    <button type="submit" class="btn btn-primary">

                        <div wire:loading wire:target="{{ (!empty($role->id) ? 'save' : 'submit')}}">
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