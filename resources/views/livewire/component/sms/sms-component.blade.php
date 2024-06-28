<div>
    <style>
        .loader {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3498db;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
    </style>
    <div class="card border-light shadow-sm mb-4">
        <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
            <h4 class="card-title">{{$alert? '': 'Your chat with arun'}}</h4>
        </div>
        <div class="card-content">
            <div class="card-body">
                @if($isLoading)
                    <div class="loader"></div>
                @endif
                @if (!empty($alert))
                <div class="alert alert-light-warning color-warning"><p>Please provide a phone number or email address!</p></div>
                @else
                    <div wire:init="$set('isLoading', false)">
                        <livewire:component.sms.messages :phone="$phone" :email="$email" lazy />
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
