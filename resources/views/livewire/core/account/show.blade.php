<div>
    <x-page
        :breadcrumbs="$breadcrumbs"
    >  
       
        <x-slot:title>Account Info</x-slot>

        <x-slot:description>View Account details</x-slot>

        <x-slot:content>

            @include('partials.flash') 

            @if($editRecord)
                @include('livewire.core.account.partials.form', ['button_text' => 'Update Account'])
            @else
                @include('livewire.core.account.partials.view')
            @endif
            
        </x-slot>
            
    </x-page>
</div>

