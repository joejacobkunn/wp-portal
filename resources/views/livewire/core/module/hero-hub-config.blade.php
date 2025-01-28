<div>
    @unless($is_configured)
        <span wire:click="$toggle('show')" class="badge bg-light-warning">Click here to configure HeroHub</span>
    @else
        <span class="badge bg-light-success">Configured</span>
    @endunless

    <x-modal toggle="show">
        <x-slot name="title">
            <div class="">Configure Herohub for {{$account->name}}</div>
        </x-slot>
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-light-info color-info">
                      Please refer to HeroHub documentation for credentials. Your key will be encrypted on our end
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-3">
                <x-forms.input
                    label="Client ID"
                    model="client_id"
                    lazy
                />
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-3">
                <x-forms.input
                    label="Client Key"
                    model="client_key"
                    lazy
                />
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-3">
                <x-forms.input
                    label="Organization GUID"
                    model="organization_guid"
                    lazy
                />
            </div>
        </div>


    
        <x-slot name="footer">
            <button wire:click="configure()" type="button" class="btn btn-success">
                <div wire:loading wire:target="configure">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                </div>
                Configure
            </button>
        </x-slot>
    
    </x-modal>
</div>
