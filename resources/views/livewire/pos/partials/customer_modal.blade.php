<x-modal toggle="customerSearchModal" :size="'xl'" :closeEvent="'closeCustomerSearch'">
    <x-slot name="title">
        <div>Search Customer: {{ $lastCustomerQuery }}</div>
    </x-slot>

    <p>Select Customer for billing</p>

    <div class="customer-outer-div">
        @foreach ($customerResult as $customer)
            <div class="customer-ind-div {{ $customer['id'] == $customerResultSelected['id'] ? 'selected' : '' }}"
                wire:click="selectCustomer('{{ $customer['id'] }}')">
                <div class="selected-label"><i class="fa fa-check-circle"></i></div>
                <label>
                    @if ($customer['is_active'])
                        <span class="text-success">{{ $customer['name'] }}</span>
                    @else
                        <span class="text-danger">{{ $customer['name'] }}</span>
                    @endif
                </label>
                <p class="mb-0"><strong>SX #</strong> :
                    {{ $customer['sx_customer_number'] }}
                </p>
                @if (!empty($customer['full_address']))
                    <p class="mb-0"><strong>Address:</strong>
                        {{ $customer['full_address'] }}
                    </p>
                @endif
                @if (!empty($customer['email']))
                    <p class="mb-0"><strong>Email:</strong> {{ $customer['email'] }}</p>
                @endif
                @if (!empty($customer['phone']))
                    <p class="mb-0"><strong>Phone:</strong>
                        {{ format_phone($customer['phone']) }}</p>
                @endif
                <p class="mb-0"><strong>Last Activity:</strong>
                    {{ \Carbon\Carbon::parse($customer['updated_at'])->diffForHumans() }}</p>
            </div>
        @endforeach
    </div>

    <div class="text-center">
        <button class="btn btn-primary btn-lg my-4 add-cart-btn"
            wire:click="proceedToPayment">
            <div wire:loading wire:target="proceedToPayment">
                <span class="spinner-border spinner-border-sm" role="status"
                    aria-hidden="true"></span>
            </div>

            <div wire:loading.class="d-none" wire:target="proceedToPayment">
                Proceed <i class="fas fa-arrow-circle-right"></i>
            </div>
        </button>
    </div>
</x-modal>