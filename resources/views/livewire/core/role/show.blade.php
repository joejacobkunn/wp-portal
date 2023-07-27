<div>
    <x-page
            :breadcrumbs="$breadcrumbs"
        >  
        
        <x-slot:title>Role Info</x-slot>

        <x-slot:description>
            View role information here.
        </x-slot>

        <x-slot:content>
            @include('partials.flash') 

            @if($editRole)
                @include('livewire.core.role.partials.form', ['role' => $role, 'button_text' => 'Update Role'])
            @else
                @include('livewire.core.role.partials.view', ['role' => $role])
            @endif
        </x-slot>
            
    </x-page>

    
</div>

