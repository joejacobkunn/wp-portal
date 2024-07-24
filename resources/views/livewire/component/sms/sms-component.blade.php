<div>
    <div class="card border-light shadow-sm mb-4">
        <div class="card-content">
            <div class="card-body">
                @if($isLoading)
                    @include('components.grow-spinner', ['message' => 'loading...'])
                @endif
                    @if (!empty($alert['status']))
                        <div class="alert alert-light-warning color-warning"><p><i class="fas fa-exclamation-circle"></i>  {{ $alert['message'] }}</p></div>
                    @else

                        <div wire:loading.remove wire:init="$set('isLoading', false)">
                            <livewire:component.sms.messages :phone="$phone" :email="$email" :apiUser="$apiUser" lazy />
                        </div>
                    @endif
            </div>
        </div>
    </div>
</div>
