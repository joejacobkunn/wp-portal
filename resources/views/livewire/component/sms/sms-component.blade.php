<div>
    <div class="card border-light shadow-sm mb-4">
        <div class="card-content">
            <div class="card-body">
                @if($isLoading)
                    @include('components.grow-spinner')
                @endif
                @if (!empty($alert))
                <div class="alert alert-light-warning color-warning"><p>Please provide a phone number or email address!</p></div>
                @else
                    <div wire:init="$set('isLoading', false)">
                        <livewire:component.sms.messages :phone="$phone" :email="$email" :apiUser="$apiUser" lazy />
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
