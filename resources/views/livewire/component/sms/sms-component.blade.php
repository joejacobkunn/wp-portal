<div>
    <div class="card border-light shadow-sm mb-4">
        <div class="card-content">
            <div class="card-body">
                    @if (!empty($alert['status']))
                        <div class="alert alert-light-warning color-warning"><p>{{ $alert['message'] }}</p></div>
                    @else
                        <div wire:loading>
                            @include('components.grow-spinner')
                        </div>
                        <div wire:loading.remove>
                            <livewire:component.sms.messages :phone="$phone" :email="$email" :apiUser="$apiUser" lazy />
                        </div>
                    @endif
            </div>
        </div>
    </div>
</div>
