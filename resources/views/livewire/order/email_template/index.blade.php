<div>

    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:title>Orders Notification Templates</x-slot>

        <x-slot:description>
            {{ !$addRecord ? 'Manage notification templates here' : 'Create a new notification template here' }}
        </x-slot>

        <x-slot:content>

            @if ($addRecord)
                @include('livewire.order.email_template.partials.form', ['button_text' => 'Add Template'])
            @else
                @include('livewire.order.email_template.partials.listing')
            @endif
        </x-slot>
    </x-page>
</div>
